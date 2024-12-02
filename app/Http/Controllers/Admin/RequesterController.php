<?php

namespace App\Http\Controllers\Admin;

use App\Koobeni;
use App\Models\BloodRequest;
use App\Models\User;
use Exception;

class RequesterController extends Koobeni
{

    public function getAllRequesters()
    {
        try {
            $data = $this->findAll->allWithPagination([
                'model' => User::class,
                'sort' => ['blood_type', 'asc'],
                'select' => ['blood_type'],
                'where' => [['role', '!=', 'doctor']],
                'rawSelects' => [
                    'COUNT(*) as user_count',
                    'COUNT(CASE WHEN available_for_donation = true THEN 1 END) as available_donors',
                    '(SELECT COUNT(*) FROM blood_requests WHERE blood_requests.blood_type = users.blood_type) as request_count',
                    '(SELECT COUNT(*) FROM blood_requests WHERE blood_requests.blood_type = users.blood_type AND status = "completed") as completed_count',
                    '(SELECT COUNT(*) FROM blood_requests WHERE blood_requests.blood_type = users.blood_type AND status = "pending") as pending_count'
                ],
                'groupBy' => 'blood_type',
                'perPage' => $this->req->perPage,
                'search' => [
                    'blood_type' => $this->req->search
                ]
            ]);
            return $this->paginationDataResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function userRelatedRequest(string $bloodType)
    {
        try {
            $data = $this->findAll->allWithPagination([
                'model' => BloodRequest::class,
                'sort' => 'latest',
                'relations' => [
                    'requester:id,name,image',
                ],
                'perPage' => $this->req->perPage,
                'where' => [
                    ['blood_type', '=', $bloodType]
                ],
                'select' => [
                    'id',
                    'requester_id',
                    'quantity',
                    'status',
                ],
                'search' => [
                    'requester.name' => $this->req->search,
                    'status' => $this->req->status
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

    public function getUserRequestDetails(int $requesterId)
    {
        try {
            $data = BloodRequest::findOrFail($requesterId)
                ->load([
                    'requester:id,name,phone_number,blood_type,location,image',
                    'donors:id,blood_request_id,donor_id,status,medical_records,blood_amount',
                    'donors.donor:id,name,phone_number,blood_type,location,image'
                ]);
            return $this->dataResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function delete(int $requesterId)
    {
        try {
            $query = BloodRequest::findOrFail($requesterId);
            $query->delete();
            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function getTrashed()
    {
        try {

            $data = $this->findAll->allWithPagination([
                'model' => BloodRequest::class,
                'sort' => 'latest',
                'relations' => [
                    'requester:id,name,image',
                ],
                'perPage' => $this->req->perPage,
                'trash' => true,
                'select' => [
                    'id',
                    'requester_id',
                    'quantity',
                    'status',
                ],
                'search' => [
                    'requester.name' => $this->req->search,
                    'status' => $this->req->status
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

    public function restore(int $requesterId)
    {
        try {
            $query = BloodRequest::withTrashed()->findOrFail($requesterId);
            $query->restore();
            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function forceDelete(int $requesterId)
    {
        try {
            $query = BloodRequest::withTrashed()->findOrFail($requesterId);
            $query->forceDelete();
            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }
}
