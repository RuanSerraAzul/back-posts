<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ErrorResponse extends BaseResponse
{
    protected $status = 'error';
    protected $errors;

    public function __construct($message = '', int $statusCode = 400, $errors = null)
    {
        parent::__construct(null, $message, $statusCode);
        $this->errors = $errors;
    }

    public function toResponse(): JsonResponse
    {
        $response = $this->formatResponse();
        
        if (!is_null($this->errors)) {
            $response['errors'] = $this->errors;
        }

        return response()->json($response, $this->statusCode);
    }
} 