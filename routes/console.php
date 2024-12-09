<?php

use App\Http\Controllers\Admin\UserManagment;
use App\Models\BloodRequest;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::call(function (UserManagment $verifier) {
    User::whereNull('trusted_at')
        ->where('available_for_donation', true)
        ->chunk(100, function ($users) use ($verifier) {
            foreach ($users as $user) {
                $verifier->verifyUser($user);
            }
        });
})->dailyAt('00:00');

Schedule::call(function () {
    BloodRequest::where('status', 'pending')
        ->where('expired_at', '<', now())
        ->update(['status' => 'cancelled']);
})->dailyAt('00:00');

Schedule::call(function () {
    Event::where('is_active', true)
        ->where('end_date', '<', now())
        ->update(['is_active' => false]);
})->dailyAt('00:00');



//// dont forget event too
