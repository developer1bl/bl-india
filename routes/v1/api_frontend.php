<?php

use App\Http\Controllers\Api\CareerController;
use App\Http\Controllers\Api\ContactUsController;
use App\Http\Controllers\Api\Frontend\AboutPageController;
use App\Http\Controllers\Api\Frontend\BlogPageController;
use App\Http\Controllers\Api\Frontend\BrochureController;
use App\Http\Controllers\Api\Frontend\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Frontend\CaptchaController;
use App\Http\Controllers\Api\Frontend\FormController;
use App\Http\Controllers\Api\Frontend\CalenderController;
use App\Http\Controllers\Api\Frontend\KnowledgeBaseController;
use App\Http\Controllers\Api\Frontend\ServicePageController;
use App\Http\Controllers\Api\GalleryController;
use App\Models\KnowledgeBaseCategory;
use App\Helpers\DownloadBrochureHelper;
use App\Helpers\LanguageHelper;
use App\Http\Controllers\Api\Frontend\DownloadController;
use App\Http\Controllers\Api\Frontend\NotificationController;
use App\Http\Controllers\Api\Frontend\ProductController;
use App\Http\Controllers\Api\Frontend\SocialMediaController;
use App\Models\Associate;
use App\Models\Certificate;
use App\Models\ContactUs;
use App\Models\QuickLink;
use App\Models\Service;
use App\Models\SocialMedia;
use Illuminate\Http\Request;

