<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Storage\utils\KobeniValidation;
use Storage\utils\Validators\ValidationReplacer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(KobeniValidation::class, function ($app) {
            return new KobeniValidation();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $kobeniValidation = app(KobeniValidation::class);

        Validator::extend('strongPassword', [$kobeniValidation, 'strongPassword']);
        Validator::extend('validDateRange', [$kobeniValidation, 'validDateRange']);
        Validator::extend('validTimeRange', [$kobeniValidation, 'validTimeRange']);
        Validator::extend('minimumAge', [$kobeniValidation, 'minimumAge']);
        Validator::extend('phoneNumber', [$kobeniValidation, 'phoneNumber']);

        $validationReplacer = app(ValidationReplacer::class);
        $validationReplacer->register();
    }
}
