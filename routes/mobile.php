<?php

use App\Http\Controllers\Auth\Authentication;
use App\Http\Controllers\Mobile\BannerController;
use App\Http\Controllers\Mobile\BloodRequestController;
use App\Http\Controllers\Mobile\EventController;
use App\Http\Controllers\Mobile\Notification;
use App\Http\Controllers\Mobile\ShareController;
use Illuminate\Support\Facades\Route;

Route::get('/statistic', [Authentication::class , 'getStats']);

Route::prefix('banners')->group(function () {
    Route::get('/', [BannerController::class , 'index']);
    Route::get('/{id}', [BannerController::class , 'show']);
});

Route::prefix('events')->group(function () {
    Route::get('/', [EventController::class , 'index']);
    Route::get('/{id}', [EventController::class , 'show']);
});

Route::prefix('shares')->group(function () {
    Route::get('/', [ShareController::class , 'index']);
    Route::post('/record', [ShareController::class , 'recordShare']);
});

Route::prefix('requests')->group(function(){
    Route::get('/', [BloodRequestController::class, 'index']);
    Route::post('/', [BloodRequestController::class, 'store']);
    Route::get('/my-request', [BloodRequestController::class, 'myRequests']);
    Route::post('/donate/{reqId}',[BloodRequestController::class , 'donate'])->middleware('available'); /// this one logic is a bit tricky
    Route::post('/cancel/{reqId}',[BloodRequestController::class , 'cancel']);
    Route::post('/confirm/{donorId}',[BloodRequestController::class , 'confirmDonor']); /// this one too
    Route::get('/report/my-donation' , [BloodRequestController::class , 'myDonationHistory']);
    Route::get('/report/my-request' , [BloodRequestController::class , 'myRequestHistory']);
    Route::get('/search-donor' , [BloodRequestController::class , 'searchForDonor']);
});


//Route::post('/donate/{reqId}',[BloodRequestController::class , 'donate']); /// this one logic is a bit tricky
Route::get('/show/{reqId}', [BloodRequestController::class, 'show']);
Route::get('/view-detail/{reqId}', [BloodRequestController::class, 'detailRequests']);

Route::prefix('notifications')->group(function(){
    Route::get('/', [Notification::class , 'index']);
    Route::get('/{id}' , [Notification::class , 'viewDetails']);
    Route::post('/{id}', [Notification::class , 'markAsRead']);
}); //// not yet
