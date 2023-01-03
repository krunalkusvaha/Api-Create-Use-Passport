# Laravel Api create use a passport 

## Steps

## Step 1: Laravel Installation
            - copmoser laravel-project laravel/laravel ("your project name")

## Step 2: Database Configuration
            - create your database and .env file change database name

## Step 3:  Laravel Passport Installation
            -  composer require laravel/passport

            - Open the app/Providers/AppServiceProvider.php and add the below line of code:
                - use Illuminate\Support\Facades\Schema;
                - public function boot(){
                -        Schema::defaultStringLength(191);
                - }
            - then migrate your table 
                - php artisan migrate
            - Next run the below command this command create the encryption keys for generating secured access tokens:
                - php artisan passport:install 
                - after passwort install to jenerate the client id and client secret key so your .env file to change 
                - open .env file below code 
                    - PASSPORT_PERSONAL_ACCESS_CLIENT_ID="1"
                    - PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET="Yxf9iAvYnj06SL3545AG8YLUXu97m9Pjsy5aRm4Y"

## Step 4:  Passport Configuration
            - this step open the App/Models/User.php fileand write the Configuration
            - Uncomment this line : use Illuminate\Contracts\Auth\MustVerifyEmail;    

## Step 5:  Register passport routes
            - Open this file App/Providers/AuthServiceProvider.php 
            - Add the package this 
                - use Illuminate\Support\Facades\Gate;
                - use Laravel\Passport\Passport;

## Step 6:  Update the config/auth.php change the api driver
            - 'api' => [
            -    'driver' => 'passport',
            -    'provider' => 'users',
            -    'hash' => false,
            -   ],

## Step 7:  API Route Creation
            - Open your route/api.php and create your api route 

## Step 8:  Controller Creation
            - php artisan make:controller Api\UserController

## Step 9:  Laravel Rest API testing Using Postman

