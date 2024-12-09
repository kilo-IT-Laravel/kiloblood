<?php

namespace App\Http\Controllers\Admin;

use App\Koobeni;
use App\Models\BloodRequest;
use Exception;

class Notifications extends Koobeni
{
    public function index()
    {
        try {
            $data = $this->findAll->allWithPagination([
                'model' => BloodRequest::class,
                'sort' => 'latest',
                'select' => [
                    'id',
                    'donor_id',
                    'blood_type',
                    'status',
                    'created_at'
                ],
                'where' => [
                    ['created_at', '>=', now()->subDays(7)]
                ],
                'relations' => ['requester:id,name'],
                'perPage' => $this->req->perPage,
            ]);

            $data->getCollection()->transform(function ($item) {
                return [
                    'id' => $item->id,
                    'message' => "{$item->requester->name} has requested {$item->blood_type} blood",
                    'status' => $item->status,
                    'created_at' => $item->created_at
                ];
            });

            return $this->paginationDataResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }
}
