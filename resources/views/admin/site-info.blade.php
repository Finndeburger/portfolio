@extends('layouts.app')

@section('title', 'Admin - Site Info')

@section('content')
    <section class="min-h-screen px-6 py-10">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-[#212121] mb-2">Site info</h1>
            <p class="text-[#4b5563] mb-8">Edit metadata for {{ $site->title }}</p>

            @include('admin.tabs')

            @if (session('status'))
                <div class="mb-6 rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-green-700 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-red-700 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.sites.info.update', $site) }}"
                class="space-y-5 rounded-xl border border-[#d1d5db] bg-white p-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-semibold text-[#212121] mb-2">Title</label>
                    <input name="title" type="text" value="{{ old('title', $site->title) }}" required
                        class="w-full rounded-lg border border-[#bdbdbd] bg-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-black/20">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-[#212121] mb-2">Slug</label>
                    <input name="slug" type="text" value="{{ old('slug', $site->slug) }}" required
                        class="w-full rounded-lg border border-[#bdbdbd] bg-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-black/20">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-[#212121] mb-2">Dummy URL</label>
                    <input name="dummy_url" type="url" value="{{ old('dummy_url', $site->dummy_url) }}" required
                        class="w-full rounded-lg border border-[#bdbdbd] bg-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-black/20">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-[#212121] mb-2">Description</label>
                    <textarea name="description" rows="4" required
                        class="w-full rounded-lg border border-[#bdbdbd] bg-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-black/20">{{ old('description', $site->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-[#212121] mb-2">Tags (comma separated)</label>
                    <input name="tags" type="text" value="{{ old('tags', $tags) }}"
                        class="w-full rounded-lg border border-[#bdbdbd] bg-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-black/20">
                </div>

                <label class="inline-flex items-center gap-2 text-sm text-[#4b5563]">
                    <input type="checkbox" name="sponsored" value="1" @checked(old('sponsored', $site->sponsored)) class="rounded border-[#9ca3af]">
                    Sponsored result
                </label>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-[#212121] mb-2">Database connection</label>
                        <input name="database_connection" type="text" value="{{ old('database_connection', $site->database_connection) }}"
                            class="w-full rounded-lg border border-[#bdbdbd] bg-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-black/20">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-[#212121] mb-2">Database table</label>
                        <input name="database_table" type="text" value="{{ old('database_table', $site->database_table) }}"
                            class="w-full rounded-lg border border-[#bdbdbd] bg-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-black/20">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-[#212121] mb-2">Database meta (JSON)</label>
                    <textarea name="database_meta" rows="5"
                        class="w-full rounded-lg border border-[#bdbdbd] bg-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-black/20">{{ old('database_meta', $site->database_meta ? json_encode($site->database_meta, JSON_PRETTY_PRINT) : '') }}</textarea>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button type="submit"
                        class="rounded-lg bg-[#212121] px-4 py-2 text-white font-semibold hover:bg-black transition-colors">
                        Save metadata
                    </button>
                    <a href="{{ route('admin.sites') }}"
                        class="rounded-lg border border-[#212121] px-4 py-2 text-[#212121] hover:bg-[#ececec] transition-colors">
                        Back to sites
                    </a>
                </div>
            </form>
        </div>
    </section>
@endsection
