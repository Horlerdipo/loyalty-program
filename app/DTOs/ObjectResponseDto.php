<?php

namespace App\DTOs;

/**
 * @template T of object
 */
readonly class ObjectResponseDto
{
    /**
     * @param bool $status
     * @param int $statusCode
     * @param string $message
     * @param T $data
     */
    public function __construct(
        public bool $status,
        public int $statusCode,
        public string $message,
        public object $data,
    ) {}
}
