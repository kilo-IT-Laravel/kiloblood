<?php

namespace App\Http\Controllers\Mobile;

use App\Koobeni;
use App\Models\BloodRequest;
use App\Models\BloodRequestDonor;
use App\Models\DocumentationFile;
use App\Models\File;
use App\Models\Notification;
use Exception;
use Illuminate\Support\Facades\Auth;

class BloodRequestController extends Koobeni
{
    public function index() ///// donation rqeuests
    {
        try {
            $data = $this->findAll->allWithLimit([
                'model' => BloodRequest::class,
                'sort' => 'latest',
                'relations' => ['requester:id,name,location'],
                'select' => [
                    'id',
                    'requester_id',
                    'blood_type',
                    'quantity',
                    'message',
                    'status',
                    'created_at'
                ],
                'where' => [
                    ['requester_id', '!=', Auth::id()],
                    ['status', '!=', 'rejected']
                ],
                'limit' => $this->req->perPage,
                'offset' => $this->req->offset
            ]);
            return $this->dataResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function show(int $requestId)
    {
        try {
            $request = BloodRequest::with([
                'requester:id,name,location,blood_type',
                'documentationFile'
            ])->findOrFail($requestId);

            return $this->dataResponse([
                'request' => $request,
                'can_donate' => !BloodRequestDonor::where([
                    'blood_request_id' => $requestId,
                    'donor_id' => Auth::id()
                ])->exists()
            ]);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function store()
    {
        try {
            $validated = $this->req->validate([
                'blood_type' => 'required|string',
                'quantity' => 'required|integer|min:1',
                'message' => 'required|string'
            ]);

            $request = BloodRequest::create([
                'requester_id' => Auth::id(),
                'blood_type' => $validated['blood_type'],
                'quantity' => $validated['quantity'],
                'message' => $validated['message']
            ]);

            return $this->dataResponse($request);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function viewMyRequestDonors() ///// request donations 
    {
        try {
            $data = $this->findAll->allWithLimit([
                'model' => BloodRequestDonor::class,
                'sort' => 'latest',
                'relations' => [
                    'donor:id,name,location,blood_type',
                    'bloodRequest:id,blood_type,quantity'
                ],
                'select' => [
                    'id',
                    'blood_request_id',
                    'donor_id',
                    'status',
                    'medical_records',
                    'blood_amount',
                    'created_at'
                ],
                'where' => [
                    ['blood_request_id', '=', function ($q) {
                        return $q->select('id')
                            ->from('blood_requests')
                            ->where('requester_id', Auth::id());
                    }],
                    ['status', '!=', 'cancelled']
                ],
                'limit' => $this->req->perPage,
                'offset' => $this->req->offset
            ]);
            return $this->dataResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function viewMyDonorDetails(int $donorId)
    {
        try {
            $donorRequest = BloodRequestDonor::with([
                'donor:id,name,location,blood_type',
                'bloodRequest:id,blood_type,quantity,message',
                'documentationFile'
            ])->findOrFail($donorId);

            $request = BloodRequest::findOrFail($donorRequest->blood_request_id);
            if ($request->requester_id !== Auth::id()) {
                return $this->error('Unauthorized', 403);
            }

            return $this->dataResponse($donorRequest);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }
    public function donate(int $requestId, int $docId)
    {
        try {
            $request = BloodRequest::findOrFail($requestId);

            $validated = $this->req->validate([
                'blood_amount' => 'required|integer|min:1',
                'medical_records' => 'required|string',
                'medical_file' => 'required|file|mimes:pdf,doc,docx|max:10248',
                'url' => 'required|string'
            ]);

            $donor = BloodRequestDonor::create([
                'blood_request_id' => $request->id,
                'donor_id' => Auth::id(),
                'status' => 'pending',
                'blood_amount' => $validated['blood_amount'],
                'medical_records' => $validated['medical_records']
            ]);

            if ($this->req->hasFile('medical_file')) {
                $path = $this->req->file('medical_records')->store('medical_records', 'public');
                $query = File::findOrFail($docId);
                $query->update([
                    'user_id' => Auth::id(),
                    'blood_request_donor_id' => $donor->donor_id,
                    'file_path' => $path,
                    'file_type' => $this->req->file('medical_file')->getClientOriginalExtension()
                ]);
            }

            Notification::create([
                'user_id' => $request->requester_id,
                'message' => 'Someone has offered to donate blood for your request',
                'url' => $this->req->url
            ]);

            return $this->dataResponse(null, 'Donation offer sent');
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function MedicalRecords()
    {
        try {
            $data = $this->findAll->allWithPagination([
                'model' => File::class,
                'sort' => 'latest',
                'perPage' => $this->req->perPage,
                'dateRange' => [
                    'startDate' => $this->req->startDate,
                    'endDate' => $this->req->endDate
                ],
                'select' => [
                    'id',
                    'user_id',
                    'file_path',
                    'file_type',
                    'description'
                ],
                'where' => [
                    ['user_id', '=', function ($q) {
                        return $q->select('id')
                            ->from('users')
                            ->where('id', Auth::id());
                    }]
                ]
            ]);
            return $this->paginationDataResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function acceptDonor(int $donorId)
    {
        try {

            $this->req->validate([
                'url' => 'required|string'
            ]);

            $donor = BloodRequestDonor::findOrFail($donorId);
            $request  = BloodRequest::findOrFail($donor->blood_request_id);

            if ($donor->status !== 'pending') {
                return $this->error('Can only accept pending donations');
            }

            $donor->update(['status' => 'completed']);

            $totalReceivedAmount = BloodRequestDonor::where('blood_request_id', $request->id)
                ->where('status', 'completed')
                ->sum('blood_amount');

            if ($totalReceivedAmount >= $request->quantity) {
                $request->update(['status' => 'accepted']);
            }

            Notification::create([
                'user_id' => $donor->donor_id,
                'message' => 'Your recent donation has been successfully processed',
                'url' => $this->req->url
            ]);

            return $this->dataResponse(null, 'Donation completed');
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function cancelDonation(int $donorId)
    {
        try {
            $donor = BloodRequestDonor::findOrFail($donorId);
            $donor->update(['status' => 'cancelled']);
            return $this->dataResponse(null, 'Donation cancelled');
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function rejectRequest(int $requestId)
    {
        try {
            $request = BloodRequest::findOrFail($requestId);
            $request->update(['status' => 'rejected']);
            return $this->dataResponse(null, 'Request rejected');
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function requestDonationReport()
    {
        try {
            $data = $this->findAll->allWithLimit([
                'model' => BloodRequestDonor::class,
                'sort' => 'latest',
                'relations' => [
                    'donor:id,name,location,blood_type',
                    'bloodRequest:id,blood_type,quantity'
                ],
                'select' => [
                    'id',
                    'blood_request_id',
                    'donor_id',
                    'status',
                    'blood_amount',
                    'created_at'
                ],
                'where' => [
                    ['blood_request_id', '=', function ($q) {
                        $q->select('id')
                            ->from('blood_requests')
                            ->where('requester_id', Auth::id());
                    }]
                ],
                'limit' => $this->req->perPage,
                'offset' => $this->req->offset
            ]);
            return $this->dataResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function donationRequestReport()
    {
        try {
            $data = $this->findAll->allWithLimit([
                'model' => BloodRequestDonor::class,
                'sort' => 'latest',
                'relations' => [
                    'bloodRequest:id,blood_type,quantity,requester_id',
                    'bloodRequest.requester:id,name,location'
                ],
                'select' => [
                    'id',
                    'blood_request_id',
                    'status',
                    'blood_amount',
                    'created_at'
                ],
                'where' => [
                    ['donor_id', '=', Auth::id()]
                ],
                'limit' => $this->req->perPage,
                'offset' => $this->req->offset
            ]);
            return $this->dataResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }
}
