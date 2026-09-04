<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $posts = Post::with([
            'author.profile',
            'category',
        ])->latest()->paginate(20);

        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $data = $request->validated();
        $data['author_id'] = $request->user()->id;

        $data['slug'] = Str::slug($data['title']) . '-' . Str::random(6);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('posts', 'public');
        }
        $post = Post::create($data);
        $post->load([
            'author.profile',
            'category',
        ]);
        return new PostResource($post);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $username, Post $post)
    {
        // abort_if($post->author_id !== Auth::id() ||
        //     $post->author->username !== $username, 403, 'Unauthorized access to this post.');
        $post->load('author.profile');

        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StorePostRequest $request, Post $post)
    {
        abort_if($post->author_id !== Auth::id(), 403, 'Unauthorized access to this post.');
        $data = $request->validated();
        $post->update($data);
        return new PostResource($post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        abort_if($post->author_id !== Auth::id(), 403, 'Unauthorized access to this post.');
        $post->delete();
        return response()->json(['message' => 'Post deleted successfully']);
    }
    public function category(Category $category)
    {
        $post = $category->posts()->simplePaginate(20);
        return PostResource::collection($post);
    }
}
