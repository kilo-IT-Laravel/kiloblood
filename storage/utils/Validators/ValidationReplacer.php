<?php

namespace Storage\utils\Validators;

use Illuminate\Support\Facades\Validator;

class ValidationReplacer
{
    public function register()
    {
        Validator::replacer('strongPassword', function ($message, $attribute, $rule, $parameters) {
            return 'The password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one number, and one special character.';
        });

        Validator::replacer('validDateRange', function ($message, $attribute, $rule, $parameters) {
            return "The date range must be between {$parameters[0]} and {$parameters[1]}.";
        });

        Validator::replacer('validTimeRange', function ($message, $attribute, $rule, $parameters) {
            $startTime = $parameters[0] ?? '00:00';
            $endTime = $parameters[1] ?? '23:59';
            return "The time must be between {$startTime} and {$endTime}.";
        });

        Validator::replacer('minimumAge', function ($message, $attribute, $rule, $parameters) {
            $ageLimit = $parameters[0] ?? 18;
            return "You must be at least {$ageLimit} years old.";
        });

        Validator::replacer('phoneNumber', function ($message, $attribute, $rule, $parameters) {
            return "The {$attribute} field must be a valid phone number for your selected country.";
        });
    }
}
