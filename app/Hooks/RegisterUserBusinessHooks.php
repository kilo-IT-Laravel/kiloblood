<?php

namespace App\Hooks;

use Illuminate\Support\Facades\Log;

class RegisterUserBusinessHooks
{
    public static function registerBeforeHook($data)
    {
        if (empty($data[0]['email'])) {
            $data[0]['email'] = 'default@example.com';
        }

        return $data;
    }

    public static function registerAfterHook($result)
    {
        $result['message'] = 'User successfully registered!';
        return $result;
    }
}
