@extends('layouts.app')

@section('title', 'LookAtMe - Results')

@section('content')
    <div class="px-10 py-8 max-w-2xl">
        {{-- Search bar with Alpine.js --}}
        <div class="mb-8" x-data="searchApp()" x-init="query = '{{ addslashes($query) }}'">
            <div class="relative w-[500px]">
                <div class="flex items-center rounded-xl h-[60px] bg-[#d9d9d9] border-2 border-[#a6a6a6]"
                    :class="{ 'rounded-b-none': suggestions.length > 0 }">
                    <svg class="absolute left-4" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                        viewBox="0 0 24 24">
                        <g fill="none" stroke="#a6a6a6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                            <path d="m21 21l-4.34-4.34" />
                            <circle cx="11" cy="11" r="8" />
                        </g>
                    </svg>
                    <input x-model="query" @keydown="handleKeydown($event)" @input="updateSuggestions()" type="text"
                        placeholder="Search..."
                        class="w-full h-full pl-12 pr-4 text-lg rounded-xl focus:outline-none bg-transparent text-gray-800"
                        style="line-height: 60px;" />
                </div>

                {{-- Suggestions dropdown --}}
                <div x-show="suggestions.length > 0" x-cloak
                    class="absolute top-full left-0 w-full bg-white rounded-b-xl border border-t-0 border-[#a6a6a6] shadow-lg z-50 overflow-hidden">
                    <template x-for="(site, i) in suggestions" :key="site.slug">
                        <button @click="selectSuggestion(site)" @mouseenter="activeIndex = i"
                            class="flex items-center gap-3 w-full px-4 py-2.5 text-left hover:bg-[#f0f0f0] transition-colors"
                            :class="{ 'bg-[#f0f0f0]': i === activeIndex }">
                            <svg class="w-4 h-4 text-[#a6a6a6] shrink-0" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24">
                                <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2">
                                    <path d="m21 21l-4.34-4.34" />
                                    <circle cx="11" cy="11" r="8" />
                                </g>
                            </svg>
                            <span class="text-gray-800 truncate" x-text="site.title.toLowerCase()"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        {{-- Results --}}
        @if ($query)
            @if ($primary->isEmpty() && !$sponsored)

                <p class="text-[#70757a]">No results found for "{{ $query }}".</p>
            @else
                @if ($sponsored)
                    @include('partials.result-card', ['site' => $sponsored, 'isSponsored' => true])
                @endif

                @foreach ($primary as $site)
                    @include('partials.result-card', ['site' => $site])
                @endforeach

                @foreach ($others as $site)
                    @include('partials.result-card', ['site' => $site])
                @endforeach
            @endif
        @else
            <p class="text-[#70757a]">Type something to search.</p>
        @endif
    </div>
@endsection
