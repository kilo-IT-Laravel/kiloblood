<?php

namespace App\Http\Controllers\Auth;

use App\Koobeni;
use App\Models\DocumentationFile;
use App\Models\User;
use Exception;

class Authentication extends Koobeni
{

    public function register()
    {
        try {
            $cred = $this->req->validate([
                'name' => 'required|string',
                'phone_number' => 'required|string|unique:users,phone_number',
                'password' => 'required|string|confirmed',
                'blood_type' => 'required|string',
                'location' => 'required|string',
                'medical_file' => 'required|file|mimes:pdf,doc,docx|max:10248',
                'role' => 'required|in:user,doctor',
                'description' => 'nullable|string'
            ]);

            $user = $this->TokenRegister([
                'model' => User::class,
                'credentials' => $cred
            ]);

            if ($this->req->hasFile('medical_file')) {
                $path = $this->req->file('medical_file')->store('medical_records', 'public');
                DocumentationFile::create([
                    'user_id' => $user->id,
                    'file_path' => $path,
                    'file_type' => $this->req->file('medical_file')->getClientOriginalExtension(),
                    'description' => $this->req->description
                ]);
            }

            return $this->dataResponse($user);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function login()
    {
        try {
            $cred = $this->req->validate([
                'phone_number' => 'required|string',
                'password' => 'required|string'
            ]);

            $data = $this->TokenLogin([
                'model' => User::class,
                'credentials' => $cred,
                'oneTimeLogin' => true
            ]);

            return $this->dataResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function logout()
    {
        try {
            $this->TokenLogout($this->req->user());
            return $this->dataResponse(null, 'Logout successfully');
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function show()
    {
        try {
            return $this->dataResponse($this->req->user());
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function getDeviceHistory()
    {
        try {
            $devices = $this->req->user()
                ->tokens()
                ->select(['id', 'name', 'last_used_at', 'created_at'])
                ->orderBy('last_used_at', 'desc')
                ->get();

            return $this->dataResponse($devices);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function logoutDevice($tokenId)
    {
        try {
            $this->req->user()->tokens()->where('id', $tokenId)->delete();
            return $this->dataResponse(null, 'Logged out from device successfully');
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function logoutAllDevices()
    {
        try {
            $this->req->user()
                ->tokens()
                ->where('id', '!=', $this->req->user()->currentAccessToken()->id)
                ->delete();

            return $this->dataResponse(null, 'Logged out from all other devices');
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    // public function terminateAllDeviceTokens($userId){
    //     try {
    //         $userTokenData = $this->logAllDevices($userId , 5);

    //         return $this->paginationDataResponse($userTokenData);

    //     } catch (Exception $e) {
    //         return $this->handleException($e, $this->req);
    //     }
    // }
}
