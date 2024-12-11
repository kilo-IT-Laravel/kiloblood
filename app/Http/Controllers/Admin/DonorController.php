<?php

namespace App\Http\Controllers\Admin;

use App\Koobeni;
use App\Models\BloodRequestDonor;
use Exception;

class DonorController extends Koobeni
{

    public function viewDonors()
    {
        try {
            $data = $this->findAll->allWithPagination([
                'model' => BloodRequestDonor::class,
                'sort' => 'latest',
                'relations' => [
                    'donor:id,name,avatar,blood_type,location',
                    'bloodRequest:id,blood_type,quantity,status,created_at'
                ],
                'select' => [
                    'id',
                    'requester_id',
                    'blood_request_id',
                    'status',
                    'quantity',
                    'created_at'
                ],
                'search' => [
                    'donor.name' => $this->req->search,
                    'bloodRequest.blood_type' => $this->req->blood_type,
                    'status' => $this->req->status
                ],
                'dateRange' => [
                    'startDate' => $this->req->startDate,
                    'endDate' => $this->req->endDate
                ],
                'perPage' => $this->req->perPage
            ]);
            return $this->paginationDataResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function donorDetails(int $donorId)
    {
        try {
            $donor = BloodRequestDonor::with([
                'donor:id,name,avatar,blood_type,location,available_for_donation',
                'bloodRequest:id,blood_type,quantity,status,note,created_at',
                'bloodRequest.requester:id,name,image'
            ])->findOrFail($donorId);

            return $this->dataResponse($donor);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function trashed()
    {
        try {
            $data = $this->findAll->allWithPagination([
                'model' => BloodRequestDonor::class,
                'trash' => true,
                'sort' => 'latest',
                'relations' => [
                    'donor:id,name,avatar,blood_type',
                    'bloodRequest:id,blood_type,quantity,status'
                ],
                'select' => [
                    'id',
                    'requester_id',
                    'blood_request_id',
                    'status',
                    'quantity',
                    'created_at',
                    'deleted_at'
                ],
                'search' => [
                    'donor.name' => $this->req->search,
                    'bloodRequest.blood_type' => $this->req->blood_type,
                    'status' => $this->req->status
                ],
                'dateRange' => [
                    'startDate' => $this->req->startDate,
                    'endDate' => $this->req->endDate
                ],
                'perPage' => $this->req->perPage
            ]);
            return $this->paginationDataResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function delete(int $donorId)
    {
        try {
            $query = BloodRequestDonor::findOrFail($donorId);
            $query->delete();
            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function restore(int $donorId)
    {
        try {
            $query = BloodRequestDonor::withTrashed()->findOrFail($donorId);
            $query->restore();
            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function forceDelete(int $donorId)
    {
        try {
            $query = BloodRequestDonor::withTrashed()->findOrFail($donorId);
            $query->forceDelete();
            return $this->dataResponse(null);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }
}
