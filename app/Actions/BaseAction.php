<?php

namespace App\Actions;

use App\DTOs\BaseResponseDto;
use App\DTOs\ObjectResponseDto;
use Illuminate\Support\Facades\Log;

class BaseAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function successResponse(string $message, array $data = []): BaseResponseDto
    {
        return new BaseResponseDto(
            status: true,
            statusCode: 200,
            message: $message,
            data: $data
        );
    }

    /**
     * @param  array<mixed>  $data
     */
    public function errorResponse(string $message, int $statusCode, array $data = []): BaseResponseDto
    {
        return new BaseResponseDto(
            status: false,
            statusCode: $statusCode,
            message: $message,
            data: $data,
        );
    }

    public function handleException(\Exception $exception, ?string $message = null): BaseResponseDto
    {
        Log::error($exception->getMessage(), [
            'exception' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ]);

        return new BaseResponseDto(
            status: false,
            statusCode: 500,
            message: is_null($message) ? 'Something went wrong, please try again later' : $message,
            data: []
        );
    }

    /**
     * @template T of object
     *
     * @param  T|null  $data
     * @return ObjectResponseDto<T>
     */
    public function objectResponse(bool $status, string $message, int $statusCode = 200, ?object $data = null): ObjectResponseDto
    {
        /**
         * @phpstan-ignore-next-line
         */
        return new ObjectResponseDto(
            status: $status,
            statusCode: $statusCode,
            message: $message,
            data: is_object($data) ? $data : (object) [],
        );
    }
}
