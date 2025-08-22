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

        return response()->json([
            'status' => true,
            'data'   => $user
        ]);
    }

    public function update(Request $request, $id)
    {
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
    

}
