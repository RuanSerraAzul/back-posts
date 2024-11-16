<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'author' => 'sometimes|string|max:255',
            'tags' => 'sometimes|array',
            'tags.*' => 'string'
        ];
    }

    public function messages(): array
    {
        return [
            'title.max' => 'O título não pode ter mais que 255 caracteres',
            'author.max' => 'O nome do autor não pode ter mais que 255 caracteres',
            'tags.array' => 'As tags devem ser um array',
            'tags.*.string' => 'Cada tag deve ser uma string',
        ];
    }
} 