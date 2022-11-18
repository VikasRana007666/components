<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use App\Models\Reply;
use App\Models\Comment;
// use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $search = request()->search;

        $data['posts'] = Post::latest()
            ->where('is_deleted', 'no')
            ->where('title', 'like', '%' . $search . '%')
            ->paginate(10)
            ->appends(request()->input());

        return view('pages.post.index', $data);
    }

    public function create(Request $request)
    {
        $this->authorize('create', Post::class);

        if ($request->isMethod('post')) {
            $request->validate([
                'title' => 'required',
            ]);

            $data['post'] = Post::create([
                'title' => $request->title,
                'user_id' => auth()->id()
            ]);

            if ($request->hasFile('doc_path')) {
                foreach ($request->file('doc_path') as $file) {
                    // $fileName = 'DOC-' . date("Y-m-d") . '-L' . time();

                    $extension = $file->extension();

                    File::create([
                        'doc_type' => $extension,
                        'doc_path' => $file->store('projects', 'public'),
                        'table_name' => 'posts',
                        'table_name_id' => $data['post']->id,
                        'user_id' => auth()->id()
                    ]);
                }
            }

            return redirect('/posts');
        }

        return view('pages.post.form');
    }

    public function show(Post $post)
    {
        $data['post'] = $post;
        $data['comments'] = Comment::latest()->where('post_id', $post->id)->get();
        $data['replies'] = Reply::latest()->get();
        // $data['users'] = User::all();

        return view('pages.post.single', $data);
    }

    public function update(Request $request, Post $id)
    {
        $this->authorize('update', $id);

        $data['post'] = $id;
        $files = File::where('table_name', 'posts')->where('table_name_id', $data['post']->id)->get();

        if ($request->isMethod('post')) {
            $request->validate([
                'title' => 'required',
            ]);

            $data['post']->update([
                'title' => $request->title,
            ]);

            if ($request->hasFile('doc_path')) {
                foreach ($files as $file) {
                    // if you actually want to delete the file
                    // Storage::delete($file->doc_path);
                    // $file->delete();

                    $file->update([
                        'is_deleted' => 'yes'
                    ]);
                }

                foreach ($request->file('doc_path') as $file) {
                    $fileName = 'DOC-' . date("Y-m-d") . '-L' . time();

                    $extension = $file->extension();

                    File::create([
                        'doc_type' => $extension,
                        'doc_path' => $file->store('public'),
                        'table_name' => 'posts',
                        'table_name_id' => $data['post']->id,
                        'user_id' => auth()->id()
                    ]);
                }
            }

            return redirect('/posts');
        }

        return view('pages.post.form', $data);
    }

    public function destroy(Post $post)
    {
        //
    }

    public function testing(Request $request)
    {

        if ($request->isMethod('post')) {
            $fields = $request->validate([
                'email' => 'required|string',
                'password' => 'required|string'
            ]);

            // Check email
            $user = User::where('email', $fields['email'])->first();
            // dd($user->password);
            // dd(Hash::make($fields['password']));
            // Check password
            // dd(Hash::check($fields['password'], $user->password));
            if (Hash::check($fields['password'], $user->password)) {
                response()->json([
                    "error" => "Bad Credentials"
                ], 401);
            }

            $token = $user->createToken('myapptoken')->plainTextToken;

            $response = [
                'user' => $user,
                'token' => $token
            ];

            return response($response, 201);
        }

        return view('pages.testing.form');
    }
}
