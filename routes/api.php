<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\EmailVerifyController;
use App\Http\Controllers\Api\Auth\OtpController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductCatrgoryController;
use App\Http\Controllers\Api\ServiceController;
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
        Route::post('/user-auth', 'loginUser');
        //for client routes
        Route::post('/login', 'loginClient');
        //forgot password routes
        Route::Post('/forgot-password', 'forgotPassword');
        //handle forgot password 
        Route::get('/reset-password/{token?}', 'resetPasswordPage')->name('password.forgot');
    });

    // public routes (register routes)
    Route::controller(RegisterController::class)->group(function () {

        //for client routes
        Route::post('/register', 'registerClient');
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

    //protected routes (autherized user can access)
    Route::middleware('auth:sanctum')->group(function () {

        //for user routes
        Route::post('/add-user', [RegisterController::class, 'registerUser'])->middleware(['checkRoleAndPermission:admin,create_user']);

        //user Routs
        Route::prefix('/user')->group(function(){

            Route::controller(UserController::class)->group(function(){

                Route::get('/', 'index')->middleware(['checkRoleAndPermission:admin,view_user']);
                Route::get('/{user}', 'show')->middleware(['checkRoleAndPermission:admin,view_user']);
                Route::get('/{user}/restore', 'restore')->middleware(['checkRoleAndPermission:admin,restore_data']);
                Route::post('/{user}', 'update')->middleware(['checkRoleAndPermission:admin,edit_user']);
                Route::post('/self/{user}', 'updateUserSelf');
                Route::delete('/{user}', 'destroy')->middleware(['checkRoleAndPermission:admin,delete_user']);
            });
        });

        //logout user routes
        Route::Post('/logout', [LoginController::class, 'logout']);

        //User accessble routes
        Route::middleware('UserRoute')->group(function () {

            //Roles routes
            Route::prefix('/role')->group(function () {

                Route::controller(RoleController::class)->group(function () {

                    Route::get('/', 'index')->middleware(['checkRoleAndPermission:admin,view_role']);
                    Route::get('/{role}', 'show')->middleware(['checkRoleAndPermission:admin,view_role']);
                    Route::get('/{role}/restore', 'restore');
                    Route::post('/create', 'create')->middleware(['checkRoleAndPermission:admin,create_role']);
                    Route::post('/{role}', 'update')->middleware(['checkRoleAndPermission:admin,edit_role']);
                    Route::delete('/{role}', 'destroy')->middleware(['checkRoleAndPermission:admin,delete_role']);
                });
            });

            //Permissions routes
            Route::prefix('/permission')->group(function () {

                Route::controller(PermissionController::class)->group(function () {

                    Route::get('/', 'index')->middleware(['checkRoleAndPermission:admin,view_permission']);
                    Route::get('/{permission}', 'show')->middleware(['checkRoleAndPermission:admin,view_permission']);
                    Route::get('/{permission}/restore', 'restore');
                    Route::post('/create', 'create')->middleware(['checkRoleAndPermission:admin,create_permission']);
                    Route::post('/{permission}', 'update')->middleware(['checkRoleAndPermission:admin,edit_permission']);
                    Route::delete('/{permission}', 'destroy')->middleware(['checkRoleAndPermission:admin,delete_permission']);
                });
            });

            //services routes
            Route::prefix('/services')->group(function () {

                Route::controller(ServiceController::class)->group(function () {

                    Route::get('/', 'index')->middleware(['checkRoleAndPermission:admin,view_service']);
                    Route::get('/{services}', 'show')->middleware(['checkRoleAndPermission:admin,view_service']);
                    Route::get('/{services}/restore', 'restore')->middleware(['checkRoleAndPermission:admin,restore_data']);
                    Route::post('/create', 'create')->middleware(['checkRoleAndPermission:admin,create_service']);
                    Route::post('/{service}', 'update')->middleware(['checkRoleAndPermission:admin,edit_service']);
                    Route::delete('/{service}', 'destroy')->middleware(['checkRoleAndPermission:admin,delete_service']);
                });
            });

            //product routes
            Route::prefix('/product')->group(function () {

                Route::controller(ProductController::class)->group(function () {

                    Route::get('/', 'index')->middleware(['checkRoleAndPermission:admin,view_product']);
                    Route::get('/{product}', 'show')->middleware(['checkRoleAndPermission:admin,view_product']);
                    Route::get('/{product}/restore', 'restore')->middleware(['checkRoleAndPermission:admin,restore_data']);
                    Route::post('/create', 'create')->middleware(['checkRoleAndPermission:admin,create_product']);
                    Route::post('/{product}', 'update')->middleware(['checkRoleAndPermission:admin,edit_product']);
                    Route::delete('/{product}', 'destroy')->middleware(['checkRoleAndPermission:admin,delete_product']);
                });
            });

            //product_categories routes
            Route::prefix('/product-categories')->group(function () {

                Route::controller(ProductCatrgoryController::class)->group(function () {

                    Route::get('/', 'index')->middleware(['checkRoleAndPermission:admin,view_productCategory']);
                    Route::get('/{productcategories}', 'show')->middleware(['checkRoleAndPermission:admin,view_productCategory']);
                    Route::get('/{productcategories}/restore', 'restore')->middleware(['checkRoleAndPermission:admin,restore_data']);
                    Route::post('/create', 'create')->middleware(['checkRoleAndPermission:admin,create_productCategory']);
                    Route::post('/{productcategories}', 'update')->middleware(['checkRoleAndPermission:admin,edit_ProductCategory']);
                    Route::delete('/{productcategories}', 'destroy')->middleware(['checkRoleAndPermission:admin,delete_productCategory']);
                });
            });
        });

        //Client accessble routes
        Route::middleware('verified')->group(function () {

            Route::prefix('/client')->group(function (){
                
                Route::controller(ClientController::class)->group(function () {

                    Route::get('/', 'index')->middleware(['checkRoleAndPermission:admin,view_client']);
                    Route::get('/{client}', 'show');
                    Route::post('/client/{client}', 'update');
                    Route::delete('/client/{client}', 'destroy');
                });
            });
        });

    });
});
