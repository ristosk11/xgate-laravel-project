<?php

namespace App\Http\Controllers;

use App\Domain\IdentityAndAccess\Actions\ToggleFollowAction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

final class FollowController
{
    public function __invoke(Request $request, User $user, ToggleFollowAction $toggleFollowAction): JsonResponse
    {
        try {
            $following = $toggleFollowAction->execute($request->user(), $user);
        } catch (ValidationException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'errors' => $exception->errors(),
            ], 422);
        }

        return response()->json([
            'following' => $following,
        ]);
    }
}
