<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Gate;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PostController extends Controller
{
    protected QueryBuilder $builder;

    public function __construct()
    {
        $this->builder = QueryBuilder::for(Post::class)
            ->allowedFilters([AllowedFilter::exact('category_id'),'user_id'])
            ->allowedIncludes(['category', 'tags', 'likes', 'comments'])
            ->allowedSorts(['created_at']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): ResourceCollection
    {
        // authorize
        Gate::authorize('viewAny', Post::class);
        // paginate posts
        $posts = $this->builder->paginate();
        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        // authorize
        Gate::authorize('create', Post::class);
        // validate the request
        $validatedData = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required'],
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        // create a new post
        $post = new Post();
        $post->title = $validatedData['title'];
        $post->body = $validatedData['body'];
        $post->category_id = $validatedData['category_id'];
        $post->user_id = $request->user()->id; // set the user_id to the authenticated user
        $post->save();

        return $this->json(PostResource::make($post), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $post_id): PostResource
    {
        // find the post
        $post = $this->builder->findOrFail($post_id);
        // authorize
        Gate::authorize('view', $post);
        // fetch the post
        return PostResource::make($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post): JsonResponse
    {
        // authorize
        Gate::authorize('update', $post);
        // validate the request
        $validatedData = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required'],
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        // update the post
        $post->title = $validatedData['title'];
        $post->body = $validatedData['body'];
        $post->category_id = $validatedData['category_id'];
        $post->save();

        return $this->json(PostResource::make($post));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): JsonResponse
    {
        // authorize
        Gate::authorize('delete', $post);
        // delete the post
        $post->delete();
        return $this->json(null, 204);
    }
}
