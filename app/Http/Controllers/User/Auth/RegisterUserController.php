<?php

namespace App\Http\Controllers\User\Auth;

use App\Actions\User\Auth\RegisterUser;
use App\DTOs\User\Auth\RegisterUserDto;
use App\Exceptions\DtoValidationErrorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Auth\RegisterUserRequest;
use Symfony\Component\HttpFoundation\Response;

class RegisterUserController extends Controller
{
    /**
     * @throws DtoValidationErrorException
     */
    public function __invoke(RegisterUserRequest $request): Response
    {
        $actionResponse = (new RegisterUser)->execute(RegisterUserDto::fromArray($request->validated()));
        if (! $actionResponse->status) {
            return $this->errorResponse($actionResponse);
        }

        return response()->noContent();
    }
}
