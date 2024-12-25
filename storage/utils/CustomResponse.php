<?php

namespace Storage\utils;

trait CustomResponse
{
//    public function dataResponse($data = null, $message = 'Successfully')
//    {
//        $response = [
//            'success' => true,
//            'message' => $message,
//            'data' => $this->processImageUrls($data),
//        ];
//
//        return response()->json($response, 200);
//    }

    public function dataResponse($data = null, $message = 'Successfully')
    {
        try {
            $response = [
                'success' => true,
                'message' => $message,
                'data' => $this->processImageUrls($data), // Process image URLs
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            \Log::error('Image processing error: ' . $e->getMessage()); // Log the error
            return response()->json([
                'success' => false,
                'message' => 'Failed to process image URLs.',
            ], 500);
        }
    }

    public function LoginResponse($data, $message = 'Successfully')
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if($data !== null) {
            $response['data'] = [
                'token' => $data
            ];
        }

        return response()->json($response, 200);
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
            'data' => $this->processImageUrls($data->items()),
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
            $response['data'] = $data;
        }

        return response()->json($response, 201);
    }

//    private function processImageUrls($data)
//    {
//
//        if (is_object($data)) {
//
//            if (isset($data->image) && is_string($data->image)) {
//                $data->image = env('AWS_URL') . '/' . $data->image;
//            }
//        }
//        return $data;
//    }

    private function processImageUrls($data)
    {
        if ($data instanceof \Illuminate\Database\Eloquent\Model) {
            $data = $data->toArray();
        }

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    $data[$key] = $this->processImageUrls($value);
                } elseif (
                    is_string($value) &&
                    preg_match('/\.(jpg|png|jpeg)$/i', $value) &&
                    !str_starts_with($value, env('AWS_URL'))
                ) {
                    $data[$key] = env('AWS_URL') . '/' . $value;
                }
            }
        } elseif (is_object($data)) {
            foreach ($data as $key => $value) {
                $data->$key = $this->processImageUrls($value);
            }
        }

        return $data;
    }



}
