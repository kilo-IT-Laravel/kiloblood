<?php

namespace Storage\utils;

trait CustomResponse
{
    public function dataResponse($data = null, $message = 'Successfully')
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data ? $this->recursiveUpdateImageUrls($data) : $data,
        ], 200);
    }

    public function tokenResponse($data = null, $message = 'Successfully'){
        return response()->json([
            'success' => true,
            'message' => $message,
            'token' => $data ? $this->recursiveUpdateImageUrls($data) : $data,
        ], 200);
    }

    public function paginationResponse($data)
    {
        return [
            'current_page' => $data->currentPage(),
            'page_size' => $data->perPage(),
            'total_items' => $data->total(),
            'total_pages' => $data->lastPage(),
        ];
    }

    public function paginationDataResponse($data, $message = 'Successfully')
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $this->recursiveUpdateImageUrls($data->items()),
            'pagination' => $this->paginationResponse($data),
        ], 200);
    }

    public function createdResponse($data, $message = 'Successfully')
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if($data !== null) {
            $response['data'] = $this->recursiveUpdateImageUrls($data);
        }

        return response()->json($response, 201);
    }
}
