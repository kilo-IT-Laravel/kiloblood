<?php

namespace Storage\utils;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class KobeniValidation
{
    public function strongPassword($attribute, $value, $parameters)
    {
        $pattern = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/';

        return preg_match($pattern, $value) === 1;
    }

    public function validDateRange($attribute, $value, $parameters)
    {
        if (count($parameters) != 2) {
            return false;
        }

        $startDate = Carbon::parse($parameters[0]);
        $endDate = Carbon::parse($parameters[1]);

        return Carbon::parse($value)->between($startDate, $endDate);
    }

    public function validTimeRange($attribute, $value, $parameters)
    {
        if (count($parameters) !== 2) {
            return false;
        }

        $startTime = Carbon::createFromFormat('H:i', $parameters[0]);
        $endTime = Carbon::createFromFormat('H:i', $parameters[1]);
        $time = Carbon::createFromFormat('H:i', $value);

        return $time->between($startTime, $endTime);
    }

    public function minimumAge($attribute, $value, $parameters)
    {
        $ageLimit = $parameters[0] ?? 18;
        $dob = Carbon::parse($value);
        $age = $dob->age;

        return $age >= $ageLimit;
    }

    public function phoneNumber($attribute, $value, $parameters)
    {
        $country = env('PHONE_VALIDATION_COUNTRY', 'US');

        switch ($country) {
            case 'CN':
                return $this->validateChinaPhone($value);
            case 'KH':
                return $this->validateCambodiaPhone($value);
            case 'US':
            default:
                return $this->validateUsPhone($value);
        }
    }

    protected function validateChinaPhone($value)
    {
        return preg_match('/^\+86\d{11}$/', $value) === 1;
    }

    protected function validateCambodiaPhone($value)
    {
        return preg_match('/^\+855\d{8}$/', $value) === 1;
    }

    protected function validateUsPhone($value)
    {
        return preg_match('/^\+1\d{10}$/', $value) === 1;
    }
}
