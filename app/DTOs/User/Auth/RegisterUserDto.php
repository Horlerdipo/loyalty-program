<?php

namespace App\DTOs\User\Auth;

use App\DTOs\ValidatedDto;
use App\Exceptions\DtoValidationErrorException;

readonly class RegisterUserDto extends ValidatedDto
{
    public function __construct(
        public string $name,
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
        self::validate($data, ['name', 'email', 'password']);

        return new self(
            $data['name'],
            $data['email'],
            $data['password']
        );
    }
}
