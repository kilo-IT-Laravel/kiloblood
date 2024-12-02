<?php

namespace App\Http\Controllers\Mobile;

use App\Koobeni;
use App\Models\BloodRequest;
use App\Models\BloodRequestDonor;
use Exception;
use Illuminate\Support\Facades\Auth;

class DonorController extends Koobeni
{
    public function acceptTheDonate(int $requestId)
    {
        try {

            $request = BloodRequest::findOrFail($requestId);

            $existing = BloodRequestDonor::where([
                'blood_request_id' => $request->id,
                'donor_id' => Auth::id()
            ])->first();

            if ($existing) {
                return $this->Forbidden('You have already offered to donate');
            }

            $validated = $this->req->validate([
                'blood_amount' => 'required|integer|min:1',
                'medical_records' => 'required|string'
            ]);

            BloodRequestDonor::create([
                'blood_request_id' => $request->id,
                'donor_id' => Auth::id(),
                'status' => 'pending',
                'blood_amount' => $validated['blood_amount'],
                'medical_records' => $validated['medical_records']
            ]);

            return $this->dataResponse(null, 'Offer sent successfully');
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function myDonations()
    {
        try {
            $donations = $this->findAll->allWithPagination([
                'model' => BloodRequestDonor::class,
                'sort' => 'latest',
                'relations' => [
                    'bloodRequest:id,blood_type,quantity,message,status',
                    'bloodRequest.requester:id,name,image,location'
                ],
                'where' => [
                    ['donor_id', '=', Auth::id()],
                    ['status', '!=', 'rejected']
                ],
                'select' => [
                    'id',
                    'blood_request_id',
                    'blood_amount',
                    'status',
                    'created_at'
                ],
                'perPage' => $this->req->perPage
            ]);

            return $this->paginationDataResponse($donations);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }
}
