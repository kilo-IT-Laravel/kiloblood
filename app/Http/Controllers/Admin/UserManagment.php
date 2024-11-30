<?php

namespace App\Http\Controllers\Admin;

use App\Koobeni;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserManagment extends Koobeni
{
    public function index()
    {
        try {
            $data = $this->findAll->allWithPagination([
                'model' => User::class,
                'sort' => 'latest',
                'perPage' => $this->req->perPage,
                'select' => [
                    'id',
                    'name',
                    'phone_number',
                    'blood_type',
                    'location',
                    'image',
                    'available_for_donation',
                    'created_at'
                ],
                'where' => [
                    ['role', '!=', 'doctor']
                ],
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

    public function update(int $userId)
    {
        try {
            $user = User::findOrFail($userId);

            $data = $this->req->validate([
                'name' => 'nullable|string|max:255',
                'phone_number' => 'nullable|string|unique:users,phone_number,' . $userId,
                'blood_type' => 'nullable|string',
                'location' => 'nullable|string',
                'available_for_donation' => 'boolean'
            ]);

            if ($this->req->hasFile('image')) {
                if ($user->image) {
                    Storage::disk('public')->delete($user->image);
                }
                $data['image'] = $this->req->file('image')->store('users', 'public');
            }

            $user->update($data);

            return $this->dataResponse($user);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function deleteUser(int $userId)
    {
        try {
            $user = User::findOrFail($userId);
            $user->delete();
            $this->logService->log(Auth::id(), 'soft_deleted', User::class, $user->id);
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
                'select' => [
                    'id',
                    'name',
                    'phone_number',
                    'blood_type',
                    'location',
                    'image',
                    'available_for_donation',
                    'created_at',
                    'deleted_at'
                ],
                'where' => [
                    ['role', '!=', 'doctor']
                ],
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
            $this->logService->log(Auth::id(), 'restored', User::class, $user->id);
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
            // $this->logService->log(Auth::id(), 'permenant_deleted', User::class, $user->id);
            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }
}
