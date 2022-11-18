<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;

class LikeController extends Controller
{
    public function update(Post $post)
    {
        $user_id = auth()->id();

        $like = Like::where('post_id', $post->id)->where('user_id', $user_id)->first() ?? 'na';
        if ($like == 'na') {
            Like::create([
                'user_id' => $user_id,
                'post_id' => $post->id
            ]);
        } else {
            // if ($like->is_liked == '1') {
            //     $like->update([
            //         'is_liked' => '0'
            //     ]);
            // } else {
            //     $like->update([
            //         'is_liked' => '1'
            //     ]);
            // }

            $likeUpdated = $like->is_liked == '1' ? '0' : '1';
            $like->update(['is_liked' => $likeUpdated]);
        }

        return back();
    }
}
