<?php

namespace App\Http\Requests;

use App\Traits\RequestValidation;
use Illuminate\Foundation\Http\FormRequest;

abstract class BaseRequest extends FormRequest
{
    use RequestValidation;

    public function authorize(): bool
    {
        return true;
    }
} 