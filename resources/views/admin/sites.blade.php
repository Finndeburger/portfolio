@extends('layouts.app')

@section('title', 'Admin - Sites')

@section('content')
    <section class="min-h-screen px-6 py-10">
        <div class="max-w-6xl mx-auto">
            <h1 class="text-3xl font-bold text-[#212121] mb-2">Admin panel</h1>
            <p class="text-[#4b5563] mb-8">Sites tab</p>

            @include('admin.tabs')

            <div class="space-y-4">
                @foreach ($sites as $entry)
                    @php
                        $site = $entry['model'];
                        $pages = $entry['pages'];
                    @endphp
                    <article class="rounded-xl border border-[#d1d5db] bg-white p-5">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <h2 class="text-xl font-semibold text-[#212121]">{{ $site->title }}</h2>
                                <p class="text-sm text-[#6b7280]">/{{ $site->slug }}</p>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('sites.show', ['slug' => $site->slug]) }}" target="_blank"
                                    class="rounded-lg border border-[#bdbdbd] px-3 py-1.5 text-sm text-[#212121] hover:bg-[#ececec] transition-colors">
                                    Open site
                                </a>
                                <a href="{{ route('admin.sites.info', $site) }}"
                                    class="rounded-lg bg-[#212121] px-3 py-1.5 text-sm text-white hover:bg-black transition-colors">
                                    Site info
                                </a>
                            </div>
                        </div>

                        <div class="mt-4">
                            <p class="text-sm font-semibold text-[#374151] mb-2">Subpages</p>
                            @if (count($pages) > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($pages as $page)
                                        <a href="{{ route('sites.show', ['slug' => $site->slug, 'page' => $page]) }}" target="_blank"
                                            class="rounded-full border border-[#d1d5db] px-3 py-1 text-xs text-[#374151] hover:bg-[#f9fafb] transition-colors">
                                            {{ $page }}
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-[#6b7280]">No dedicated subpages found in the view folder.</p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endsection
