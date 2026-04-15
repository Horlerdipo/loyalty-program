<?php

namespace App\Http\Controllers\V1\User;

use App\Actions\User\PurchaseItem;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseItemController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validatedReq = $request->validate($this->validations());

        $actionResponse = (new PurchaseItem)->execute(strval(Auth::id()), floatval($validatedReq['amount']));

        if (! $actionResponse->status) {
            return $this->errorResponse($actionResponse);
        }

        return response()->json($actionResponse->data);
    }

    /**
     * @return array<string, string[]>
     */
    public function validations(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:1'],
        ];
    }
}
