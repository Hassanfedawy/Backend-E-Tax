<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Post;

use Illuminate\Http\Request;
use Carbon\Carbon;
class StatsController extends Controller
{
    /**
     * Get users created this month
     */
    public function getUsersThisMonth()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth   = Carbon::now()->endOfMonth();

        $users = User::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();

        return response()->json([
            'users_this_month' => $users,
            'start' => $startOfMonth->toDateTimeString(),
            'end'   => $endOfMonth->toDateTimeString(),
        ]);
    }
    /**
     * Get posts created this month
     */
    public function getPostsToday()
    {
        $startOfDay = Carbon::today();
        $endOfDay   = Carbon::today()->endOfDay();

        $posts = Post::whereBetween('created_at', [$startOfDay, $endOfDay])->count();

        return response()->json([
            'posts_today' => $posts,
            'start' => $startOfDay->toDateTimeString(),
            'end'   => $endOfDay->toDateTimeString(),
        ]);
    }


}
