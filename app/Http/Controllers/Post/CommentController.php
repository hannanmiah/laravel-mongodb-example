<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CommentController extends Controller
{
    public function index(Post $post):ResourceCollection
    {
        $comments = $post->comments()->get();

        return CommentResource::collection($comments);
    }

    public function show(Post $post,Comment $comment):CommentResource
    {
        return new CommentResource($comment);
    }

    public function store(Request $request,Post $post)
    {
        // validate the request
        $validatedData = $request->validate([
            'content' => ['required','string'],
        ]);

        // create a new comment
        $comment = new Comment();
        $comment->content = $validatedData['content'];
        $comment->user_id = $request->user()->id; // set the user_id to the authenticated user
        $comment->post_id = $post->id;

        // save the comment
        $comment->save();

        return response()->json(CommentResource::make($comment),201);
    }

    public function update(Request $request,Post $post,Comment $comment)
    {
        // validate the request
        $data = $request->validate([
            'content' => ['required','string'],
        ]);

        // update the comment
        $comment->update($data);

        return response()->json(CommentResource::make($comment));
    }

    public function destroy(Post $post,Comment $comment)
    {
        // delete the comment
        $comment->delete();

        return response()->json(null, 204);
    }
}
