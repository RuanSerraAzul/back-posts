<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class SuccessResponse extends BaseResponse
{
    protected $status = 'success';

    public function toResponse(): JsonResponse
    {
        return response()->json($this->formatResponse(), $this->statusCode);
    }
} 