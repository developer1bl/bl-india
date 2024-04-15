<?php

use App\Http\Controllers\Api\Frontend\BrochureController;
use App\Http\Controllers\Api\Frontend\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Frontend\CaptchaController;
use App\Http\Controllers\Api\Frontend\FormController;

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

     //captcha routes
     Route::group(['prefix' => 'captcha', 'controller' => CaptchaController::class], function(){

        //getCaptcha
        Route::get('/', 'getCaptcha');
        //validateCaptcha
        Route::post('/validate', 'validateCaptcha');
        //captcha keys
        Route::get('/credential', 'captchaCredentials');
    });

    //brochures form routes
    Route::group(['prefix' => 'brochures', 'controller' => BrochureController::class], function(){

        // submit from handling
        Route::post('/submit', 'submitBrochureForm');
        //deleteBrochurePDf
        Route::delete('/{brochure}', 'deleteBrochurePDF');
    });

    //all static form routes
    Route::controller(FormController::class)->group(function(){

        //this route handles the request to call from
        Route::post('request-to-call', 'requestToCallSubmit');

        //this route handles the Application form
        Route::post('application-form', 'submitApplicationForm');

        //this route handles the partner form routes
        Route::post('partner-form', 'submitPartnerForm');
    });


    Route::prefix('about')->group(function(){

        // Route::get('/', [HomeController::class, 'index']);
        // Route::get('{page}/section/{slug}', [HomeController::class, 'getHomeSectionData']);
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
