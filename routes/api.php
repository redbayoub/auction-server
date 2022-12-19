<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', App\Http\Controllers\Api\MeController::class);
    Route::apiResource('items', App\Http\Controllers\Api\ItemController::class)->only(['index', 'show']);
    
    Route::middleware('isUser')->group(function () {
        Route::post('/items/{id}/bids',[App\Http\Controllers\Api\BidController::class,'store'])->name('items.bids.store');
    });
    Route::apiResource('items', App\Http\Controllers\Api\ItemController::class)->only(['index', 'show']);

    Route::middleware('isAdmin')->group(function () {
        Route::get('/items/{id}/bids',[App\Http\Controllers\Api\BidController::class,'index'])->name('items.bids.index');
        Route::post('/items/{id}',[App\Http\Controllers\Api\ItemController::class,'update'])->name('items.update');
        Route::apiResource('items', App\Http\Controllers\Api\ItemController::class)->only(['store', 'destroy']);
    });
});
