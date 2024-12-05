<?php

use App\Http\Controllers\Admin\UserManagment;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::call(function(UserManagment $verifier) {
    User::whereNull('trusted_at')
        ->where('available_for_donation', true)
        ->chunk(100, function($users) use ($verifier) {
            foreach($users as $user) {
                $verifier->verifyUser($user);
            }
        });
})->dailyAt('00:00');