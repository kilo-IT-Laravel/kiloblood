<?php

namespace App\Http\Controllers\Auth;

use App\Koobeni;
use App\Models\BloodRequest;
use App\Models\BloodRequestDonor;
use App\Models\DeviceToken;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
                'role' => 'required|in:user,doctor',
                'description' => 'nullable|string'
            ]);

            $user = $this->TokenRegister([
                'model' => User::class,
                'credentials' => $cred
            ]);

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

            $token = $this->TokenLogin([
                'model' => User::class,
                'credentials' => $cred,
                'oneTimeLogin' => true
            ]);

            return $this->dataResponse([ 'token' => $token]);
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
            $userId = $this->req->user()->id;


            $devices = DB::table('devices')
                ->where('user_id', $userId)
                ->select(['id', 'user_agent', 'ip', 'created_at', 'updated_at'])
                ->orderBy('updated_at', 'desc')
                ->get();

            $deviceNames = $devices->groupBy('user_agent');

            foreach ($deviceNames as $name => $group) {
                if ($group->count() > 1) {

                    $latestDevice = $group->first();

                    DB::table('devices')
                        ->where('user_id', $userId)
                        ->where('user_agent', $name)
                        ->where('id', '!=', $latestDevice->id)
                        ->delete();
                }
            }

            $updatedDevices = DB::table('devices')
                ->where('user_id', $userId)
                ->select(['id', 'user_agent', 'ip', 'created_at', 'updated_at'])
                ->orderBy('updated_at', 'desc')
                ->get();

            return $this->dataResponse($updatedDevices);
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

            $donationCount = BloodRequest::where('donor_id', Auth::id())
                ->where('status', 'completed')
                ->count();

            $requestCount = BloodRequest::where('donor_id', Auth::id())
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

    public function updateProfile()
    {
        try {
            $validate = $this->req->validate([
                'name' => 'nullable|string',
                'phone_number' => 'nullable|string|unique:users,phone_number',
                'location' => 'nullable|string',
                'blood_type' => 'nullable|string',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            if($this->req->hasFile('avatar')){
                Storage::disk('s3')->delete($this->req->avatar);
                $validate['avatar'] = $this->req->file('avatar')->store('avatars' , 's3');
            }

            $this->req->user()->update($validate);

            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function updatePassword()
    {
        try {
            $this->req->validate([
                'old_password' => 'required|string',
                'new_password' => 'required|string|confirmed'
            ]);

            if (!Hash::check($this->req->old_password, $this->req->user()->password)) {
                return $this->dataResponse(null, 'Old password is incorrect');
            }

            $this->req->user()->update([
                'password' => Hash::make($this->req->new_password)
            ]);

            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function deleteAccount()
    {
        try {
            $this->req->user()->delete();
            return $this->dataResponse(null, 'Account deleted');
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function device()
    {
        try{
            $this->req->validate([
                'device_token' => 'required|string',
                'device_name' => 'required|string'
            ]);

            $result = DeviceToken::create([
                'user_id' => $this->req->user()->id,
               'device_token' => $this->req->device_token,
               'device_name' => $this->req->device_name
            ]);

            return $this->dataResponse($result);
        }catch(Exception $e){
            return $this->handleException($e , $this->req);
        }
    }
}
