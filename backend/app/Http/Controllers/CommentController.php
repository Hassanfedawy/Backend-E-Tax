<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    // ✅ Ensure the user is authenticated
    public function __construct()
    {
        $this->middleware('auth:api'); // or 'auth:sanctum' depending on your setup
    }

    // Get all comments for a post
    public function index($postId)
    {
        $comments = Comment::with('user:id,name,email') // eager load user info
            ->where('post_id', $postId)
            ->latest()
            ->get();

        return response()->json($comments);
    }

    // Store a new comment
    public function store(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $comment = Comment::create([
            'post_id' => $postId,
            'user_id' => Auth::id(), // ✅ real logged-in user
            'content' => $request->content,
        ]);

        return response()->json([
            'message' => 'Comment added successfully',
            'comment' => $comment
        ], 201);
    }

    // Show a single comment of a post
    public function show($postId, $id)
    {
        $comment = Comment::with('user:id,name,email')
            ->where('post_id', $postId)
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

        // ✅ Only allow owner to update
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment->update(['content' => $request->content]);

        return response()->json([
            'message' => 'Comment updated successfully',
            'comment' => $comment
        ]);
    }

    // Delete a comment of a post
    public function destroy($postId, $id)
    {
        $comment = Comment::where('post_id', $postId)
            ->where('id', $id)
            ->firstOrFail();

        // ✅ Only allow owner to delete
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
