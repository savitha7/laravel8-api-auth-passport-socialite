<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\ApiBaseController as ApiBaseController;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Services\SocialAccountsService;

class AuthController extends ApiBaseController
{
    public const PROVIDERS = ['google'];

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
            'password_confirmation' => 'required|same:password',
        ]);
   
        if($validator->fails()){
            return $this->sendError(self::VALIDATION_ERROR, null, $validator->errors());       
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return $this->respondWithMessage('User register successfully.');
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user();
            $token =  $user->createToken(env('API_AUTH_TOKEN_PASSPORT'))->accessToken;
            return $this->respondWithToken($token);
        } else { 
            return $this->sendError(self::UNAUTHORIZED, null, ['error'=>'Unauthorised']);
        } 
    }

    private function respondWithToken($token) {
        $success['token'] =  $token;
        $success['access_type'] = 'bearer';
        $success['expires_in'] = now()->addDays(15);
   
        return $this->sendResponse($success, 'Login successfully.');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirectToProvider($provider)
    {
        if(!in_array($provider, self::PROVIDERS)){
            return $this->sendError(self::NOT_FOUND);       
        }

        $success['provider_redirect'] = Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();
   
        return $this->sendResponse($success, "Provider '".$provider."' redirect url.");
    }
        
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleProviderCallback($provider)
    {
        if(!in_array($provider, self::PROVIDERS)){
            return $this->sendError(self::NOT_FOUND);       
        }

        try {
            $providerUser = Socialite::driver($provider)->stateless()->user();
            
            if ($providerUser) {
                $user = (new SocialAccountsService())->findOrCreate($providerUser, $provider);

                $token = $user->createToken(env('API_AUTH_TOKEN_PASSPORT_SOCIAL'))->accessToken; 
       
                return $this->respondWithToken($token);
                //return redirect('https://my-frontend-domain.com/dashboard?access_token='.$token);
            }

        } catch (Exception $exception) {
            return $this->sendError(self::UNAUTHORIZED, null, ['error'=>$e->getMessage()]);
        }        
    }
}
