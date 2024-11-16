<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

abstract class BaseResponse
{
    protected $status;
    protected $message;
    protected $data;
    protected $statusCode;

    public function __construct($data = null, string $message = '', int $statusCode = 200)
    {
        $this->data = $data;
        $this->message = $message;
        $this->statusCode = $statusCode;
    }

    abstract public function toResponse(): JsonResponse;

    protected function formatResponse(): array
    {
        $response = [
            'status' => $this->status,
            'message' => $this->message
        ];

        if (!is_null($this->data)) {
            $response['data'] = $this->data;
        }

        return $response;
    }
} 