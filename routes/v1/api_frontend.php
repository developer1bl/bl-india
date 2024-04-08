<?php

use App\Http\Controllers\Api\Frontend\HomeController;
use App\Models\StaticPage;
use Illuminate\Support\Facades\Route;

Route::prefix('home')->group(function(){

    Route::get('/', [HomeController::class, 'index']);
    Route::get('/section/{slug}', [HomeController::class, 'getHomeSectionData']);
});

?>
