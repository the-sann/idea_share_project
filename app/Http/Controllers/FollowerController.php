<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class FollowerController extends Controller
{
    public function followUnfollow(User $user)
    {
        $authUser = auth()->user();

        if ($authUser->id === $user->id) {
            return response()->json([
                'message' => 'You cannot follow yourself.',
            ], 422);
        }

        $user->followers()->toggle($authUser->id);

        return response()->json([
            'followers' => $user->followers()->count(),
            'is_following' => $user->isFollowedBy($authUser),
        ]);
    }
}
