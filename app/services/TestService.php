<?php

namespace App\Services;

use App\Models\User;

class TestService extends BaseService
{
    public function testing()
    {
        return $this->findAll->allWithPagination([
            'model' => User::class,
            'sort' => [
                'created_at',
                'asc'
            ],
            'perPage' => 5,
            'select' => ['id', 'name', 'phone_number', 'location', 'image', 'created_at'],
            'relations' => ['bloodRequests' => function ($query) {
                $query->select('requester_id', 'status');
            }],
            // 'where' => [['created_at', '=', '2024-11-21T16:45:55.000000Z']],
            'search' => [
                'name' => $this->req->name,
            ],
            // 'dateRange' => [
            //      'startDate' => '2024-11-21',
            //      'endDate' => '2024-11-22'
            // ]
        ]);
    }
}
