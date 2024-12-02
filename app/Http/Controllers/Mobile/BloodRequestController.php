<?php

namespace App\Http\Controllers\Mobile;

use App\Koobeni;
use App\Models\BloodRequest;
use App\Models\HiddenBloodRequest;
use Exception;
use Illuminate\Support\Facades\Auth;

class BloodRequestController extends Koobeni
{

    public function index()
    {
        try {
            $data = $this->findAll->allWithPagination([
                'model' => BloodRequest::class,
                'sort' => 'latest',
                'relations' => [
                    'requester:id,name,location,image',
                    'donors' => function ($q) {
                        $q->where('donor_id', Auth::id())
                            ->select('blood_request_id', 'status');
                    }
                ],
                'select' => [
                    'id',
                    'requester_id',
                    'blood_type',
                    'quantity',
                    'quantity_received',
                    'message',
                    'status',
                    'created_at'
                ],
                'where' => [
                    ['status', '=', 'pending'],
                    ['id', 'NOT IN', function ($q) {
                        $q->select('blood_request_id')
                            ->from('hidden_blood_requests')
                            ->where('user_id', Auth::id());
                    }]
                ],
                'perPage' => $this->req->perPage
            ]);

            $data->through(function ($request) {
                $request->has_offered = $request->donors->count() > 0;
                unset($request->donors);
                return $request;
            });

            return $this->paginationDataResponse($data);
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

            $data = BloodRequest::create([
                'requester_id' => Auth::id(),
                'blood_type' => $validated['blood_type'],
                'quantity' => $validated['quantity'],
                'quantity_received' => 0,
                'message' => $validated['message'],
                'status' => 'pending'
            ]);

            return $this->dataResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function myRequests()
    {
        try {
            $data = $this->findAll->allWithPagination([
                'model' => BloodRequest::class,
                'sort' => 'latest',
                'relations' => [
                    'donors' => function ($q) {
                        $q->with('donor:id,name,image,blood_type,location')
                            ->where('status', '!=', 'rejected');
                    }
                ],
                'where' => [
                    ['requester_id', '=', Auth::id()]
                ],
                'select' => [
                    'id',
                    'blood_type',
                    'quantity',
                    'quantity_received',
                    'message',
                    'status',
                    'created_at'
                ],
                'perPage' => $this->req->perPage
            ]);

            return $this->paginationDataResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function hideRequest(int $requestId)
    {
        try {

            $request = BloodRequest::findOrFail($requestId);

            HiddenBloodRequest::create([
                'user_id' => Auth::id(),
                'blood_request_id' => $request->id
            ]);

            return $this->dataResponse(null, 'Request hidden successfully');
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function cancelRequest(int $requestId)
    {
        try {

            $request = BloodRequest::findOrFail($requestId);

            if ($request->requester_id !== Auth::id()) {
                return $this->Forbidden('Unauthorized', 403);
            }

            if ($request->status !== 'pending') {
                return $this->Forbidden('Can only cancel pending requests');
            }

            $request->update(['status' => 'cancelled']);

            return $this->dataResponse(null, 'Request cancelled successfully');
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }
}
