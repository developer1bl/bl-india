<?php

use App\Http\Controllers\Api\ContactUsController;
use App\Http\Controllers\Api\Frontend\AboutPageController;
use App\Http\Controllers\Api\Frontend\BlogPageController;
use App\Http\Controllers\Api\Frontend\BrochureController;
use App\Http\Controllers\Api\Frontend\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Frontend\CaptchaController;
use App\Http\Controllers\Api\Frontend\FormController;
use App\Http\Controllers\Api\Frontend\CalenderController;

Route::prefix('v1')->group(function(){

    //home page frontend routes
    Route::prefix('home')->group(function(){

        Route::get('/', [HomeController::class, 'index']);
        Route::get('/sections/{slug}', [HomeController::class, 'getHomeSectionData']);
        //services routes
        Route::get('/section/services', [HomeController::class, 'getHomeServiceData']);
        Route::get('/service/{service}', [HomeController::class, 'getHomeSingleServiceData']);
        Route::get('/service-all', [HomeController::class, 'getHomeAllServiceData']);
        //blogs routes
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


    //about page routes
    Route::group(['prefix' => 'about', 'controller' => AboutPageController::class], function(){

        Route::get('/', 'index'); //about main page
        Route::get('section/{slug}', 'getAboutSectionData'); //get about sections
        Route::get('team-section', 'getAboutTeamData'); //about team sections
        Route::get('founder-voice-section', 'getFounderVoiceData'); //about founder voice sections
        Route::get('our-client-section', 'getAboutClientData'); //about our client sections
    });

    //contact-us page routes
    Route::group(['prefix' => 'contact-us', 'controller' => ContactUsController::class], function(){

        Route::get('/', 'getContactDetails'); //get contact details
        Route::post('/submit', 'submitContactUsForm'); //submit contact us form
    });

    //blog page routes
    Route::group(['prefix' => 'blog', 'controller' => BlogPageController::class], function(){

        Route::get('/', 'getBlogs'); //blog list
        Route::get('/category', 'getBlogsCategory'); //blog category list
        Route::get('/category/{categorySlug}', 'getCategoryWiseBlogs'); //single blog category
        Route::get('/get/{blog}', 'getSingleBlogData'); //single blog
        Route::get('/latest','getLatestBlogData'); //Latest Blog
    });

    //calender page routes
    Route::group(['prefix' => 'calender', 'controller' => CalenderController::class], function(){

        Route::get('/holiday-list/{month?}', 'getHolidayListByMonth'); //get calender holiday list data
        Route::get('/download/{year?}', 'downloadHolidayListOfYear'); //downloaded calender
        Route::delete('/download/{calendar}', 'downloadHolidayListOfYear'); //delete calender PDF
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
