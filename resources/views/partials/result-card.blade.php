<div class="mb-6">

    @if ($isSponsored ?? false)
        <div class="text-xs text-[#70757a] mb-1">Sponsored</div>
    @endif

    <p class="text-sm text-[#202124] truncate">{{ $site->dummy_url }}</p>

    <a href="/sites/{{ $site->slug }}" class="text-xl text-[#1a0dab] hover:underline font-medium">

        {{ $site->title }}

    </a>

    <p class="text-sm text-[#4d5156] mt-1">{{ $site->description }}</p>

</div>
