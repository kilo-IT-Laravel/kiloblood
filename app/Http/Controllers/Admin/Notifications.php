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
            $data = $this->findAll->allWithLimit([
                'model' => BloodRequest::class,
                'sort' => 'latest',
                'select' => [
                    'id',
                    'requester_id',
                    'blood_type',
                    'status',
                    'created_at'
                ],
                'where' => [
                    ['created_at', '>=', now()->subDays(7)]
                ],
                'relations' => ['requester:id,name'],
                'limit' => $this->req->limit,
                'offset' => $this->req->offset
            ]);

            $mappedData = $data->map(function ($request) {
                return [
                    'id' => $request->id,
                    'message' => "{$request->requester->name} has requested {$request->blood_type} blood",
                    'status' => $request->status,
                    'created_at' => $request->created_at
                ];
            });

            return $this->dataResponse($mappedData);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }
}
