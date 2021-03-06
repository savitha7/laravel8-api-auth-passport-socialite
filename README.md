# laravel8-api-auth-passport-socialite

Laravel 8 REST API Authentication with Passport and Socialite  
-------------------------------------------------------------

### Install Laravel
```
$ composer create-project laravel/laravel
```
### Install passport
```
$ composer require laravel/passport
```
### Run the Artisan migrate command:
```bash
$ php artisan migrate
```

#### Create "personal access" and "password grant" clients which will be used to generate access tokens:
```bash
$ php artisan passport:install
```
### Install passport config
```
$ php artisan passport:keys
$ php artisan vendor:publish --tag=passport-config
$ php artisan vendor:publish --tag=passport-migrations
```
### Install socialite
```
$ composer require laravel/socialite
```

#### Auth with Social Account API:
```bash
php artisan make:model SocialAccount -m

php artisan make:migration make_password_and_email_fields_nullable_in_users_table --table=users
```

Create an SocialAccountsService in App\Services\SocialAccountsService

Start the local development server

    php artisan serve

You can now access the server at http://127.0.0.1:8000
    
**Make sure you set the correct database connection information before running the migrations**

    php artisan migrate 
    php artisan serve

### API Routes

| HTTP Method	| Path | Desciption  |
| ----- | ----- |------------- |
| GET     | api/auth/redirect/{provider} | redirect to provider - social redirect 
| GET     | api/auth/{provider}/callback | callback url - social register/login 
| POST     | api/register | API normal user register via passport
| POST     | api/login | API normal user login via passport

Note: ```/api/auth/redirect/{provider}``` ```/api/auth/{provider}/callback``` is a auth routes for getting register & login the user via social login.

> **Note:**
- You can now use:

- ```GET api/auth/redirect/google``` –> Create user via google provider 

    ```json    

		{
		  "success": true,
		  "data": {
			"provider_redirect": "https://accounts.google.com/o/oauth2/auth?client_id={client_key}&redirect_uri={callback_url}&scope=openid+profile+email&response_type=code"
		  },
		  "message": "Provider 'google' redirect url."
		}

     ```
     
- ```GET api/auth/google/callback``` –> with code to obtain a access token

And remember, Passport requires you to provide the token as a header.

**Run APIs**

Let's run API through postman.

Run authentication API to get passport access token & copy the token and use the same in Header of other CRUD APIs. 

Please check the laravel8-api-auth-passport-socialite documentation - The collection published URL. [API Documentation](https://documenter.getpostman.com/view/10171555/Tz5jfLUV)
