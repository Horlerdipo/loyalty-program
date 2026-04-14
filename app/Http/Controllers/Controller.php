<?php

namespace App\Http\Controllers;

use App\DTOs\BaseResponseDto;
use Illuminate\Http\JsonResponse;

abstract class Controller
{
    public function errorResponse(BaseResponseDto $responseDto): JsonResponse
    {
        $data = [
            'message' => $responseDto->message,
        ];

        if (! empty($responseDto->data)) {
            $data['details'] = $responseDto->data;
        }

        return response()->json($data, $responseDto->statusCode);
    }
}
