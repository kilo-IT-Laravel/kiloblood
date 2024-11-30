<?php

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