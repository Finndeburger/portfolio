@extends('layouts.browser')

@section('title', $site->title . ' - Finn Harmens')
@section('browser-url', $site->dummy_url)

@section('content')
    <section class="min-h-full bg-[#f6f7f9] text-[#212121]">
        <div class="max-w-4xl mx-auto px-6 py-16">
            <p class="text-sm uppercase tracking-wide text-[#6b7280] mb-2">Project</p>
            <h1 class="text-5xl font-bold mb-6">{{ $site->title }}</h1>
            <p class="text-lg mb-8">{{ $site->description }}</p>

            @if (!empty($site->tags))
                <div class="flex flex-wrap gap-2 mb-12">
                    @foreach ($site->tags as $tag)
                        <span class="px-3 py-1 rounded-full bg-white border border-[#d1d5db] text-sm">{{ $tag }}</span>
                    @endforeach
                </div>
            @endif

            <a href="/" class="inline-flex items-center px-4 py-2 rounded-lg bg-[#212121] text-white hover:bg-black transition-colors">
                Back to search
            </a>
        </div>
    </section>
@endsection
