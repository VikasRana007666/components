<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use App\Models\Comment;
use Illuminate\Http\Request;

class ReplyController extends Controller
{
    public function commentReply(Request $request, Comment $comment)
    {
        $request->validate([
            'content' => 'required'
        ]);

        $user_id = auth()->id();

        Reply::create([
            'user_id' => $user_id,
            'comment_id' => $comment->id,
            'content' => $request->content,
        ]);

        return back();
    }

    public function replyReply(Request $request, Comment $comment, Reply $reply)
    {
        $request->validate([
            'content' => 'required'
        ]);

        $user_id = auth()->id();

        Reply::create([
            'user_id' => $user_id,
            'comment_id' => $comment->id,
            'content' => $request->content,
            'reply_id' => $reply->id
        ]);

        return back();
    }
}
