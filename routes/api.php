<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::get('/auth/redirect/{provider}', [AuthController::class, 'redirectToProvider'])->where('provider', '[A-Za-z]+');
Route::get('/auth/{provider}/callback', [AuthController::class, 'handleProviderCallback'])->where('provider', '[A-Za-z]+');

Route::middleware('auth:api')->group( function () {
    Route::get('/auth/check', function () {
	    $response = [
            'success' => true,
            'message' => 'success',
        ];
        return response()->json($response, 200);
	});
});
