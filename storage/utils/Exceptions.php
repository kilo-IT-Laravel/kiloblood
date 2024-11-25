<?php

namespace Storage\utils;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Database\QueryException;

trait Exceptions
{
    public function handleException($exception, $request)
    {
        $exceptionMap = [
            ValidationException::class => [
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => fn($exception) => $this->formatValidationError($exception->errors()),
            ],
            AuthenticationException::class => [
                'status' => 401,
                'message' => fn($exception) => $exception->getMessage() ?? 'Unauthenticated'
            ],
            ModelNotFoundException::class => [
                'status' => 404,
                'message' => 'Resource not found',
                // 'data' => fn($exception) => [],
            ],
            HttpException::class => [
                'status' => fn($exception) => $exception->getStatusCode(),
                'message' => fn($exception) => $exception->getMessage(),
                // 'data' => fn($exception) => [], /// i forgot this should be log instead or something we will check it out later
            ],
            QueryException::class => [
                'status' => 500,
                'message' => 'Database error',
                'errors' => fn($exception) => [
                    'error' => $exception->getMessage(),
                    'sql' => $exception->getSql(),
                ],
            ]
        ];

        foreach ($exceptionMap as $key => $value) {
            if ($exception instanceof $key) {
                return $this->generateResponse($value, $exception);
            }
        }

        Log::error($exception);
        return response()->json([
            'success' => false,
            'message' => 'An internal error occurred',
        ], 500);
    }

    private function generateResponse(array $responseConfig, $exception)
    {
        $status = is_callable($responseConfig['status']) ? $responseConfig['status']($exception) : $responseConfig['status'];
        $message = is_callable($responseConfig['message']) ? $responseConfig['message']($exception) : $responseConfig['message'];
        $errors = isset($responseConfig['errors']) && is_callable($responseConfig['errors']) 
                    ? $responseConfig['errors']($exception) 
                    : null;

        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }

    private function formatValidationError(array $errors){
        $formattedErrors = [];
        foreach($errors as $key => $value){
            $formattedErrors[$key] = $value[0];
        }
        return $formattedErrors;
    }
}