Route::prefix('v1')->group(function () {

    //home page frontend routes
    Route::prefix('home')->group(function () {

        Route::get('/', [HomeController::class, 'index']);
        Route::get('/sections/{slug}', [HomeController::class, 'getHomeSectionData']);
        //services routes
        Route::get('/section/services', [HomeController::class, 'getHomeServiceData']);
        Route::get('/service/{service}', [HomeController::class, 'getHomeSingleServiceData']);
        // Route::get('/service-all', [HomeController::class, 'getHomeAllServiceData']);
        //blogs routes
        Route::get('/section/blogs', [HomeController::class, 'getHomeBlogData']);
        Route::get('/section/workflow', [HomeController::class, 'getHomeWorkFlowData']);
        Route::get('/section/testimonials', [HomeController::class, 'getHomeTestimonialsData']);
        Route::get('/section/associate', [HomeController::class, 'getHomeAssociateData']);
    });

    //captcha routes
    Route::group(['prefix' => 'captcha', 'controller' => CaptchaController::class], function () {

        //getCaptcha
        Route::get('/', 'getCaptcha');
        //validateCaptcha
        Route::post('/validate', 'validateCaptcha');
        //captcha keys
        Route::get('/credential', 'captchaCredentials');
    });

    //brochures form routes
    Route::group(['prefix' => 'brochures', 'controller' => BrochureController::class], function () {

        Route::get('/', 'brochureFromImage');
        //country list
        Route::get('/country', function(){
            return DownloadBrochureHelper::getCountryData();
        });
        //service list
        Route::get('/service', function(){
            return DownloadBrochureHelper::getServiceData();
        });
        //sources list
        Route::get('/sources', function(){
            return DownloadBrochureHelper::getSourceList();
        });
        // submit from Download brochure form
        Route::post('/submit', 'submitBrochureForm');
        //deleteBrochurePDf
        Route::delete('/{brochure}', 'deleteBrochurePDF');
    });

    //all static form routes
    Route::controller(FormController::class)->group(function () {

        //this route handles the request to call from
        Route::post('request-to-call', 'requestToCallSubmit');
        //this route handles the Application form
        Route::post('application-form', 'submitApplicationForm');
        //this route handles the partner form routes
        Route::post('partner-form', 'submitPartnerForm');
    });


    //about page routes
    Route::group(['prefix' => 'about', 'controller' => AboutPageController::class], function () {

        Route::get('/', 'index'); //about main page
        Route::get('section/{slug}', 'getAboutSectionData'); //get about sections
        Route::get('team-section', 'getAboutTeamData'); //about team sections
        Route::get('founder-voice-section', 'getFounderVoiceData'); //about founder voice sections
        Route::get('our-client-section', 'getAboutClientData'); //about our client sections
    });

    //contact-us page routes
    Route::group(['prefix' => 'contact-us', 'controller' => ContactUsController::class], function () {

        Route::get('/', 'getContactDetails'); //get contact details
        Route::get('/google-map', 'getGoogleMapContents'); //for google map display
        Route::post('/submit', 'submitContactUsForm'); //submit contact us form
    });

    //blog page routes
    Route::group(['prefix' => 'blog', 'controller' => BlogPageController::class], function () {

        Route::get('/', 'getBlogs'); //blog list
        Route::get('/category', 'getBlogsCategory'); //blog category list
        Route::get('/category/{categorySlug}', 'getCategoryWiseBlogs'); //single blog category
        Route::get('/get/{blog}', 'getSingleBlogData'); //single blog
        Route::get('/latest', 'getLatestBlogData'); //Latest Blog
    });

    //calender page routes
    Route::group(['prefix' => 'calender', 'controller' => CalenderController::class], function () {

        Route::get('/holiday-list/{month?}', 'getHolidayListByMonth'); //get calender holiday list data
        Route::get('/download/{year?}', 'downloadHolidayListOfYear'); //downloaded calender
        Route::delete('/download/{calendar}', 'downloadHolidayListOfYear'); //delete calender PDF
    });

    //service page route
    Route::group(['prefix' => 'service', 'controller' => ServicePageController::class], function () {

        //get all service categories
        Route::get('/service-category', 'getAllServiceCategories');
        //get single service category
        Route::get('/service-category/{category}', 'getSingleServiceCategory');
        //get all services
        Route::get('/', 'getAllServices');
        //get single service
        Route::get('/get/{service}', 'getSingleService');
        //service detail page section
        Route::get('/{service?}/service-section/{section?}', 'getServiceSectionDetails');
        //get all service sections
        Route::get('/{service?}/get/service-section', 'getAllServiceSectionList');
    });

    //product page route
    Route::group(['prefix' => 'product', 'controller' => ProductController::class], function () {
        //get product category
        Route::get('/product-category', 'getProductCategory');
        //get product by category
        Route::get('/category/{category}', 'getProductByCategory');
        //get product details
        Route::get('/{product}', 'getProductDetails');
        //get product all services
        Route::get('/{product}/services', 'getProductAllServices');
    });

    //careers
    Route::group(['prefix' => 'careers', 'controller' => CareerController::class], function () {
        Route::get('/', 'index');
    });

    //gallery
    Route::get('/gallery', [GalleryController::class, 'index']);

    //knowledge base route
    Route::group(['prefix' => 'knowledgeBase', 'controller' => KnowledgeBaseController::class], function () {
        //for all knowledge base categories
        Route::get('/category', 'viewAllCategories');
        //for single knowledge base category
        Route::get('/category/{category}', 'viewSingleCategory');
        //for single knowledge base article
        Route::get('/', 'viewAllKnowledgeBase');
        //for all knowledge base articles
        Route::get('/{knowledgeBase}', 'viewSingleKnowledgeBase');
    });

    //Notifications
    Route::group(['prefix' => 'notification', 'controller' => NotificationController::class], function () {
        //get all notifications
        Route::get('/', 'index');
        //get single notification
        Route::get('/{notice}', 'getSingleNotification');
       //get all notifications category
        Route::get('/category/all', 'getNotificationCategory');
        //get single notification category
        Route::get('/category/{category}', 'getSingleNotificationCategory');
    });

    //social media
    Route::group(['prefix' => 'social-media', 'controller' => SocialMediaController::class], function (){
        //get all social media
        Route::get('/', 'getSocialMedia');
    });

    //language api
   Route::prefix('language')->group(function(){

        Route::get('/', function(Request $request){

            $language = LanguageHelper::getLanguageByRegion($request->ip() ?? null);

            if (!empty($language)) {

                return response()->json([
                                        'success' => true,
                                        'data' => $language
                                        ]);
            } else {

                return response()->json([
                                       'success' => false,
                                       'message' => 'Language not found',
                                        ]);
            }

        });
   });

   //download api
   Route::group(['prefix' => 'download', 'controller' => DownloadController::class], function (){
        //get all download categories
        Route::get('/category', 'getCategory');
        //get download list based on category
        Route::get('/category/{category}', 'getDownloadByCategory');
        //get download list based
        Route::get('/', 'getDownload');
   });

   //for data render api (convert editor json data into normal text format)
   Route::prefix('data-render')->group(function(){

        Route::post('/', function(Request $request){

            $data = json_decode($request->field_data) ?? null;

            $filteredData = DownloadBrochureHelper::extractTextFromData($data ?? null);

            if (!empty($filteredData)) {

                return response()->json([
                                        'success' => true,
                                        'data' => $filteredData
                                        ],200);
            } else {

                return response()->json([
                                       'success' => false,
                                       'message' => 'no data available',
                                        ],204);
            }
        });
   });

   //footer section api
   Route::prefix('footer')->group(function(){
        //for associate
        Route::get('/associate', function(){

            $associate = Associate::orderBy('associate_id')->get();

            return response()->json([
                                    'data' => $associate ?? [],
                                    'success' => true
                                    ], 200);
        });

        //for social media icons
        Route::get('/social-media', function(){

            $socialMedia = SocialMedia::all();

            return response()->json(['data' => $socialMedia ?? []], 200);
        });

        //company imformation
        Route::get('/company-information', function(){

            $information = ContactUs::first();

            return response()->json([
                                    'data' => $information?? [],
                                    'success' => true
                                    ], 200);
        });

        //service
        Route::get('/service', function(){
            $service = Service::all('service_name');

            return response()->json([
                                    'data' => $service?? [],
                                    'success' => true
                                    ], 200);
        });

        //certificates
        Route::get('/certificates', function(){

            $certificates = Certificate::all();

            return response()->json([
                                    'data' => $certificates?? [],
                                    'success' => true
                                    ], 200);
        });

        //quick link
        Route::get('/quick-link', function(){
            $quickLink = QuickLink::all();
            return response()->json([
                                    'data' => $quickLink?? [],
                                    'success' => true
                                    ], 200);
        });
   });

    //for unknown routes
    Route::get('/{any}', function () {

        return response()->json([
            'success' => false,
            'message' => '404, Page Not found, please try again',
        ], 404);
    })->where('any', '.*');
});
