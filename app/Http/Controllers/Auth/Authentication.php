<?php

namespace App\Http\Controllers\Auth;

use App\Koobeni;
use App\Models\User;
use Exception;

class Authentication extends Koobeni {

    public function register(){
        try{
            $cred = $this->req->validate([
                'name' => 'required|string',
                'phone_number' => 'required|string|unique:users,phone_number',
                'password' => 'required|string|confirmed',
                'blood_type' => 'required|string',
                'location' => 'required|string',
                'role' => 'required|string'
            ]);

            $user = $this->TokenRegister($cred , User::class , false , null);

            return $this->dataResponse($user);

        }catch(Exception $e){
            return $this->handleException($e , $this->req);
        }
    }

    public function login(){
        try{
            $cred = $this->req->validate([
                'phone_number' => 'required|string',
                'password' => 'required|string'
            ]);

            $data = $this->TokenLogin($cred , User::class , null , true);

            return $this->dataResponse($data);
        }catch(Exception $e){
            return $this->handleException($e , $this->req);
        }
    }

    public function logout(){
        try{
            $this->TokenLogout($this->req->user());
            return $this->dataResponse(null,'Logout successfully');
        }catch(Exception $e){
            return $this->handleException($e , $this->req);
        }
    }

    public function show(){
        try{
            return $this->dataResponse($this->req->user());
        }catch(Exception $e){
            return $this->handleException($e , $this->req);
        }
    }

    public function terminateAllDeviceTokens($userId){
        try {
            $userTokenData = $this->logAllDevices($userId , 5);

            return $this->paginationDataResponse($userTokenData);
    
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }
}