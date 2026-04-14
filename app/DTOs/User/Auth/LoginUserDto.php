<?php

namespace App\DTOs\User\Auth;

use App\DTOs\ValidatedDto;
use App\Exceptions\DtoValidationErrorException;

readonly class LoginUserDto extends ValidatedDto
{
    public function __construct(
        public string $email,
        public string $password,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     *
     * @throws DtoValidationErrorException
     */
    public static function fromArray(array $data): self
    {
        self::validate($data, ['email', 'password']);

        return new self(
            $data['email'],
            $data['password']
        );
    }
}
