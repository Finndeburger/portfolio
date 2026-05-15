@extends('layouts.app')

@section('title', 'Admin - Site Create')

@section('content')
    <section class="min-h-screen px-6 py-10">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-[#212121] mb-2">Site create</h1>
            <p class="text-[#4b5563] mb-8">Create a new site record and generate scaffold files in one step.</p>

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

            <form method="POST" action="{{ route('admin.sites.create.store') }}"
                class="space-y-5 rounded-xl border border-[#d1d5db] bg-white p-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-[#212121] mb-2">Title</label>
                        <input name="title" type="text" value="{{ old('title') }}" required
                            class="w-full rounded-lg border border-[#bdbdbd] bg-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-black/20">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-[#212121] mb-2">Slug</label>
                        <input name="slug" type="text" value="{{ old('slug') }}" required
                            placeholder="example-site"
                            class="w-full rounded-lg border border-[#bdbdbd] bg-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-black/20">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-[#212121] mb-2">Dummy URL (optional)</label>
                    <input name="dummy_url" type="url" value="{{ old('dummy_url') }}"
                        placeholder="https://example.com/sites/example-site"
                        class="w-full rounded-lg border border-[#bdbdbd] bg-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-black/20">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-[#212121] mb-2">Description</label>
                    <textarea name="description" rows="4" required
                        class="w-full rounded-lg border border-[#bdbdbd] bg-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-black/20">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-[#212121] mb-2">Tags (comma separated)</label>
                    <input name="tags" type="text" value="{{ old('tags') }}"
                        placeholder="portfolio, product, demo"
                        class="w-full rounded-lg border border-[#bdbdbd] bg-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-black/20">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-[#212121] mb-2">Database connection (optional)</label>
                        <input name="database_connection" type="text" value="{{ old('database_connection') }}"
                            class="w-full rounded-lg border border-[#bdbdbd] bg-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-black/20">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-[#212121] mb-2">Database table (optional)</label>
                        <input name="database_table" type="text" value="{{ old('database_table') }}"
                            class="w-full rounded-lg border border-[#bdbdbd] bg-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-black/20">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-[#212121] mb-2">Database meta JSON (optional)</label>
                    <textarea name="database_meta" rows="4"
                        class="w-full rounded-lg border border-[#bdbdbd] bg-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-black/20">{{ old('database_meta') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <label class="inline-flex items-center gap-2 text-sm text-[#4b5563]">
                        <input type="checkbox" name="sponsored" value="1" @checked(old('sponsored')) class="rounded border-[#9ca3af]">
                        Sponsored result
                    </label>

                    <label class="inline-flex items-center gap-2 text-sm text-[#4b5563]">
                        <input type="checkbox" name="create_about" value="1" @checked(old('create_about', true)) class="rounded border-[#9ca3af]">
                        Generate about page
                    </label>

                    <label class="inline-flex items-center gap-2 text-sm text-[#4b5563]">
                        <input type="checkbox" name="create_contact" value="1" @checked(old('create_contact', true)) class="rounded border-[#9ca3af]">
                        Generate contact page
                    </label>

                    <label class="inline-flex items-center gap-2 text-sm text-[#4b5563]">
                        <input type="checkbox" name="create_assets_folder" value="1" @checked(old('create_assets_folder', true)) class="rounded border-[#9ca3af]">
                        Generate assets folder
                    </label>

                    <label class="inline-flex items-center gap-2 text-sm text-[#4b5563] md:col-span-2">
                        <input type="checkbox" name="force_overwrite" value="1" @checked(old('force_overwrite')) class="rounded border-[#9ca3af]">
                        Force overwrite scaffold files if they already exist
                    </label>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button type="submit"
                        class="rounded-lg bg-[#212121] px-4 py-2 text-white font-semibold hover:bg-black transition-colors">
                        Create site and scaffold
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
