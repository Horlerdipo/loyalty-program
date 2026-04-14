<?php

namespace App\Http\Controllers\V1\User\Auth;

use App\Actions\User\Auth\LoginUser;
use App\DTOs\User\Auth\LoginUserDto;
use App\Exceptions\DtoValidationErrorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\User\Auth\LoginUserRequest;
use Symfony\Component\HttpFoundation\Response;

class LoginUserController extends Controller
{
    /**
     * @throws DtoValidationErrorException
     */
    public function __invoke(LoginUserRequest $request): Response
    {

        $actionResponse = (new LoginUser)->execute(
            LoginUserDto::fromArray($request->validated())
        );
        if (! $actionResponse->status) {
            return $this->errorResponse($actionResponse);
        }

        return response()->json($actionResponse->data);
    }
}
