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

    Route::get('/user/notifications', [App\Http\Controllers\Api\NotificationsController::class, 'index'])->name('user.notifications.show');
    Route::put('/user/notifications', [App\Http\Controllers\Api\NotificationsController::class, 'update'])->name('user.notifications.update');
    Route::delete('/user/notifications', [App\Http\Controllers\Api\NotificationsController::class, 'destroy'])->name('user.notifications.destroy');

    Route::middleware('isUser')->group(function () {
        Route::post('/items/{id}/bids', [App\Http\Controllers\Api\BidController::class, 'store'])->name('items.bids.store');
        Route::get('/items/{id}/auto-bid', [App\Http\Controllers\Api\AutoBidItemController::class, 'show'])->name('items.auto-bid.show');
        Route::post('/items/{id}/auto-bid', [App\Http\Controllers\Api\AutoBidItemController::class, 'store'])->name('items.auto-bid.enable');
        Route::delete('/items/{id}/auto-bid', [App\Http\Controllers\Api\AutoBidItemController::class, 'destroy'])->name('items.auto-bid.disable');

        Route::get('/user/bot', [App\Http\Controllers\Api\BotController::class, 'show'])->name('user.bot.show');
        Route::put('/user/bot', [App\Http\Controllers\Api\BotController::class, 'update'])->name('user.bot.update');

        Route::get('/user/bids', [App\Http\Controllers\Api\UserBidController::class, 'index'])->name('user.bids.index');
        Route::get('/user/items', [App\Http\Controllers\Api\UserItemController::class, 'index'])->name('user.items.index');
    });

    Route::apiResource('items', App\Http\Controllers\Api\ItemController::class)->only(['index', 'show']);

    Route::middleware('isAdmin')->group(function () {
        Route::get('/items/{id}/bids', [App\Http\Controllers\Api\BidController::class, 'index'])->name('items.bids.index');
        Route::post('/items/{id}', [App\Http\Controllers\Api\ItemController::class, 'update'])->name('items.update');
        Route::apiResource('items', App\Http\Controllers\Api\ItemController::class)->only(['store', 'destroy']);
    });
});
