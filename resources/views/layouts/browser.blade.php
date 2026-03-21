<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'LookAtMe')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=roboto-mono:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="flex flex-col h-screen bg-[#dee1e6]">
        {{-- Browser Bar --}}
        <div class="flex items-center h-10 bg-[#dee1e6] border-b border-[#b0b0b0] px-3 gap-3 shrink-0">
            {{-- Window dots --}}
            <div class="flex gap-1.5">
                <span class="w-3 h-3 rounded-full bg-[#ff5f56]"></span>
                <span class="w-3 h-3 rounded-full bg-[#ffbd2e]"></span>
                <span class="w-3 h-3 rounded-full bg-[#27c93f]"></span>
            </div>

            {{-- Back button --}}
            <a href="javascript:history.back()" class="flex items-center text-[#5f6368] hover:text-black transition-colors cursor-pointer" aria-label="Go back">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/>
                </svg>
            </a>

            {{-- URL bar --}}
            <div class="flex-1 flex items-center h-7 bg-white rounded-full px-4 text-sm text-[#5f6368] select-all overflow-hidden">
                <svg class="w-3.5 h-3.5 mr-2 text-[#5f6368] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <span class="truncate">@yield('browser-url', request()->path())</span>
            </div>
        </div>

        {{-- Content --}}
        <div class="flex-1 overflow-y-auto h-0">
            @yield('content')
        </div>
    </div>
</body>
</html>
