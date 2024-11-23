<?php

namespace App\Http\Controllers;

use App\Koobeni;
use App\Models\User;
use Exception;

class test extends Koobeni
{
    public function bruh()
    {
        try {
            $data = $this->findAll->allWithPagination([
                'data'      => User::class,
                'sort'      => 'latest',
                'perPage'   => 5,
                'select'  => ['id', 'name', 'phone_number', 'location', 'image' , 'created_at'],
                'relations' => ['BloodReq' => function ($query) {
                    $query->select('requester_id','status');
                }],
                // 'where' => [['created_at', '=', '2024-11-21T16:45:55.000000Z']],
                'search'=>[
                    'name'=>$this->req->name
                ]
                // 'dateRange' => [
                //      'startDate' => '2024-11-21',
                //      'endDate' => '2024-11-22'
                // ]
            ]);
            return $this->paginationDataResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }
}
