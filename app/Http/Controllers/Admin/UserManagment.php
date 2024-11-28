<?php

namespace App\Http\Controllers\Admin;

use App\Koobeni;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;

class UserManagment extends Koobeni
{
    public function UserManagement()
    {
        try {
            $data = $this->findAll->allWithPagination([
                'model' => User::class,
                'sort' => 'latest',
                'perPage' => $this->req->perPage,
                'where' => [['role', '!=', 'doctor']],
                'search' => [
                    'name' => $this->req->name,
                    'location' => $this->req->location,
                    'blood_type' => $this->req->blood_type,
                    'phone_number' => $this->req->phone_number
                ],
                'dateRange' => [
                    'startDate' => $this->req->startDate,
                    'endDate' => $this->req->endDate
                ]
            ]);
            return $this->paginationDataResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function editUser(int $userId)
    {
        try {
            $user = User::findOrFail($userId);
            return $this->dataResponse($user);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function updateUser(int $userId)
    {
        try {
            $user = User::findOrFail($userId);
            $data = [

            ];

            
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function deleteUser(int $userId)
    {
        try {
            $user = User::findOrFail($userId);
            $user->delete();
            $this->logService->log(Auth::id() , 'soft_deleted' , User::class , $user->id);
            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function trashUserManagement()
    {
        try {
            $data = $this->findAll->allWithPagination([
                'model' => User::class,
                'trash' => true,
                'sort' => 'latest',
                'perPage' => $this->req->perPage,
                'where' => [['role', '!=', 'doctor']],
                'search' => [
                    'name' => $this->req->name,
                    'location' => $this->req->location,
                    'blood_type' => $this->req->blood_type,
                    'phone_number' => $this->req->phone_number
                ],
                'dateRange' => [
                    'startDate' => $this->req->startDate,
                    'endDate' => $this->req->endDate
                ]
            ]);
            return $this->paginationDataResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function restoreUser(int $userId)
    {
        try {
            $user = User::withTrashed()->findOrFail($userId);
            $user->restore();
            $this->logService->log(Auth::id() , 'restored' , User::class , $user->id);
            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function permenantDeleteUser(int $userId)
    {
        try {
            $user = User::withTrashed()->findOrFail($userId);
            $user->forceDelete();
            $this->logService->log(Auth::id() , 'permenant_deleted' , User::class , $user->id);
            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }
}
