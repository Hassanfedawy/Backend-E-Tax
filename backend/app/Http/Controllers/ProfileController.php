<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Get user profile with posts + attachments (with URLs).
     */
    public function show()
    {
        $id=auth()->id();
        $user = User::with(['attachments', 'posts.attachments'])->findOrFail($id);

        // Map posts to include attachment URLs
        $posts = $user->posts->map(function ($post) {
            return [
                'id'          => $post->id,
                'title'       => $post->title,
                'description' => $post->description,
                'attachments' => $post->attachments->map(function ($attachment) {
                    return [
                        'id'       => $attachment->id,
                        'category' => $attachment->category,
                        'url'      => url('storage/' . $attachment->path),
                    ];
                }),
                'created_at'  => $post->created_at,
                'updated_at'  => $post->updated_at,
            ];
        });

        // Map user attachments with URLs
        $userAttachments = $user->attachments->map(function ($attachment) {
            return [
                'id'       => $attachment->id,
                'category' => $attachment->category,
                'url'      => url('storage/' . $attachment->path),
            ];
        });

        return response()->json([
            'status' => true,
            'data'   => [
                'id'          => $user->id,
                'name'        => $user->name,
                'email'       => $user->email,
                'attachments' => $userAttachments,
                'posts'       => $posts,
            ],
        ]);
    }

    /**
     * Update user profile (name, email, password, profile image).
     */
    public function update(Request $request)
    {   $id=auth()->id();
        $user = User::findOrFail($id);

        $request->validate([
            'name'   => 'sometimes|string|max:255',
            'email'  => 'sometimes|email|unique:users,email,' . $user->id,
            'profile_image' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',

            'current_password' => 'sometimes|required_with:new_password|string',
            'new_password'     => 'sometimes|required_with:current_password|string|min:8|confirmed',
        ]);

        // Update text fields
        if ($request->filled('name')) {
            $user->name = $request->name;
        }
        if ($request->filled('email')) {
            $user->email = $request->email;
        }
        $user->save();

        // Password change
        if ($request->filled('current_password') && $request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => ['The current password is incorrect.'],
                ]);
            }
            $user->password = Hash::make($request->new_password);
            $user->save();
        }

        // Profile image upload
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->attachments()->where('category', 'profile_image')->delete();
            $user->attachments()->create([
                'category' => 'profile_image',
                'path' => $path,
            ]);
        }

        // Load updated user with profile image attachment
        $updatedUser = User::with(['attachments', 'posts'])->find($user->id);

        $userAttachments = $updatedUser->attachments->map(function ($attachment) {
            return [
                'id'       => $attachment->id,
                'category' => $attachment->category,
                'url'      => url('storage/' . $attachment->path),
            ];
        });

        $posts = $updatedUser->posts->map(function ($post) {
            return [
                'id'          => $post->id,
                'title'       => $post->title,
                'description' => $post->description,
                'attachments' => $post->attachments->map(function ($attachment) {
                    return [
                        'id'       => $attachment->id,
                        'category' => $attachment->category,
                        'url'      => url('storage/' . $attachment->path),
                    ];
                }),
                'created_at'  => $post->created_at,
                'updated_at'  => $post->updated_at,
            ];
        });

        return response()->json([
            'status' => true,
            'data'   => [
                'id'               => $updatedUser->id,
                'name'             => $updatedUser->name,
                'email'            => $updatedUser->email,
                'attachments'      => $userAttachments,
                'posts'            => $posts,
                'profile_image_url'=> $userAttachments->where('category','profile_image')->first()->url ?? null,
            ],
        ]);
    }
}
