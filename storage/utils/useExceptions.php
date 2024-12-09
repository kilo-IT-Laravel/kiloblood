<?php

namespace Storage\utils;

trait useExceptions
{
    public function Validation($errors, $message = 'Validation failed')
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, 422);
    }

    public function ModelNotFound($message = 'Resource not found')
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 404);
    }

    public function QueryException($message = 'Database error')
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 500);
    }

    public function Forbidden($message = 'Forbidden actions')
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], 403);
    }
}
