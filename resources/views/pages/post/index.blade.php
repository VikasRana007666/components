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
        <div class="">
            <form>
                <input type="search" class="form-control" placeholder="Find user here" name="search">
            </form>
        </div>
        {{-- Grid Start --}}
        <div class="bg-white">
            <div class="mx-auto max-w-2xl py-16 px-4 sm:py-24 sm:px-6 lg:max-w-7xl lg:px-8">
                <div class="grid grid-cols-1 gap-y-10 gap-x-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 xl:gap-x-8">
                    @foreach ($posts as $post)
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
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Grid End --}}
        <div>
            <p>
                @foreach ($posts as $post)
                    <a href="/posts/{{ $post->id }}">{{ $post['title'] }}</a>
                    Likes: {{ $post->likes->where('is_liked', '1')->count() }}
                    <p>
                        <x-app-ui.like :post="$post" likeColor="red" dislikeColor="blue" />
                    </p>
                    <hr>
                    @if (auth()->user()->role == 'admin')
                        <a href="/posts/update/{{ $post->id }}">Update</a>
                    @elseif ($post->user_id == auth()->id())
                        <a href="/posts/update/{{ $post->id }}">Update</a>
                    @endif
                @endforeach
            </p>
            {{-- <p>{{ $posts->appends(request()->input())->links() }}</p> --}}
            <p>{{ $posts->links() }}</p>
        </div>
    </div>
</x-app-layout>
