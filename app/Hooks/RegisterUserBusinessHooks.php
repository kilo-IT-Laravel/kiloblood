<?php

namespace App\Hooks;

use Illuminate\Support\Facades\Log;

class RegisterUserBusinessHooks
{
    public static function registerBeforeHook($data)
    {
        Log::info("Before Hook - registerUserBusiness: " . print_r($data, true));

        if (empty($data[0]['email'])) {
            $data[0]['email'] = 'default@example.com';
        }

        return $data;
    }

    public static function registerAfterHook($result)
    {
        Log::info('After Hook for registerUserBusiness: ' . print_r($result, true));

        $result['message'] = 'User successfully registered!';
        return $result;
    }
}
