<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Reaction;
use App\Models\Comment;
use App\Models\Post;

class ReactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api'); // or auth:sanctum
    }

    /**
     * Add or update a reaction
     */
    public function react(Request $request)
    {
        $request->validate([
            'type' => 'required|in:like,dislike',
            'reactionable_type' => 'required|in:comment,post',
            'reactionable_id' => 'required|integer|exists:' . ($request->reactionable_type === 'comment' ? 'comments' : 'posts') . ',id',
        ]);

        $model = $request->reactionable_type === 'comment' ? Comment::class : Post::class;

        $reaction = Reaction::where([
            'user_id' => Auth::id(),
            'reactionable_type' => $model,
            'reactionable_id' => $request->reactionable_id,
        ])->first();

        if ($reaction) {
            $reaction->update(['type' => $request->type]);
        } else {
            $reaction = Reaction::create([
                'user_id' => Auth::id(),
                'reactionable_type' => $model,
                'reactionable_id' => $request->reactionable_id,
                'type' => $request->type,
            ]);
        }

        return response()->json([
            'message' => 'Reaction added/updated successfully',
            'reaction' => $reaction
        ]);
    }

    /**
     * Remove a reaction
     */
    public function remove(Request $request)
    {
        $request->validate([
            'reactionable_type' => 'required|in:comment,post',
            'reactionable_id' => 'required|integer',
        ]);

        $model = $request->reactionable_type === 'comment' ? Comment::class : Post::class;

        $deleted = Reaction::where([
            'user_id' => Auth::id(), // Only delete user's own reaction
            'reactionable_type' => $model,
            'reactionable_id' => $request->reactionable_id,
        ])->delete();

        return response()->json([
            'message' => $deleted ? 'Reaction removed' : 'No reaction found'
        ]);
    }

    /**
     * Get total reactions for a specific item
     */
    public function getReactions(Request $request)
    {
        $request->validate([
            'reactionable_type' => 'required|in:comment,post',
            'reactionable_id' => 'required|integer',
        ]);

        $model = $request->reactionable_type === 'comment' ? Comment::class : Post::class;

        $likes = Reaction::where([
            'reactionable_type' => $model,
            'reactionable_id' => $request->reactionable_id,
            'type' => 'like'
        ])->count();

        $dislikes = Reaction::where([
            'reactionable_type' => $model,
            'reactionable_id' => $request->reactionable_id,
            'type' => 'dislike'
        ])->count();

        return response()->json([
            'likes' => $likes,
            'dislikes' => $dislikes,
            'total' => $likes + $dislikes
        ]);
    }
}
 