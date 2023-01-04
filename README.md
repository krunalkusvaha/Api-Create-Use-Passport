# Laravel Api create use a passport 

## Api Create Steps

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


## Admin api create step use the middleware

## Step 1:  Create admin middleware
            - php artisan make:middleware AdminAuthenticated
            - public function handle(Request $request, Closure $next) {
                if (Auth::guard('admin')->check()) {
                    return $next($request);
                }
              }

## Step 2:  Opent the app/Http/Kernel.php file and register the middleware  
            - protected $routeMiddleware = [
                'adminauth' => \App\Http\Middleware\AdminAuthenticated::class,
              ];

## Step 3:  Create model below command
            - php artisan make:model Admin

## Step 4:  Open the config/auth.php file and make the guards
            - 'guards'=> [
                    'admin' => [
                        'driver' => 'passport',
                        'provider' => 'admins',
                    ],
                ],
            - 'providers' => [
                    'admins' => [
                        'driver' => 'eloquent',
                        'model' => App\Models\Admin::class,
                    ],
                ],

## Step 5:  Create admin api controller below command
            - php artisan make: controller Api/AdminAuthController

## Step 6:  open routes/api.php file and make the route 
            - Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
                Route::post('login', [AdminAuthController::class, 'adminLogin']);
                Route::group(['middleware' => 'adminauth'], function () {
                    Route::get('logout', [AdminAuthController::class,'adminlogout']);
                    Route::get('profile', [AdminAuthController::class,'get_admin_profile']);
                    Route::post('profile-update', [AdminAuthController::class,'profile_update_post']);
                    Route::post('change-password', [AdminAuthController::class,'change_password_post']);
                });
            });

## Step 7:  Make the table below command
            - php artisan make:migration create_admin_table
            - migrate the table : php artisan migrate 
