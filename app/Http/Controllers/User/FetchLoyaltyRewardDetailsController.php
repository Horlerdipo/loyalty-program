<?php

namespace App\Http\Controllers\User;

use App\Actions\User\FetchLoyaltyRewardDetails;
use App\DTOs\BaseResponseDto;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FetchLoyaltyRewardDetailsController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $actionResponse = (new FetchLoyaltyRewardDetails)->execute(strval(Auth::guard('sanctum')->id()));

        if ($actionResponse instanceof BaseResponseDto) {
            return $this->errorResponse($actionResponse);
        }

        return response()->json($actionResponse->data->toArray());
    }
}
