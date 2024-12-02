<?php

use App\Http\Controllers\Mobile\BloodRequestController;
use App\Http\Controllers\Mobile\DonorController;
use App\Http\Controllers\Mobile\RequesterController;
use Illuminate\Support\Facades\Route;

Route::prefix('banners')->group(function () {
    Route::get('/', 'Mobile\BannerController@index');
    Route::get('/{id}', 'Mobile\BannerController@show');
});

Route::prefix('events')->group(function () {
    Route::get('/', 'Mobile\EventController@index');
    Route::get('/{id}', 'Mobile\EventController@show');
});

Route::prefix('shares')->group(function () {
    Route::get('/', 'Mobile\ShareController@index');
    Route::post('/record', 'Mobile\ShareController@recordShare');
});

Route::prefix('requests')->group(function(){
    Route::get('/', [BloodRequestController::class , 'index']);
    Route::post('/', [BloodRequestController::class , 'store']);
    Route::get('/my-requests' , [BloodRequestController::class , 'myRequests']);
    Route::post('/reject/{requestId}', [BloodRequestController::class , 'hideRequest']);
    Route::put('/cancel/{requestId}' , [BloodRequestController::class , 'cancelRequest']);
});

Route::prefix('donors')->group(function(){
    Route::post('/accept/{requestId}' , [DonorController::class , 'acceptTheDonate']);
    Route::get('/my-donations' , [DonorController::class , 'myDonations']);
});

Route::prefix('requester')->group(function(){
    Route::post('/confirm-donation/{requestId}/{donorId}' , [RequesterController::class , 'confirmDonation']);
    Route::get('/view-request-donor/{requestId}' , [RequesterController::class , 'viewRequestDonors']);
});