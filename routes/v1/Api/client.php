<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClientController;

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

?>