<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UserManagment extends BaseService
{

    public function getAllUsers($withTrashed = false)
    {
        return $this->findAll->allWithPagination([
            'model' => User::class,
            'trash' => $withTrashed,
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
    }

    public function update(User $user, array $data)
    {
        if ($this->req->hasFile('image')) {
            $this->deleteImage($user->image);
            $data['image'] = $this->uploadImage($this->req->file('image'));
        }

        $user->update($data);
        return $user->fresh();
    }

    public function delete(User $user)
    {
        $user->delete();
        return true;
    }

    public function restore(User $user)
    {
        $user->restore();
        return true;
    }

    public function forceDelete(User $user)
    {
        $this->deleteImage($user->image);
        $user->forceDelete();
        return true;
    }

    private function uploadImage($image)
    {
        return $image->store('users', 'public');
    }

    private function deleteImage($image)
    {
        if ($image) {
            Storage::disk('public')->delete($image);
        }
    }
}
