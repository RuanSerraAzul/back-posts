<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidTag implements Rule
{
    public function passes($attribute, $value): bool
    {
        return is_string($value) && 
               strlen($value) >= 2 && 
               strlen($value) <= 50 &&
               preg_match('/^[a-zA-Z0-9-_]+$/', $value);
    }

    public function message(): string
    {
        return 'A tag deve conter apenas letras, números, hífen e underscore, com tamanho entre 2 e 50 caracteres.';
    }
} 