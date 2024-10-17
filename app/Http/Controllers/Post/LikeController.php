<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $like = $post->likes()->create([
            'user_id' => $request->user()->id
        ]);

        return $like;
    }

    public function destroy(Request $request, Post $post, Like $like)
    {
        $like->delete();

        return response()->noContent();
    }
}
