<?php

namespace App\DTOs;

readonly class BaseResponseDto
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(
        public bool $status,
        public int $statusCode,
        public string $message,
        public array $data,
    ) {}
}
