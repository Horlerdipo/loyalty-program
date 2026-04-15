<?php

namespace App\Http\Controllers\User\Auth;

use App\Actions\User\Auth\SendForgotPasswordToken;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForgotPasswordController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $validatedData = $request->validate($this->validations());

        $actionResponse = (new SendForgotPasswordToken)->execute($validatedData['email']);
        if (! $actionResponse->status) {
            return $this->errorResponse($actionResponse);
        }

        return response()->noContent();
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function validations(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
        ];
    }
}
