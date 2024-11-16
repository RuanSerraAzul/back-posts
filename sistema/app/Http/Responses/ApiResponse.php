<?php

namespace App\Http\Responses;

abstract class ApiResponse
{
    protected $status;
    protected $message;
    protected $data;
    protected $statusCode;

    public function toArray()
    {
        return [
            'status' => $this->status,
            'message' => $this->message,
            'data' => $this->data
        ];
    }

    public function toResponse()
    {
        return response()->json($this->toArray(), $this->statusCode);
    }
} 