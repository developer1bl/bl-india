<?php

use App\Http\Controllers\Api\Frontend\HomeController;
use Illuminate\Support\Facades\Route;

Route::prefix('home')->group(function(){

    Route::get('/', [HomeController::class, 'index']);
    Route::get('{page}/section/{slug}', [HomeController::class, 'getHomeSectionData']);
    Route::get('/section/services', [HomeController::class, 'getHomeServiceData']);
    Route::get('/section/blogs', [HomeController::class, 'getHomeBlogData']);
});

Route::prefix('about')->group(function(){

    // Route::get('/', [HomeController::class, 'index']);
    // Route::get('{page}/section/{slug}', [HomeController::class, 'getHomeSectionData']);
});


?>
