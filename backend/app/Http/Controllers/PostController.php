<?php

// app/Http/Controllers/PostController.php
namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Attachment;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
  // POST /api/posts
  public function store(Request $request)
{
    $validated = $request->validate([
        'title'       => 'required|string|max:255',
        'description' => 'required|string',
        'attachment'  => 'nullable|file|max:2048'
    ]);

    $post = new Post();
    $post->title       = $validated['title'];
    $post->description = $validated['description'];

    // مؤقتًا، اربط البوست بالـ Admin user اللي اتعمل بالـ Seeder
    $post->user_id = 1; // بدل auth()->id()

    $post->save();

    // Attachments
    if ($request->hasFile('attachment')) {
        $file = $request->file('attachment');
        $path = $file->store('attachments', 'public');
        $post->attachments()->create([
            'path' => $path,
        ]);
    }

    return response()->json([
        'message' => 'Post created successfully',
        'post'    => $post->load('attachments', 'user'),
    ], 201);
}


  // GET /api/posts/mine
  public function myPosts(Request $request)
  {
    $query = Post::with(['user','attachments'])
      //->where('user_id', auth('api')->id());
        ->where('user_id', 1); // مؤقتًا Admin user


    $this->applySortFilters($query, $request);
    return PostResource::collection($query->paginate(10));
  }

  // GET /api/posts
  public function index(Request $request)
  {
    $query = Post::with(['user','attachments']);
    $this->applySortFilters($query, $request);
    return PostResource::collection($query->paginate(10));
  }

  // GET /api/posts/{id}
  public function show($id)
  {
    $post = Post::with(['user','attachments'])->findOrFail($id);
    return new PostResource($post);
  }

  private function applySortFilters($query, Request $request): void
  {
    if ($q = $request->query('q')) {
      $query->where(fn($x)=>$x->where('title','like',"%$q%")->orWhere('description','like',"%$q%"));
    }
    if ($author = $request->query('author_id')) {
      $query->where('user_id', (int)$author);
    }
    $sort = $request->query('sort','newest');
    if ($sort === 'liked') {
      $query->withCount([
        'reactions as likes_count' => fn($q) =>
          $q->where('type','like')->where('reactionable_type', Post::class)
      ])->orderBy('likes_count','desc')->latest();
    } else {
      $query->latest();
    }
  }
}
