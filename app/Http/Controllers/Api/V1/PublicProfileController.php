<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PublicProfileController extends Controller
{
    public function show(User $user)
    {
        $user = User::with([
            'profile',
            'posts.category',
            'posts.author'
        ])
            ->withCount(['followers', 'following'])
            ->findOrFail($user->id);

        return response()->json($user);
    }
}
