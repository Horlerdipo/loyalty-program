<?php

namespace App\Http\Controllers\V1\User\Auth;

use App\Actions\User\Auth\LogoutUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogoutUserController extends Controller
{
    public function __invoke(Request $request): Response
    {

        $actionResponse = (new LogoutUser)->execute(strval(Auth::id()));
        if (! $actionResponse->status) {
            return $this->errorResponse($actionResponse);
        }

        return response()->json(status: Response::HTTP_NO_CONTENT);
    }
}
