<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\EmailVerifyController;
use App\Http\Controllers\Api\Auth\OtpController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\permissionController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// api version 1
Route::prefix('v1')->group(function () {

    // public routes (login routes)
    Route::controller(LoginController::class)->group(function () {

        //for user routes
        Route::post('/userAuth', 'loginUser');
        //for client routes
        Route::post('/login', 'loginClient');
        //forgot password routes
        Route::Post('/forgot-password', 'forgotPassword');
        //handle forgot password 
        Route::get('/reset-password/{token?}', 'resetPasswordPage')->name('password.forgot');
    });

    // public routes (register routes)
    Route::controller(RegisterController::class)->group(function () {

        //for user routes
        Route::post('/addUser', 'registerUser');
        //for client routes
        Route::post('/Register', 'registerClient');
    });

    // public routes (email verify routes)
    Route::controller(EmailVerifyController::class)->group(function () {

        //verify email url
        Route::get('/verify-email/{hash?}', 'verifyUserEmail')->name('auth.verify.email');
        //resend verification email
        Route::post('/resend-verify-email', 'resendVerifyEmail');
    });

    Route::controller(OtpController::class)->group(function () {

        //verify client Otp based login
        Route::post('/verify-client', 'authClientByOTP');
        //resend Otp 
        Route::post('/resend-otp', 'resendOTP');
    });

    //protected routes 
    Route::middleware('auth:sanctum')->group(function () {

        //logout user routes
        Route::Post('/logout', [LoginController::class, 'logout']);

        //User accessble routes
        Route::middleware('UserRoute')->group(function () {

            //Roles routes
            Route::prefix('/Role')->group(function () {

                Route::controller(RoleController::class)->group(function () {

                    Route::get('/', 'index');
                    Route::post('/create', 'create');
                    Route::post('/{role}', 'update');
                    Route::delete('/{role}', 'destroy');
                });
            });

            //Permissions routes
            Route::prefix('/Permission')->group(function () {

                Route::controller(permissionController::class)->group(function () {

                    Route::get('/', 'index');
                    Route::post('/create', 'create');
                    Route::post('/{permission}', 'update');
                    Route::delete('/{permission}', 'destroy');
                });
            });

            //user Routs
            Route::prefix('/User')->group(function(){

                Route::controller(UserController::class)->group(function(){

                    Route::get('/', 'index');
                    Route::post('/create', 'create');
                    Route::post('/{user}', 'update');
                    Route::delete('/{user}', 'destroy');
                });
            });
            
        });

        //Client accessble routes
        Route::middleware('verified')->group(function () {

            Route::controller(ClientController::class)->group(function () {

                Route::get('/client', 'index');
            });
        });
    });
});
