<?php

use App\Http\Controllers\Auth\Authentication;
use App\Http\Controllers\test;
use App\Koobeni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::prefix('/admin')->group(function(){
//     include('test/test.php');
// });

Route::post('/register', [Authentication::class, 'register']);
Route::post('/login', [Authentication::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [Authentication::class, 'logout']);
    Route::get('/user', [Authentication::class, 'show']);
});

Route::get('/test', [test::class, 'bruh']);

Route::get('/deleteTokens/{userId}', [Authentication::class, 'terminateAllDeviceTokens']);

Route::get('/collection', [Koobeni::class , 'getCollection']);

Route::post('/testvalidate' , [test::class , 'testbruh']);