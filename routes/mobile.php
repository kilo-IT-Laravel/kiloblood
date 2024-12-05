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
    Route::get('blood-requests', [BloodRequestController::class, 'index']);
    Route::post('blood-requests', [BloodRequestController::class, 'store']);  
    Route::get('my-blood-request-donors', [BloodRequestController::class, 'viewMyRequestDonors']);
    Route::get('donation-request-report', [BloodRequestController::class, 'requestDonationReport']); 
    Route::get('donation-report', [BloodRequestController::class, 'donationRequestReport']); 
    Route::post('donate/{requestId}/{docId}', [BloodRequestController::class, 'donate']);  
    Route::put('accept-donor/{donorId}', [BloodRequestController::class, 'acceptDonor']); 
    Route::put('cancel-donation/{donorId}', [BloodRequestController::class, 'cancelDonation']); 
    Route::get('my-blood-donors/{donorId}', [BloodRequestController::class, 'viewMyDonorDetails']); 
    Route::get('blood-requests/{id}', [BloodRequestController::class, 'show']); 
    Route::put('blood-requests/reject/{id}', [BloodRequestController::class, 'rejectRequest']); 
});

Route::prefix('notifications')->group(function(){
    Route::get('/', [Notification::class , 'index']);
    Route::get('/{id}', [Notification::class , 'markAsRead']);
});