<?php

namespace App\Services;

use App\Models\BloodRequestDonor;

class DonorManagement
{
    public function getDonorNotiDetails($data)
    {
        $bloodRequestDonor = BloodRequestDonor::with([
            'bloodRequest:id,name,blood_type,location,quantity,note,status',
            'donor:id,name,blood_type,phone_number,location'
        ])->latest()->first();

        return [
            'notification' => [
                'id' => $data->id,
                'message' => $data->message,
                'time' => $data->created_at->diffForHumans(),
                'status' => $data->status
            ],
            'request_details' => [
                'name' => $bloodRequestDonor->bloodRequest->name,
                'blood_type' => $bloodRequestDonor->bloodRequest->blood_type,
                'location' => $bloodRequestDonor->bloodRequest->location,
                'quantity' => $bloodRequestDonor->bloodRequest->quantity,
                'note' => $bloodRequestDonor->bloodRequest->note,
                'status' => $bloodRequestDonor->bloodRequest->status
            ],
            'donor_details' => [
                'name' => $bloodRequestDonor->donor->name,
                'blood_type' => $bloodRequestDonor->donor->blood_type,
                'phone_number' => $bloodRequestDonor->donor->phone_number,
                'location' => $bloodRequestDonor->donor->location,
                'quantity_offered' => $bloodRequestDonor->quantity,
                'is_confirmed' => $bloodRequestDonor->is_confirmed
            ]
        ];
    }
}
