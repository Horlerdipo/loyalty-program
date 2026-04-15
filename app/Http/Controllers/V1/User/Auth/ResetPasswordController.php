<?php

namespace App\Http\Controllers\V1\User\Auth;

use App\Actions\User\Auth\ResetPassword;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\User\Auth\ResetPasswordRequest;
use Symfony\Component\HttpFoundation\Response;

class ResetPasswordController extends Controller
{
    public function __invoke(ResetPasswordRequest $request): Response
    {
        $validatedData = $request->validated();
        $actionResponse = (new ResetPassword)->execute($validatedData['email'], $validatedData['password'], $validatedData['token']);
        if (! $actionResponse->status) {
            return $this->errorResponse($actionResponse);
        }

        return response()->noContent();
    }
}
