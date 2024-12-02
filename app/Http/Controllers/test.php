<?php

namespace App\Http\Controllers;

use App\Hooks\HookService;
use App\Koobeni;
use App\Models\User;
use App\Services\TestService;
use Exception;
use Illuminate\Http\Request;

class test extends Koobeni
{
    private $test;

    public function __construct()
    {
        $this->test = new TestService();
    }

    public function bruh($id)
    {
        try {

            // $data = $this->test->testing();

            $data = User::findOrFail($id);

            return $this->dataResponse($data);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

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



    public function uploadSingle()
    {
        $this->req->validate([
            'file' => 'required|file|max:10240'
        ]);

        try {
            $result = $this->uploadFile($this->req->file('file'));

            return $this->dataResponse($result);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function uploadMultiple()
    {
        $this->req->validate([
            'files.*' => 'required|file|max:10240'
        ]);

        try {
            $results = $this->uploadFiles($this->req->file('files'));

            return $this->dataResponse($results);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function deleteFilebruh()
    {
        try {
            $this->req->validate([
                'path' => 'required|string'
            ]);

            $result = $this->deleteFile($this->req->path);

            return $this->dataResponse($result);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }

    public function deleteMultiple()
    {
        try {
            $this->req->validate([
                'paths' => 'required|array',
                'paths.*' => 'required|string'
            ]);

            $results = $this->deleteFiles($this->req->paths);

            return $this->dataResponse($results);
        } catch (Exception $e) {
            return $this->handleException($e, $this->req);
        }
    }
}
