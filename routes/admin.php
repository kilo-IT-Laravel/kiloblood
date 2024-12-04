<?php

use App\Http\Controllers\Admin\analytics;
use App\Http\Controllers\Admin\bannerManagment;
use App\Http\Controllers\Admin\DonorController;
use App\Http\Controllers\Admin\EventManagement;
use App\Http\Controllers\Admin\MedicalRecords;
use App\Http\Controllers\Admin\RequesterController;
use App\Http\Controllers\Admin\UserManagment;
use App\Http\Controllers\Admin\sharesManagment;
use App\Http\Controllers\Admin\socialSharesManagment;
use App\Http\Controllers\Admin\Notifications;
use Illuminate\Support\Facades\Route;


Route::prefix('users')->group(function () {
    Route::get('/', [UserManagment::class , 'index']);
    Route::get('/trash', [UserManagment::class , 'trashUserManagement']);
    Route::get('/{userId}/edit', [UserManagment::class , 'editUser']);
    Route::put('/{userId}', [UserManagment::class , 'update']);
    Route::delete('/{userId}', [UserManagment::class , 'deleteUser']);
    Route::post('/{userId}/restore', [UserManagment::class , 'restoreUser']);
    Route::delete('/{userId}/permanent', [UserManagment::class , 'permenantDeleteUser']);
});

Route::prefix('analytics')->group(function(){
    Route::get('/' , [analytics::class , 'index']);
    Route::get('/chart' , [analytics::class , 'chart']);
});

Route::prefix('notifications')->group(function(){
    Route::get('/' , [Notifications::class , 'index']);
});

Route::prefix('banners')->group(function () {
    Route::get('/', [bannerManagment::class, 'getAllBanners']);
    Route::post('/', [bannerManagment::class, 'store']);
    Route::get('/trash', [bannerManagment::class, 'getTrashed']);
    Route::post('/bulk-restore', [bannerManagment::class, 'bulkRestore']);
    Route::delete('/bulk-force-delete', [bannerManagment::class, 'bulkForceDelete']);
    Route::post('/reorder', [bannerManagment::class, 'reorder']);
    Route::get('/{bannerId}', [bannerManagment::class, 'show']);
    Route::put('/{bannerId}', [bannerManagment::class, 'update']);
    Route::delete('/{bannerId}', [bannerManagment::class, 'destroy']);
    Route::post('/{bannerId}/restore', [bannerManagment::class, 'restore']);
    Route::delete('/{bannerId}/force', [bannerManagment::class, 'forceDelete']);
    Route::post('/{bannerId}/toggle-status', [bannerManagment::class, 'toggleStatus']);
    
    
});


Route::prefix('events')->group(function () {
    Route::get('/', [EventManagement::class, 'index']);
    Route::post('/', [EventManagement::class, 'store']);
    Route::get('/trash', [EventManagement::class, 'getTrashed']);
    Route::post('/reorder', [EventManagement::class, 'reorder']);
    Route::post('/bulk-restore', [EventManagement::class, 'bulkRestore']);
    Route::delete('/bulk-force-delete', [EventManagement::class, 'bulkForceDelete']);
    Route::get('/{eventId}', [EventManagement::class, 'show']);
    Route::put('/{eventId}', [EventManagement::class, 'update']);
    Route::delete('/{eventId}', [EventManagement::class, 'destroy']);
    Route::post('/{eventId}/restore', [EventManagement::class, 'restore']);
    Route::delete('/{eventId}/force', [EventManagement::class, 'forceDelete']);
    Route::post('/{eventId}/toggle-status', [EventManagement::class, 'toggleStatus']);
});

Route::prefix('shares')->group(function () {
    Route::get('/', [sharesManagment::class, 'getAllShares']);
    Route::post('/', [SharesManagment::class, 'store']);
    Route::get('/trash', [SharesManagment::class, 'getTrashed']);
    Route::post('/bulk-restore', [SharesManagment::class, 'bulkRestore']);
    Route::delete('/bulk-force-delete', [SharesManagment::class, 'bulkForceDelete']);
    Route::get('/{id}', [SharesManagment::class, 'show']);
    Route::put('/{id}', [SharesManagment::class, 'update']);
    Route::delete('/{id}', [SharesManagment::class, 'destroy']);
    Route::post('/{id}/restore', [SharesManagment::class, 'restore']);
    Route::delete('/{id}/force', [SharesManagment::class, 'forceDelete']);
    Route::post('/{id}/toggle-status', [SharesManagment::class, 'toggleStatus']);
    
});

Route::prefix('social-shares')->group(function () {
    Route::get('/', [socialSharesManagment::class, 'getAllShares']);
    Route::get('/analytics', [socialSharesManagment::class, 'getAnalytics']);
    Route::delete('/bulk-delete', [socialSharesManagment::class, 'destroy']);
});

Route::prefix('medical-records')->group(function(){
    Route::get('/' , [MedicalRecords::class  , 'index']);
    Route::get('/trashed' , [MedicalRecords::class , 'trashed']);
    Route::get('/{id}' , [MedicalRecords::class , 'show']);
    Route::delete('/{id}' , [MedicalRecords::class , 'delete']);
    Route::post('/{id}' , [MedicalRecords::class , 'restore']);
    Route::delete('/{id}' , [MedicalRecords::class , 'forceDelete']);
});

Route::prefix('/request')->group(function(){
    Route::get('/', [RequesterController::class , 'getAllRequesters']);
    Route::get('/trash' , [RequesterController::class , 'getTrashed']);
    Route::get('/{bloodType}' , [RequesterController::class , 'userRelatedRequest']);
    Route::get('/requester/{requesterId}' , [RequesterController::class , 'getUserRequestDetails']);
    Route::delete('/requester/{requesterId}' , [RequesterController::class , 'delete']);
    Route::post('/requester/{requesterId}/restore' , [RequesterController::class , 'restore']);
    Route::delete('/requester/{requesterId}/force' , [RequesterController::class , 'forceDelete']);
});

Route::prefix('/donors')->group(function(){
    Route::get('/' , [DonorController::class , 'viewDonors']);
    Route::get('/trash' , [DonorController::class , 'trashed']);
    Route::get('/{donorId}' , [DonorController::class , 'donorDetails']);
    Route::delete('/{donorId}' , [DonorController::class , 'delete']);
    Route::post('/{donorId}/restore' , [DonorController::class , 'restore']);
    Route::delete('/{donorId}/force' , [DonorController::class , 'forceDelete']);
});