<?php

namespace App\Http\Controllers\Api;

use App\Models\File;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use App\Models\Reply;
use App\Models\Comment;
use Illuminate\Http\Request;
use \Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use App\Http\Traits\CommonTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ApiController extends Controller
{
    use CommonTrait;

    // POST START
    public function post_list()
    {
        // $posts = Post::latest()->where('is_deleted', 'no')->paginate(10)->appends(request()->input());
        $posts = Post::latest()->where('is_deleted', 'no')->get();

        $data = [];

        foreach ($posts as $post) {
            $file = File::where('table_name_id', $post->id)->where('is_deleted', 'no')->first();
            $commentCount = Comment::where('table_name', 'posts')->where('table_name_id', $post->id)->count();
            array_push($data, [
                "post_id" => $post->id,
                "post_title" => $post->title,
                "post_create_at" => $post->created_at,
                "post_updated_at" => $post->updated_at,
                "user_id" => User::where('id', $post->user_id)->first()->name,
                "file" => url("storage") . "/" . strval($file->doc_path ?? ""),
                "comment_count" => $commentCount
            ]);
        }

        return response()->json([
            "result" => true,
            "message" => "Latest Posts",
            "data" => $data,
        ]);
    }

    public function post_create(Request $request)
    {
        $this->authorize('create', Post::class);

        if ($request->isMethod('post')) {
            $request->validate([
                'title' => 'required',
            ]);

            $post = Post::create([
                'title' => $request->title,
                'user_id' => auth()->id()
            ]);

            // for images
            if ($request->hasFile('doc_image')) {
                // $this->upload_multiple_files($request, "doc_image", "image", "posts", "posts", $post->id);
                $this->upload_multiple_files($request, "doc_image", "image", "posts", "posts", $post->id);
            }

            // for videos
            if ($request->hasFile('doc_video')) {
                $this->upload_multiple_files($request, "doc_video", "video", "posts", "posts", $post->id);
            }

            // for pdf
            if ($request->hasFile('doc_pdf')) {
                $this->upload_multiple_files($request, "doc_pdf", "pdf", "posts", "posts", $post->id);
            }

            $post = Post::where('id', $post->id)->where('is_deleted', 'no')->first();
            $files = File::where('table_name_id', $post->id)->where('is_deleted', 'no')->get();

            return response()->json([
                "post" => $post,
                "files" => $files ?? 'No files uploaded'
            ]);
        }
    }

    public function post_show(Request $request)
    {
        // https://stackoverflow.com/questions/41971581/laravel-eloquent-nested-comment-and-replies

        $post = Post::where('id', $request->post_id)
            ->first();

        $files = File::where('table_name_id', $request->post_id)->where('is_deleted', 'no')->get();
        $filesData = [];
        foreach ($files as $file) {
            array_push($filesData, [
                "file_id" => $file->id,
                "file_doc_path" => url("storage") . "/" . strval($file->doc_path ?? ""),
            ]);
        }

        $comments = Comment::where("table_name_id", $request->post_id)->where('parent_id', 'na')->get();
        $commentsData = [];

        $repliesData = [];
        // foreach ($comments as $comment) {
        //     $replies = Reply::where("comment_id", $comment->id)->where("reply_id", "na")->get();
        //     foreach ($replies as $reply) {
        //         array_push($repliesData, [
        //             "id" => $reply->id,
        //             "user_id" => $reply->user_id,
        //             "content" => $reply->content,
        //             "is_deleted" => $reply->is_deleted,
        //             "created_at" => $reply->created_at,
        //             "updated_at" => $reply->updated_at,
        //             "reply_replies" => Reply::where("reply_id", $reply->id)->get() ?? ""
        //         ]);
        //     }
        // }

        foreach ($comments as $comment) {
            array_push($commentsData, [
                "comment" => $comment,
                "replies" => Comment::where('is_reply', 'yes')->where('parent_id', $comment->id)->get()
            ]);
        }

        return response()->json([
            "result" => true,
            "message" => "Single Post",
            'post' => $post,
            // 'files' => $filesData ?? 'No Files',
            // 'comments' => $commentsData ?? 'No Comments',
            "comments" => $commentsData
        ]);
    }

    public function post_update(Request $request)
    {
        $post = Post::where('id', $request->post_id)->first();

        $this->authorize('update', $post);

        if ($request->isMethod('post')) {
            $request->validate([
                'title' => 'required',
            ]);

            $post->update([
                'title' => $request->title,
            ]);

            // for image
            if ($request->hasFile('doc_image')) {
                $files = File::where('doc_type', 'image')->where('table_name', 'posts')->where('table_name_id', $post->id)->get();
                foreach ($files as $file) {
                    // if you actually want to delete the file
                    // Storage::delete($file->doc_path);
                    // $file->delete();

                    $file->update([
                        'is_deleted' => 'yes'
                    ]);
                }
                $this->upload_multiple_files($request, "doc_image", "image", "posts", "posts", $post->id);
            }

            // for video
            if ($request->hasFile('doc_video')) {
                $files = File::where('doc_type', 'video')->where('table_name', 'posts')->where('table_name_id', $post->id)->get();
                foreach ($files as $file) {
                    // if you actually want to delete the file
                    // Storage::delete($file->doc_path);
                    // $file->delete();

                    $file->update([
                        'is_deleted' => 'yes'
                    ]);
                }
                $this->upload_multiple_files($request, "doc_video", "video", "posts", "posts", $post->id);
            }

            // for pdf
            if ($request->hasFile('doc_pdf')) {
                $files = File::where('doc_type', 'pdf')->where('table_name', 'posts')->where('table_name_id', $post->id)->get();
                foreach ($files as $file) {
                    // if you actually want to delete the file
                    // Storage::delete($file->doc_path);
                    // $file->delete();

                    $file->update([
                        'is_deleted' => 'yes'
                    ]);
                }
                $this->upload_multiple_files($request, "doc_pdf", "pdf", "posts", "posts", $post->id);
            }

            $post = Post::where("is_deleted", "no")->where('id', $request->id)->first();
            $files = File::where('table_name', 'posts')->where('table_name_id', $post->id)->get();

            return response()->json([
                "post updated" => $post,
                "files" => $files ?? 'No files updated'
            ], 200);
        }

        // return view('pages.post.form', $data);
    }

    public function post_destroy(Post $post)
    {
        //
    }

    // LIKE START
    public function like_update(Post $post)
    {
        $user_id = auth()->id();

        $like = Like::where('post_id', $post->id)->where('user_id', $user_id)->first() ?? 'na';
        if ($like == 'na') {
            Like::create([
                'user_id' => $user_id,
                'post_id' => $post->id
            ]);
        } else {
            $likeUpdated = $like->is_liked == '1' ? '0' : '1';
            $like->update(['is_liked' => $likeUpdated]);
        }

        return redirect('/posts');
    }

    // COMMENT START
    public function comment_create()
    {
        request()->validate([
            "content" => "required",
            "table_name_id" => "required"
        ]);

        if (request('parent_id') == "") {
            Comment::create([
                "user_id" => auth()->id(),
                "content" => request('content'),
                "table_name_id" => request("table_name_id")
            ]);
        } else {
            Comment::create([
                "user_id" => auth()->id(),
                "content" => request('content'),
                "table_name_id" => request("table_name_id"),
                "parent_id" => request('parent_id'),
                "is_reply" => "yes"
            ]);
        }

        $comment = Comment::latest()->first();

        return response()->json(
            [
                "comment" => $comment
            ]
        );
    }

    // REPLY START
    public function reply_create()
    {
        request()->validate([
            "content" => "required",
            "comment_id" => "required"
        ]);

        if (!empty(request("reply_id"))) {
            Reply::create([
                "user_id" => auth()->id(),
                "content" => request('content'),
                "comment_id" => request("comment_id"),
                "reply_id" => request("reply_id")
            ]);
        } else {
            Reply::create([
                "user_id" => auth()->id(),
                "content" => request('content'),
                "comment_id" => request("comment_id")
            ]);
        }

        return response()->json(
            [
                "comment" => Comment::where('is_deleted', "no")->where('id', request('comment_id'))->first(),
                "replies" => Reply::where('is_deleted', 'no')->where('comment_id', request('comment_id'))->get()
            ]
        );
    }

    // User Status
    public function status()
    {
        $users = User::all();

        $data = [];

        foreach ($users as $user) {
            if (Cache::has('is-user-online-' . $user->id)) {
                array_push($data, [
                    "is_online" => "yes",
                    "user_id" => $user->id
                ]);
            } else {
                array_push($data, [
                    "is_online" => "no",
                    "user_id" => $user->id
                ]);
            }
        }

        return response()->json([
            "data" => $data
        ]);
    }



    // Notifications
    public function send_notification($title, $body, $deviceToken)
    {
        $sendData = array(
            'body' => !empty($body) ? $body : '',
            'title' => !empty($title) ? $title : '',
            'sound' => 'Default'
        );
        $result =  $this->fcmNotification($deviceToken, $sendData);
    }

    public function fcmNotification($device_id, $sendData)
    {
        #API access key from Google API's Console
        if (!defined('API_ACCESS_KEY')) {
            define('API_ACCESS_KEY', 'AAAAp8JWndI:APA91bHHqYv-qnFAPFi5nsvKDjIIeOtM6pv-9nPgXIahMgMHjeQKMOAhLCJx2UQ99i5DG9qVvYci_cB0gXWp_9PjXAQ6FS6RvVDLr-FcTcWtiXs7ilcWXpGVn2_gwGZ8kquMKt_uPv0r');
        }
        $fields = array(
            'to'    => $device_id,
            'data'  => $sendData,
            'notification'  => $sendData,
        );
        $headers = array(
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        );
        #Send Reponse To FireBase Server
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === false) {
            die('Curl failed ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }

    public function  send_notification_daily_goals(Request $request)
    {

        $time = date('H:i');
        $time1 = Carbon::parse($time);
        $new_time = $time1->format("g:i A");

        $daily_goals = DB::table('daily_goals')->select('user_id', 'task_name')->where('time', $new_time)->get();
        if (!empty($daily_goals)) {
            foreach ($daily_goals as $daily) {

                $user_logins = UserLogin::where('user_id', $daily->user_id)->get();
                if (!empty($user_logins)) {

                    foreach ($user_logins as $logins) {
                        $title = 'Hey User! These are your Daily Goals';
                        $body = $daily->task_name ?? '';
                        $deviceToken = $logins->deviceToken;
                        $success = $this->send_notification($title, $body, $deviceToken);
                        if ($success) {
                            DB::table('new')->insert(['name' => 'ssss' . $time . $daily->task_name]);
                        }
                    }
                }
            }
        }
    }
}
