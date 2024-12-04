<?php

namespace App\Services;

use App\Models\BloodRequestDonor;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UserManagment extends BaseService
{
    const MINIMUM_DONATIONS = 5;
    const MINIMUM_DAYS_ACTIVE = 30;
    const MINIMUM_COMPLETION_RATE = 0.8;

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

    public function isEligibleForTrust(User $user)
    {
        $accountAge = $user->created_at->diffInDays(now());
        if ($accountAge < self::MINIMUM_DAYS_ACTIVE) {
            return false;
        }

        $completedDonations = BloodRequestDonor::where('donor_id', $user->id)
            ->where('status', 'completed')
            ->count();

        if ($completedDonations < self::MINIMUM_DONATIONS) {
            return false;
        }

        $totalRequests = BloodRequestDonor::where('donor_id', $user->id)->count();
        $completionRate = $totalRequests > 0 ? $completedDonations / $totalRequests : 0;

        return $completionRate >= self::MINIMUM_COMPLETION_RATE;
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
