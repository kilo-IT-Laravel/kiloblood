<?php

namespace App\Http\Controllers\Auth;

use App\Koobeni;
use App\Models\BloodRequest;
use App\Models\BloodRequestDonor;
use App\Models\File;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;

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
                'avatar' => 'required|file|mimes:jpeg,png,jpg|max:2048',
                'medical_file' => 'required|file|mimes:pdf,doc,docx|max:10248',
                'role' => 'required|in:user,doctor',
                'description' => 'nullable|string'
            ]);

            $user = $this->TokenRegister([
                'model' => User::class,
                'credentials' => $cred
            ]);

            if($this->req->hasFile('avatar')){
                $file = $this->fileService->uploading('avatar' , 'pf_img');
                $user->file_id = $file->id;
            }

            if ($this->req->hasFile('medical_file')) {
                $file =$this->fileService->uploading('medical_file', 'medical_records');
                $user->medical_file_id = $file->id;
            }

            $user->save();

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
            $data = $this->req->user();
            return $this->dataResponse([
                'id' => $data->id,
                'name' => $data->name,
                'avatar' => $data->image,
                'phone_number' => $data->phone_number,
                'location' => $data->location,
                'trusted' => $data->trusted_at,
                'blood_type' => $data->blood_type,
                'available_for_donation' => $data->available_for_donation
            ]);
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

    public function getStats()
    {
        try {
            $bloodType = $this->req->user()->blood_type;

            $donationCount = BloodRequestDonor::where('donor_id', Auth::id())
                ->where('status', 'completed')
                ->count();

            $requestCount = BloodRequest::where('requester_id', Auth::id())
                ->count();

            return $this->dataResponse([
                'blood_type' => $bloodType,
                'donations_count' => $donationCount,
                'requests_count' => $requestCount
            ]);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function updateAvailability()
    {
        try {
            $this->req->user()->update([
                'available_for_donation' => $this->req->available
            ]);

            return $this->dataResponse(null, 'Availability updated');
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }
}
