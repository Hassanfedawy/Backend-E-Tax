<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // Get all comments for a post
    public function index($postId)
    {
        $comments = Comment::where('post_id', $postId)->get();
        return response()->json($comments);
    }

    // Store a new comment
    public function store(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $comment = new Comment();
        $comment->post_id = $postId;
        $comment->user_id = 1; // assuming logged-in user
        $comment->content = $request->content;
        $comment->save();

        return response()->json($comment, 201);
    }

    // Show a single comment of a post
    public function show($postId, $id)
    {
        $comment = Comment::where('post_id', $postId)
                          ->where('id', $id)
                          ->firstOrFail();

        return response()->json($comment);
    }

    // Update a comment of a post
    public function update(Request $request, $postId, $id)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $comment = Comment::where('post_id', $postId)
                          ->where('id', $id)
                          ->firstOrFail();

        $comment->content = $request->content;
        $comment->save();

        return response()->json($comment);
    }

    // Delete a comment of a post
    public function destroy($postId, $id)
    {
        $comment = Comment::where('post_id', $postId)
                          ->where('id', $id)
                          ->firstOrFail();

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
