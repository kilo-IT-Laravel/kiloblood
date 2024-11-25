<?php

namespace App\Http\Controllers;

use App\Koobeni;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
class test extends Koobeni
{
    // public function bruh()
    // {
    //     try {
    //         $data = $this->findAll->allWithPagination([
    //             'data' => User::class,
    //             'sort' => [
    //                 'created_at' , 'asc'
    //             ],
    //             'perPage' => 5,
    //             'select' => ['id', 'name', 'phone_number', 'location', 'image', 'created_at'],
    //             'relations' => ['BloodReq' => function ($query) {
    //                 $query->select('requester_id', 'status');
    //             }],
    //             // 'where' => [['created_at', '=', '2024-11-21T16:45:55.000000Z']],
    //             'search' => [
    //                 'name' => $this->req->name
    //             ],
    //             // 'dateRange' => [
    //             //      'startDate' => '2024-11-21',
    //             //      'endDate' => '2024-11-22'
    //             // ]
    //         ]);
    //         return $this->paginationDataResponse($data);
    //     } catch (Exception $e) {
    //         return $this->handleException($e, $this->req);
    //     }
    // }

    // public function testbruh()
    // {
    //     try {
    //         $validatedData = $this->req->validate([
    //             'password' => 'required|string|strongPassword',
    //             'name' => 'required|string|max:255',
    //             'email' => 'nullable|email',
    //             'date' => 'required|date|validDateRange:2024-01-01,2024-12-31',
    //             'appointment_time' => 'required|validTimeRange:09:00,17:00',
    //             'dob' => 'required|date|minimumAge:18',
    //             'phone' => 'required|phoneNumber'
    //         ]);

    //         return $this->dataResponse($validatedData);
    //     } catch (Exception $e) {
    //         return $this->handleException($e, $this->req);
    //     }
    // }

    public function registerUserBusiness($data)
    {
        return [
            'success' => true,
            'data' => $data
        ];
    }

    public function testbruh()
    {
        try {
            $data = $this->req->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email',
                'password' => 'required|string|min:6',
            ]);

            $result = $this->aop->callMethodWithHooks($this, 'registerUserBusiness', [$data]);

            return response()->json($result);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }
}
