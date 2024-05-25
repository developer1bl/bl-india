<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\EmailVerifyController;
use App\Http\Controllers\Api\Auth\OtpController;
use App\Http\Controllers\Api\BlogCategoryController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductCatrgoryController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\NoticeController;
use App\Http\Controllers\Api\DownloadCategoryController;
use App\Http\Controllers\Api\DownloadController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\ServiceSectionController;
use App\Http\Controllers\Api\CustomFormController;
use App\Http\Controllers\Api\HolidayController;
use App\Http\Controllers\Api\StaticPageConroller;
use App\Http\Controllers\Api\StaticPageSectionController;
use App\Http\Controllers\Api\WorkFlowController;
use App\Http\Controllers\Api\TestimonialController;
use App\Http\Controllers\Api\AssociateController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\ClientUserController;
use App\Http\Controllers\Api\ContactUsController;
use App\Http\Controllers\Api\ServiceCategoryController;
use Illuminate\Http\Request;
use App\Helpers\MediaHelper;
use App\Helpers\DocumentHelper;
use App\Http\Controllers\Api\Auth\TokenController;

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
        Route::post('/reset-password', 'authResetRequest')->name('auth.resetPassword');
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

    //protected routes (authorized user can access)
    Route::middleware('auth:sanctum')->group(function () {

        //for checking the token validity
        Route::Post('/check-token', [TokenController::class, 'checkTokenValidity']);

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
                Route::delete('/delete/selected-users', 'deleteSelectedUsers');
                Route::get('/get/AuthUser', 'getAuthUserData');
            });
        });

        //logout user routes
        Route::Post('/logout', [LoginController::class, 'logout']);

        //User accessible routes
        Route::middleware('UserRoute')->group(function () {

            //Roles routes
            Route::prefix('/role')->group(function () {

                Route::controller(RoleController::class)->group(function () {

                    Route::get('/', 'index')->middleware(['checkRoleAndPermission:admin,view_role']);
                    Route::get('/{role}', 'show')->middleware(['checkRoleAndPermission:admin,view_role']);
                    Route::get('/{role}/restore', 'restore')->middleware(['checkRoleAndPermission:admin,restore_data']);
                    Route::post('/create', 'create')->middleware(['checkRoleAndPermission:admin,create_role']);
                    Route::post('/{role}', 'update')->middleware(['checkRoleAndPermission:admin,edit_role']);
                    Route::delete('/{role}', 'destroy')->middleware(['checkRoleAndPermission:admin,delete_role']);
                    Route::delete('/delete/selected-role', 'deleteSelectedRole');
                });
            });

            //Permissions routes
            Route::prefix('/permission')->group(function () {

                Route::controller(PermissionController::class)->group(function () {

                    Route::get('/', 'index')->middleware(['checkRoleAndPermission:admin,view_permission']);
                    Route::get('/{permission}', 'show')->middleware(['checkRoleAndPermission:admin,view_permission']);
                    Route::get('/{permission}/restore', 'restore')->middleware(['checkRoleAndPermission:admin,restore_data']);
                    Route::post('/create', 'create')->middleware(['checkRoleAndPermission:admin,create_permission']);
                    Route::post('/{permission}', 'update')->middleware(['checkRoleAndPermission:admin,edit_permission']);
                    Route::delete('/{permission}', 'destroy')->middleware(['checkRoleAndPermission:admin,delete_permission']);
                    Route::delete('/delete/selected-permission', 'deleteSelectedPermission');
                });
            });

            //service category
            Route::group(['prefix' => 'service-category', 'controller' => ServiceCategoryController::class], function(){

                Route::get('/', 'index');
                Route::get('/{category}','show');
                Route::get('/{category}/restore','restore');
                Route::post('/create', 'create');
                Route::post('/{category}', 'update');
                Route::delete('/{category}', 'destroy');
                Route::delete('/delete/selected-service-category', 'deleteSelectedServiceCategory');
            });

            //services routes
            Route::prefix('/service')->group(function () {

                Route::controller(ServiceController::class)->group(function () {

                    Route::get('/', 'index')->middleware(['checkRoleAndPermission:admin,view_service']);
                    Route::get('/{service}', 'show')->middleware(['checkRoleAndPermission:admin,view_service']);
                    Route::get('/{service}/restore', 'restore')->middleware(['checkRoleAndPermission:admin,restore_data']);
                    Route::post('/create', 'create')->middleware(['checkRoleAndPermission:admin,create_service']);
                    Route::post('/{service}', 'update')->middleware(['checkRoleAndPermission:admin,edit_service']);
                    Route::delete('/{service}', 'destroy')->middleware(['checkRoleAndPermission:admin,delete_service']);
                    Route::delete('/delete/selected-service', 'deleteSelectedService');
                });
            });

            //service section
            Route::prefix('/service-section')->group(function () {

                Route::controller(ServiceSectionController::class)->group(function () {

                    Route::get('/', 'index')->middleware(['checkRoleAndPermission:admin,view_service_section']);
                    Route::get('/{service_section}','show')->middleware(['checkRoleAndPermission:admin,view_service_section']);
                    Route::get('/{service_section}/restore','restore')->middleware(['checkRoleAndPermission:admin,restore_data']);
                    Route::post('/create', 'create')->middleware(['checkRoleAndPermission:admin,create_service_section']);
                    Route::post('/{service_section}', 'update')->middleware(['checkRoleAndPermission:admin,edit_service_section']);
                    Route::delete('/{service_section}', 'destroy')->middleware(['checkRoleAndPermission:admin,delete_service_section']);
                    Route::delete('/delete/selected-service-section', 'deleteSelectedServiceSection');
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
                    Route::delete('/delete/selected-product', 'deleteSelectedProduct');
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
                    Route::delete('/delete/selected-product-category', 'deleteSelectedProductCategory');
                });
            });

            //notice routes
            Route::prefix('/notice')->group(function (){

                Route::controller(NoticeController::class)->group(function () {

                    Route::get('/', 'index')->middleware(['checkRoleAndPermission:admin,view_notice']);
                    Route::get('/{notice}','show')->middleware(['checkRoleAndPermission:admin,view_notice']);
                    Route::get('/{notice}/restore','restore')->middleware(['checkRoleAndPermission:admin,restore_data']);
                    Route::post('/create', 'create')->middleware(['checkRoleAndPermission:admin,create_notice']);
                    Route::post('/{notice}', 'update')->middleware(['checkRoleAndPermission:admin,edit_notice']);
                    Route::delete('/{notice}', 'destroy')->middleware(['checkRoleAndPermission:admin,delete_notice']);
                    Route::delete('/delete/selected-notice', 'deleteSelectedNotice');
                });
            });

            //download category routes
            Route::prefix('/download-category')->group(function (){

                Route::controller(DownloadCategoryController::class)->group(function () {

                    Route::get('/', 'index')->middleware(['checkRoleAndPermission:admin,view_download_category']);
                    Route::get('/{downloadCategory}','show')->middleware(['checkRoleAndPermission:admin,view_download_category']);
                    Route::get('/{downloadCategory}/restore','restore')->middleware(['checkRoleAndPermission:admin,restore_data']);
                    Route::post('/create', 'create')->middleware(['checkRoleAndPermission:admin,create_download_category']);
                    Route::post('/{downloadCategory}', 'update')->middleware(['checkRoleAndPermission:admin,edit_download_category']);
                    Route::delete('/{downloadCategory}', 'destroy')->middleware(['checkRoleAndPermission:admin,delete_download_category']);
                });
            });

            //download routes
            Route::prefix('/download')->group(function (){

                Route::controller(DownloadController::class)->group(function (){

                    Route::get('/', 'index')->middleware(['checkRoleAndPermission:admin,view_download']);
                    Route::get('/{download}','show')->middleware(['checkRoleAndPermission:admin,view_download']);
                    Route::get('/{download}/restore','restore')->middleware(['checkRoleAndPermission:admin,restore_data']);
                    Route::post('/create', 'create')->middleware(['checkRoleAndPermission:admin,create_download']);
                    Route::post('/{download}', 'update')->middleware(['checkRoleAndPermission:admin,edit_download']);
                    Route::delete('/{download}', 'destroy')->middleware(['checkRoleAndPermission:admin,delete_download']);
                });
            });

            //blog category routes
            Route::prefix('/blog-category')->group(function (){

                Route::controller(BlogCategoryController::class)->group(function(){

                    Route::get('/', 'index')->middleware(['checkRoleAndPermission:admin,view_blog_category']);
                    Route::get('/{blogCategory}','show')->middleware(['checkRoleAndPermission:admin,view_blog_category']);
                    Route::get('/{blogCategory}/restore','restore')->middleware(['checkRoleAndPermission:admin,restore_data']);
                    Route::post('/create', 'create')->middleware(['checkRoleAndPermission:admin,create_blog_category']);
                    Route::post('/{blogCategory}', 'update')->middleware(['checkRoleAndPermission:admin,edit_blog_category']);
                    Route::delete('/{blogCategory}', 'destroy')->middleware(['checkRoleAndPermission:admin,delete_blog_category']);
                });
            });

            //blog routes
            Route::prefix('/blog')->group(function (){

                Route::controller(BlogController::class)->group(function(){

                    Route::get('/', 'index')->middleware(['checkRoleAndPermission:admin,view_blog']);
                    Route::get('/{blog}','show')->middleware(['checkRoleAndPermission:admin,view_blog']);
                    Route::get('/{blog}/restore','restore')->middleware(['checkRoleAndPermission:admin,restore_data']);
                    Route::post('/create', 'create')->middleware(['checkRoleAndPermission:admin,create_blog']);
                    Route::post('/{blog}', 'update')->middleware(['checkRoleAndPermission:admin,edit_blog']);
                    Route::delete('/{blog}', 'destroy')->middleware(['checkRoleAndPermission:admin,delete_blog']);
                });
            });

            //custom form
            Route::prefix('/custom-form')->group(function(){

                Route::controller(CustomFormController::class)->group(function(){

                    Route::get('/', 'index');
                    Route::get('/{form}', 'show');
                    Route::get('/{form}/restore', 'restore');
                    Route::post('/create', 'create');
                    Route::post('/{form}', 'update');
                    Route::delete('/{form}', 'destroy');

                    Route::post('/submit/{form}', 'submitFormStore');
                });
            });

            //Holiday
            Route::prefix('/holiday')->group(function(){

                Route::controller(HolidayController::class)->group(function(){

                    Route::get('/', 'index');
                    Route::get('/{holiday}','show');
                    Route::get('/{holiday}/restore','restore');
                    Route::post('/create', 'create');
                    Route::post('/{holiday}', 'update');
                    Route::delete('/{holiday}', 'destroy');
                });
            });

            //static pages
            Route::prefix('/static-page')->group(function(){

                Route::controller(StaticPageConroller::class)->group(function(){

                    Route::get('/', 'index');
                    Route::get('/{staticPage}','show');
                    Route::get('/{staticPage}/restore','restore');
                    Route::post('/create', 'create');
                    Route::post('/{staticPage}', 'update');
                    Route::delete('/{staticPage}', 'destroy');
                });

            });

            //static page sections
            Route::prefix('/static-page-section')->group(function(){

                Route::controller(StaticPageSectionController::class)->group(function(){

                    Route::get('/', 'index');
                    Route::get('/{staticPageSection}','show');
                    Route::get('/{staticPageSection}/restore','restore');
                    Route::post('/create', 'create');
                    Route::post('/{staticPageSection}', 'update');
                    Route::delete('/{staticPageSection}', 'destroy');
                });

            });

            //workflow
            Route::prefix('/workflow')->group(function(){

                Route::controller(WorkflowController::class)->group(function(){

                    Route::get('/', 'index');
                    Route::get('/{workflow}','show');
                    Route::get('/{workflow}/restore','restore');
                    Route::post('/create', 'create');
                    Route::post('/{workflow}', 'update');
                    Route::delete('/{workflow}', 'destroy');
                });

            });

            //testimonial
            Route::prefix('/testimonial')->group(function(){

                Route::controller(TestimonialController::class)->group(function(){

                    Route::get('/', 'index');
                    Route::get('/{testimonial}','show');
                    Route::get('/{testimonial}/restore','restore');
                    Route::post('/create', 'create');
                    Route::post('/{testimonial}', 'update');
                    Route::delete('/{testimonial}', 'destroy');
                });

            });

            //associates routes
            Route::prefix('/associate')->group(function(){

                Route::controller(AssociateController::class)->group(function(){

                    Route::get('/', 'index');
                    Route::get('/{associates}','show');
                    Route::get('/{associates}/restore','restore');
                    Route::post('/create', 'create');
                    Route::post('/{associates}', 'update');
                    Route::delete('/{associates}', 'destroy');
                });

            });

            //team routes
            Route::prefix('/team')->group(function(){

                Route::controller(TeamController::class)->group(function(){

                    Route::get('/', 'index');
                    Route::get('/{team}','show');
                    Route::post('/create', 'create');
                    Route::post('/{team}', 'update');
                    Route::delete('/{team}', 'destroy');
                });

            });

            //client routes
            Route::prefix('/client-user')->group(function(){

                Route::controller(ClientUserController::class)->group(function(){

                    Route::get('/', 'index');
                    Route::get('/{client}','show');
                    Route::get('/{client}/restore','restore');
                    Route::post('/create', 'create');
                    Route::post('/{client}', 'update');
                    Route::delete('/{client}', 'destroy');
                });

            });

            //contact us routes
            Route::prefix('contact-us')->group(function(){

                Route::controller(ContactUsController::class)->group(function(){

                    //get contact us information
                    Route::get('/details', 'getContactDetails');
                    //create contact us information
                    Route::post('/create', 'CreateContactDetails');
                    //update contact us information
                    Route::post('/update/{contact}', 'UpdateContactDetails');
                });
            });

            //media
            Route::prefix('/media')->group(function(){

                Route::post('/upload', function (Request $request){
                    return MediaHelper::uploadImage($request);
                });

                Route::get('/get', function (){
                    return MediaHelper::getAllImages();
                });

                Route::delete('/destroy/{media}', function ($id){
                    return MediaHelper::deleteMedia($id);
                });

                Route::Post('/update/{media}', function(Request $request, string $name){
                    return MediaHelper::updateImage($request, $name);
                });

                Route::get('/{media}', function (String $media){

                    return MediaHelper::getMediaByName($media);
                });
            });

            //Document
            Route::prefix('/document')->group(function(){

                Route::post('/upload', function (Request $request){
                    return DocumentHelper::uploadDocument($request);
                });

                Route::get('/get', function (){
                    return DocumentHelper::getAllDocuments();
                });

                Route::delete('/destroy/{document}', function ($id){
                    return DocumentHelper::deleteDocument($id);
                });

                Route::Post('/update/{document}', function(Request $request, string $name){
                    return DocumentHelper::updateDocument($request, $name);
                });

                Route::get('/download/{document}', function (string $id){
                    return DocumentHelper::downloadDocument($id);
                });
            });
        });

        //Client accessible routes
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

        //for unknown routes
        Route::get('/{any}', function () {

            return response()->json([
                                    'success' => false,
                                    'message' => '404, Page Not found, please try again',
                                    ], 404);
        })->where('any', '.*');
    });
});
