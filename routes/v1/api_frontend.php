<?php

use App\Http\Controllers\Api\Frontend\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Frontend\CaptchaController;

Route::prefix('v1')->group(function(){

    //home page frontend routes
    Route::prefix('home')->group(function(){

        Route::get('/', [HomeController::class, 'index']);
        Route::get('/section/{slug}', [HomeController::class, 'getHomeSectionData']);
        Route::get('/section/services', [HomeController::class, 'getHomeServiceData']);
        Route::get('/section/blogs', [HomeController::class, 'getHomeBlogData']);
        Route::get('/section/workflow', [HomeController::class, 'getHomeWorkFlowData']);
        Route::get('/section/testimonials', [HomeController::class, 'getHomeTestimonialsData']);
        Route::get('/section/associate', [HomeController::class, 'getHomeAssociateData']);
    });

    Route::prefix('about')->group(function(){

        // Route::get('/', [HomeController::class, 'index']);
        // Route::get('{page}/section/{slug}', [HomeController::class, 'getHomeSectionData']);
    });

    //captcha routes
    Route::group(['prefix' => 'captcha', 'controller' => CaptchaController::class], function(){

        //getCaptcha
        Route::get('/', 'getCaptcha');
        //validateCaptcha
        Route::post('/validate', 'validateCaptcha');
        //captcha keys
        Route::get('/credential', 'captchaCredentials');
    });

    //for unknown routes
    Route::get('/{any}', function () {

        return response()->json([
                                'success' => false,
                                'message' => '404, Page Not found, please try again',
                                ], 404);

    })->where('any', '.*');
});
?>
