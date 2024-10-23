<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\Tag;
use App\Queries\Post\PublicPostQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    public function __construct(protected PublicPostQuery $postQuery)
    {
    }

    public function index()
    {
        $posts = $this->postQuery->paginate();
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
        Gate::authorize('update', $post);
        // validate
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required',
            'category_id' => 'required|exists:categories,id',
        ]);

        $post->update($data);

        return response()->json($post);
    }

    public function show(string $post_id)
    {
        $post = $this->postQuery->find($post_id);
        return PostResource::make($post);
    }

    public function destroy(Post $post)
    {
        // authorize
        Gate::authorize('delete',$post);

        $post->delete();
        return response()->json(null, 204);
    }
}
