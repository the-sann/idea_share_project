<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function toggleLike(Post $post)
    {
        $user = auth()->user();

        if ($user->hasLiked($post)) {
            $user->likes()
                ->where('post_id', $post->id)
                ->delete();

            return response()->json([
                'message' => 'Post unliked successfully.',
                'likes_count' => $post->likes()->count(),
            ]);
        }

        $user->likes()->create([
            'post_id' => $post->id,
        ]);

        return response()->json([
            'message' => 'Post liked successfully.',
            'likes_count' => $post->likes()->count(),
        ]);
    }
}
