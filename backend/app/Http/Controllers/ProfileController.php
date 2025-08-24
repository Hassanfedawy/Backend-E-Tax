<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Get user profile with posts + attachments.
     */
   public function show($id)
{
    $user = User::with(['attachments', 'posts.attachments'])->findOrFail($id);

    // Map posts to include a link
    $posts = $user->posts->map(function ($post) {
        return [
            'id'          => $post->id,
            'title'       => $post->title,
            'description' => $post->description,
            'link'        => url("/posts/{$post->id}"), 
            'attachments' => $post->attachments,        
            'created_at'  => $post->created_at,
            'updated_at'  => $post->updated_at,
        ];
    });

    return response()->json([
        'status' => true,
        'data'   => [
            'id'          => $user->id,
            'name'        => $user->name,
            'email'       => $user->email,
            'attachments' => $user->attachments,
            'posts'       => $posts, // posts now contain links
        ],
    ]);
}


    public function update(Request $request, $id)
    { dd($request->all());
    $user = User::findOrFail($id);

    $request->validate([
        'name'   => 'sometimes|string|max:255',
        'email'  => 'sometimes|email|unique:users,email,' . $user->id,
        'profile_image' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048'
    ]);

    // Update text fields
    if ($request->filled('name')) {
        $user->name = $request->name;
    }
    if ($request->filled('email')) {
        $user->email = $request->email;
    }
    $user->save();
    dd($request);

    // Handle profile image upload
    if ($request->hasFile('profile_image')) {
        $path = $request->file('profile_image')->store('profile_images', 'public');

        // Delete old profile image
        $user->attachments()->where('category', 'profile_image')->delete();

        // Save new one
        $user->attachments()->create([
            'category' => 'profile_image',
            'path' => $path,
        ]);
    }

    // Load updated user with profile image attachment
    $updatedUser = User::with(['attachments' => function($q) {
        $q->where('category', 'profile_image');
    }, 'posts'])->find($user->id);

    // Transform profile_image path into URL
    if ($updatedUser->attachments->isNotEmpty()) {
        $updatedUser->profile_image_url = Storage::url($updatedUser->attachments->first()->path);
    } else {
        $updatedUser->profile_image_url = null;
    }

    return response()->json($updatedUser);
}
public function changePassword(Request $request)
    {
        // ✅ Validate request
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed', 
        ]);

        $user = User::find($request->user_id);

        // ✅ Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password does not match'
            ], 400);
        }

        // ✅ Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Password updated successfully'
        ]);
    }

    

}
