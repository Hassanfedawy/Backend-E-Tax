<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reaction;
use App\Models\Comment;
use App\Models\Post;

class ReactionController extends Controller
{
    /**
     * Add or update a reaction
     */
  public function react(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'type' => 'required|in:like,dislike',
        'reactionable_type' => 'required|in:comment,post',
        'reactionable_id' => 'required|integer',
    ]);

    // Determine the model class
    $model = $request->reactionable_type === 'comment' ? Comment::class : Post::class;

    // Try to find existing reaction
    $reaction = Reaction::where([
        'user_id' => $request->user_id,
        'reactionable_type' => $model,
        'reactionable_id' => $request->reactionable_id,
    ])->first();

    if ($reaction) {
        // Update manually
        $reaction->type = $request->type;
        $reaction->save();
    } else {
        // Create manually without mass assignment
        $reaction = new Reaction();
        $reaction->user_id = $request->user_id;
        $reaction->reactionable_type = $model;
        $reaction->reactionable_id = $request->reactionable_id;
        $reaction->type = $request->type;
        $reaction->save();
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
            'user_id' => 'required|exists:users,id',
            'reactionable_type' => 'required|in:comment,post',
            'reactionable_id' => 'required|integer',
        ]);

        $model = $request->reactionable_type === 'comment' ? Comment::class : Post::class;

        $deleted = Reaction::where([
            'user_id' => $request->user_id,
            'reactionable_type' => $model,
            'reactionable_id' => $request->reactionable_id,
        ])->delete();

        return response()->json([
            'message' => $deleted ? 'Reaction removed' : 'No reaction found'
        ]);
    }

    /**
     * Get reactions for a specific item
     */
 /**
 * Get total reactions (likes and dislikes) for a specific item
 */
public function getReactions(Request $request)
{
    $request->validate([
        'reactionable_type' => 'required|in:comment,post',
        'reactionable_id' => 'required|integer',
    ]);

    $model = $request->reactionable_type === 'comment' ? Comment::class : Post::class;

    // Count likes
    $likes = Reaction::where([
        'reactionable_type' => $model,
        'reactionable_id' => $request->reactionable_id,
        'type' => 'like'
    ])->count();

    // Count dislikes (optional)
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
