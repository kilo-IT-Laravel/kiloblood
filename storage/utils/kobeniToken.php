<?php

namespace Storage\utils;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

trait kobeniToken
{
    public function TokenRegister($credentials, $model, $token = true, $date)
    {
        if (!isset($credentials['password'])) {
            throw new AuthenticationException('Need to key["password"]!');
        }

        $data['password'] = Hash::make($credentials['password']);

        $user = $model::create($credentials);

        if ($token == true) {
            $expireDate = now()->addDays($date);
            if ($date) {
                $user->createToken('my_token', expiresAt: $expireDate)->plainTextToken;
            } else {
                $user->createToken('my_token')->plainTextToken;
            }
        }

        return $user;
    }

    public function TokenLogin($credentials, $model, $date , $oneTimeLogin = false)
    {
        $keys = array_keys($credentials);
        $values = array_values($credentials);
        $user = $model::where($keys[0], $values[0])->first();

        if (!$user || !Hash::check($values[1], $user->password)) {
            throw new AuthenticationException('Invalid credentials');
        }

        $expireDate = now()->addDays($date);

        if ($oneTimeLogin) {
            DB::table('personal_access_tokens')->where('tokenable_id', $user->id)->delete();
        }

        $token = '';

        if ($date) {
            $token = $user->createToken('my_token', expiresAt: $expireDate)->plainTextToken;
        } else {
            $token = $user->createToken('my_token')->plainTextToken;
        }

        DB::table('devices')->insert([
            'user_id' => $user->id,
            'user_agent' => $this->req->header('User-Agent'),
            'ip' => $this->req->ip(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return $token;
    }

    public function TokenLogout($user)
    {
        $user->currentAccessToken()->delete();
    }
}
