<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <a class="bg-sky-500 hover:bg-sky-400 px-2 rounded-lg" href="{{ route('post-create') }}">Create</a>
                </div>
            </div>
        </div>

        {{-- Single Start --}}
        <div class="bg-white">
            <div class="mx-auto max-w-2xl py-16 px-4 sm:py-24 sm:px-6 lg:max-w-7xl lg:px-8">
                <div class="bg-slate-100 group">
                    <a href="/posts/{{ $post->id }}">
                        <h3 class="mt-4 text-lg text-gray-700">{{ $post->title }}</h3>
                    </a>
                    <div class="flex justify-between">
                        <div class="flex">
                            <x-app-ui.like :post="$post" likeColor="red" dislikeColor="blue" />
                            {{ $post->likes->where('is_liked', '1')->count() }}
                        </div>
                        @if (auth()->user()->role == 'admin')
                            <a href="/posts/update/{{ $post->id }}">Update</a>
                        @elseif ($post->user_id == auth()->id())
                            <a href="/posts/update/{{ $post->id }}">Update</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        {{-- Single End --}}

        {{-- Comment Start --}}
        <div class="mx-auto max-w-2xl">
            <h1>Comment</h1>
            <form action="/comment/{{ $post->id }}" method="post" enctype="multipart/form-data">
                @csrf
                <textarea name="content" rows="10" placeholder="Comment Here ..."></textarea>
                <button type="submit" class='bg-stone-400 px-2 rounded-full'>
                    Comment
                </button>
            </form>
        </div>
        <div class="mx-4 md:mx-10">
            @foreach ($comments as $comment)
                <div>
                    <p class="font-bold">{{ $comment->user->name }} <span
                            class="font-thin">{{ $comment->created_at }}</span></p>
                    <p class="mx-4">{{ $comment->content }}</p>
                    <hr>
                    <form action="/reply/{{ $comment->id }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <textarea class="mx-4" name="content" rows="1" placeholder="Reply to {{ $comment->user->name }} ..."></textarea>
                        <button type="submit" class='bg-stone-400 px-2 rounded-full'>
                            reply
                        </button>
                    </form>
                    @foreach ($comment->replies as $reply)
                        <p class="font-bold mx-8">Replied: {{ $reply->user->name }} <span
                                class="font-thin">{{ $reply->created_at }}</span></p>
                        <p class="mx-8">{{ $reply->content }}</p>
                        <form action="/reply/{{ $comment->id }}/{{ $reply->id }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <textarea class="mx-8" name="content" rows="1" placeholder="Reply to {{ $reply->user->name }} ..."></textarea>
                            <button type="submit" class='bg-stone-400 px-2 rounded-full'>
                                reply
                            </button>
                        </form>
                    @endforeach
                    {{-- <p>Name: {{ $users->where('id', $comment->user_id)->first()->name }}</p> --}}
                </div>
            @endforeach
        </div>
        {{-- Comment End --}}
    </div>
</x-app-layout>
