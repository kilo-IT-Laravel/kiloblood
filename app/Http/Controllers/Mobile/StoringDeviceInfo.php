<?php

namespace App\Http\Controllers\Mobile;

use App\Koobeni;
use App\Models\DeviceToken;
use Exception;
use Illuminate\Support\Facades\Auth;

class StoringDeviceInfo extends Koobeni
{
    public function store()
    {
        try {
            $validated = $this->req->validate([
                'device_token' => 'required|string',
                'device_type' => 'required|in:ios,android'
            ]);

            $deviceToken = DeviceToken::updateOrCreate(
                [
                    'device_token' => $validated['device_token']
                ],
                [
                    'user_id' => Auth::id(),
                    'device_type' => $validated['device_type']
                ]
            );

            return $this->dataResponse($deviceToken);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }
}
