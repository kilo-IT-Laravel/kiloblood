<?php

namespace App\Http\Controllers\Admin;

use App\Koobeni;
use App\Models\User;
use App\Services\UserManagment as ServicesUserManagment;
use Exception;

class UserManagment extends Koobeni
{
    private $userService;

    public function __construct() {
        $this->userService = new ServicesUserManagment();
    }

    public function index()
    {
        try {
            $data = $this->userService->getAllUsers();
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

    public function update(int $userId)
    {
        try {
            $data = $this->req->validate([
                'name' => 'nullable|string|max:255',
                'phone_number' => 'nullable|string|unique:users,phone_number,' . $userId,
                'blood_type' => 'nullable|string',
                'location' => 'nullable|string',
                'available_for_donation' => 'boolean'
            ]);

            $user = User::findOrFail($userId);
            $updateUser = $this->userService->update($user , $data);

            return $this->dataResponse($updateUser);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function deleteUser(int $userId)
    {
        try {
            $user = User::findOrFail($userId);
            $this->userService->delete($user);
            // $this->logService->log(Auth::id(), 'soft_deleted', User::class, $user->id);
            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function trashUserManagement()
    {
        try {
            $data = $this->userService->getAllUsers(true);
            return $this->paginationDataResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function restoreUser(int $userId)
    {
        try {
            $user = User::withTrashed()->findOrFail($userId);
            $this->userService->restore($user);
            // $this->logService->log(Auth::id(), 'restored', User::class, $user->id);
            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function permenantDeleteUser(int $userId)
    {
        try {
            $user = User::withTrashed()->findOrFail($userId);
            $this->userService->forceDelete($user);
            // $this->logService->log(Auth::id(), 'permenant_deleted', User::class, $user->id);
            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }
}
