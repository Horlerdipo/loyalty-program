<?php

namespace App\DTOs;

use App\Exceptions\DtoValidationErrorException;

readonly class ValidatedDto
{
    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, string>  $items
     *
     * @throws DtoValidationErrorException
     */
    protected static function validate(array $data, array $items): void
    {
        foreach ($items as $item) {
            if (! isset($data[$item])) {
                throw new DtoValidationErrorException("$item is required in ".self::class);
            }
        }
    }
}
