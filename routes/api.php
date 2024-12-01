<?php

use App\Http\Controllers\Auth\Authentication;
use App\Http\Controllers\test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::prefix('/admin')->group(function(){
//     include('test/test.php');
// });

Route::post('/register', [Authentication::class, 'register']);
Route::post('/login', [Authentication::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [Authentication::class, 'logout']);
    Route::get('/user', [Authentication::class, 'show']);
    Route::put('/profile', [Authentication::class, 'updateProfile']);
    Route::put('/password', [Authentication::class , 'updatePassword']);
    Route::delete('/account', [Authentication::class , 'deleteAccount']);

    // Device management
    Route::get('/devices', [Authentication::class , 'getDeviceHistory']);
    Route::post('/devices/{id}/logout', [Authentication::class , 'logoutDevice']);
    Route::post('/devices/logout-all', [Authentication::class , 'logoutAllDevices']);
});

Route::prefix('/mobile')->middleware('auth:sanctum')->group(function () {
    include('mobile.php');
});

Route::prefix('/admin')->middleware(['auth:sanctum' , 'doctor'])->group(function () {
    include('admin.php');
});

Route::get('/test', [test::class, 'bruh']);

// Route::get('/deleteTokens/{userId}', [Authentication::class, 'terminateAllDeviceTokens']);

// Route::get('/collection', [Koobeni::class , 'getCollection']);

// Route::post('/testvalidate' , [test::class , 'testbruh']);

// Route::post('/postey' , [test::class , 'testBruh1']);

// Route::post('/testing' , [test::class , 'uploadSingle']);