<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::paginate(10);
        return PostResource::collection($posts);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'list|filled',
            'tags.*' => 'string|max:255',
        ]);

        $data['user_id'] = $request->user()->id;

        $post = Post::create($data);

        if ($request->filled('tags')) {
            collect($request->input('tags',[]))->each(function ($tag) use ($post) {
                $tag = Tag::firstOrCreate(['name' => $tag]);
                $post->tags()->attach($tag);
            });
        }

        return response()->json($post, 201);
    }

    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required',
            'category_id' => 'required|exists:categories,id',
        ]);

        $post->update($data);

        return response()->json($post);
    }

    public function show(Post $post)
    {
        $post->load('user');
        return PostResource::make($post);
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return response()->json(null, 204);
    }
}
