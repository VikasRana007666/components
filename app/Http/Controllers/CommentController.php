<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function create(Request $request, Post $post)
    {
        $user_id = auth()->id();

        Comment::create([
            'user_id' => $user_id,
            'post_id' => $post->id,
            'content' => $request->content
        ]);

        return back();
    }
}
