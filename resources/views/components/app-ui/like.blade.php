@props(['post'])

@php
    $user_id = auth()->id();
    $like = $post->likes->where('user_id', $user_id)->first() ?? 'na';
@endphp

<div>
    @if ($like == 'na')
        <form action="/like/{{ $post->id }}" method="post" enctype="multipart/form-data">
            @csrf
            <button type="submit" class='text-stone-400 px-2 rounded-full'>
                <i class="fa-solid fa-heart"></i>
            </button>
        </form>
    @else
        @if ($like->is_liked == '1')
            <form action="/like/{{ $post->id }}" method="post" enctype="multipart/form-data">
                @csrf
                <button type="submit" class='text-red-400 px-2 rounded-full'>
                    <i class="fa-solid fa-heart"></i>
                </button>
            </form>
        @else
            <form action="/like/{{ $post->id }}" method="post" enctype="multipart/form-data">
                @csrf
                <button type="submit" class='text-stone-400 px-2 rounded-full'>
                    <i class="fa-solid fa-heart"></i>
                </button>
            </form>
        @endif
    @endif
</div>
