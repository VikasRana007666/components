@php
    $post['title'] = $post['title'] ?? '';
@endphp

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
                    You're logged in!
                </div>
            </div>
        </div>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <form method="POST" enctype="multipart/form-data">
                    @csrf
                    <label class="block">
                        <div class="mb-2">
                            <span for="title" class="block text-sm font-medium text-slate-700">Title</span>
                            <!-- Using form state modifers, the classes can be identical for every input -->
                            <input type="text" value="{{ $post['title'] }}"
                                class="mt-1 block w-full px-3 py-2 bg-white border border-slate-300 rounded-md text-sm shadow-sm placeholder-slate-400
                    focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500
                    disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200 disabled:shadow-none
                    invalid:border-pink-500 invalid:text-pink-600
                    focus:invalid:border-pink-500 focus:invalid:ring-pink-500
                  "
                                name="title" />
                        </div>

                        <div class="mb-2">
                            <span for="doc_path" class="block text-sm font-medium text-slate-700">Images/Docs</span>
                            <!-- Using form state modifers, the classes can be identical for every input -->
                            <input type="file" value=""
                                class="mt-1 block w-full px-3 py-2 bg-white border border-slate-300 rounded-md text-sm shadow-sm placeholder-slate-400
                    focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500
                    disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200 disabled:shadow-none
                    invalid:border-pink-500 invalid:text-pink-600
                    focus:invalid:border-pink-500 focus:invalid:ring-pink-500
                  "
                                name="doc_path[]" multiple />
                        </div>
                    </label>

                    <button type="submit" class="px-2 bg-sky-500 hover:bg-sky-400 my-2 rounded-lg">
                        Submit
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
