<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout']);
});

Route::get('/items/{id}/bill', [App\Http\Controllers\ItemBillController::class, 'show'])
    ->name('items.bill.show')
    ->middleware('signed');
