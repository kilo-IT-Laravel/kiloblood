<?php

namespace App\Http\Controllers\Mobile;

use App\Koobeni;
use App\Models\BloodRequest;
use App\Models\BloodRequestDonor;
use Exception;
use Illuminate\Support\Facades\Auth;

class RequesterController extends Koobeni
{
    public function confirmDonation(Int $requestId, Int $donorId)
    {
        try {

            $request = BloodRequest::findOrFail($requestId);
            $donor = BloodRequestDonor::findOrFail($donorId);

            if ($request->requester_id !== Auth::id()) {
                return $this->Forbidden('Unauthorized');
            }

            if ($donor->blood_request_id !== $request->id) {
                return $this->Forbidden('Invalid donor for this request');
            }

            if ($donor->status !== 'pending') {
                return $this->Forbidden('Can only confirm pending donations');
            }

            $donor->update(['status' => 'confirmed']);

            $request->increment('quantity_received', $donor->blood_amount);
            if ($request->quantity_received >= $request->quantity) {
                $request->update(['status' => 'completed']);
            }

            return $this->dataResponse(null, 'Donation confirmed successfully');
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function viewRequestDonors(int $requestId)
    {
        try {

            $request = BloodRequest::findOrFail($requestId);

            if ($request->requester_id !== Auth::id()) {
                return $this->Forbidden('Unauthorized');
            }

            $donors = $this->findAll->allWithPagination([
                'model' => BloodRequestDonor::class,
                'sort' => 'latest',
                'relations' => [
                    'donor:id,name,image,blood_type,location'
                ],
                'where' => [
                    ['blood_request_id', '=', $request->id],
                    ['status', '!=', 'rejected']
                ],
                'select' => [
                    'id',
                    'donor_id',
                    'blood_amount',
                    'medical_records',
                    'status',
                    'created_at'
                ],
                'perPage' => $this->req->perPage
            ]);

            return $this->paginationDataResponse($donors);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }
}
