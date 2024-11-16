<?php

namespace App\Traits;

use App\Http\Responses\ErrorResponse;
use App\Http\Responses\SuccessResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

trait ApiResponse
{
    /**
     * Retorna uma resposta de sucesso
     */
    protected function success(mixed $data = null, string $message = 'Operação realizada com sucesso', int $statusCode = 200): JsonResponse
    {
        return (new SuccessResponse($data, $message, $statusCode))->toResponse();
    }

    /**
     * Retorna uma resposta de erro
     */
    protected function error(string $message = 'Erro na operação', int $statusCode = 400, mixed $errors = null): JsonResponse
    {
        return (new ErrorResponse($message, $statusCode, $errors))->toResponse();
    }

    /**
     * Retorna uma resposta de erro de validação
     */
    protected function validationError(ValidationException $exception): JsonResponse
    {
        return $this->error(
            'Erro de validação',
            422,
            $exception->errors()
        );
    }

    /**
     * Retorna uma resposta não encontrado
     */
    protected function notFound(string $message = 'Recurso não encontrado'): JsonResponse
    {
        return $this->error($message, 404);
    }

    /**
     * Retorna uma resposta não autorizado
     */
    protected function unauthorized(string $message = 'Não autorizado'): JsonResponse
    {
        return $this->error($message, 401);
    }
} 