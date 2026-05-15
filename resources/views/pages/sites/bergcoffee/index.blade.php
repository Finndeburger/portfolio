@extends('layouts.browser')

@section('title', 'BergCoffee')
@section('browser-url', 'https://bergcoffee.com')

@push('head')
    <link rel="preload" as="image" type="image/webp" href="{{ asset('assets/BergCoffee/hero-optimized.webp') }}">
@endpush

@section('content')
    <style>
        /* Font - @import must come first */
        @import url('https://fonts.googleapis.com/css2?family=Bungee&display=swap');

        /* Colors */
        * {
            --primary: #336B1E;
            --secondary: #A3A75B;
            --accent: #6B8E23;
            --dark: #453B2B;
            --muted: #807B58;
            --light: #D5D5AA;
        }

        .main {
            font-family: "Bungee", sans-serif !important;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 12px;
        }

        ::-webkit-scrollbar-track {
            background-color: #000000;
            opacity: 0;
        }

        ::-webkit-scrollbar-thumb {
            background-color: var(--primary);
            border-radius: 6px;
            border: 2px solid var(--primary);
        }

        ::-webkit-scrollbar-thumb:hover {
            background-color: var(--primary);
            /* TODO: If needed, use a lighter primary color for hover state instead */
        }

        /* Firefox scrollbar */
        * {
            scrollbar-color: var(--primary) var(--light);
            scrollbar-width: thin;
        }
    </style>

    <div class="main">
        <nav class="flex items-center p-1 bg-(--muted) gap-10">
            <img src="{{ asset('assets/BergCoffee/BergCoffeeLogo.png') }}" alt="BergCoffee logo" class="w-20">
            <a href="{{ route('sites.show', ['slug' => 'bergcoffee']) }}" class="text-(--light) text-xl underline">Home</a>
            <a href="{{ route('sites.show', ['slug' => 'bergcoffee', 'page' => 'about']) }}"
                class="text-(--light) text-xl hover:underline">About</a>
            <a href="{{ route('sites.show', ['slug' => 'bergcoffee', 'page' => 'store']) }}"
                class="text-(--light) text-xl hover:underline">Store</a>
            <a href="{{ route('sites.show', ['slug' => 'bergcoffee', 'page' => 'contact']) }}"
                class="text-(--light) text-xl hover:underline">Contact</a>
        </nav>

        <section id="hero">
            <div class="relative w-full py-32 overflow-hidden">
                <picture>
                    <source srcset="{{ asset('assets/BergCoffee/hero-optimized.webp') }}" type="image/webp">
                    <img src="{{ asset('assets/BergCoffee/hero-optimized.jpg') }}"
                        alt="BergCoffee hero - Photo by Land O'Lakes, Inc. on Unsplash."
                        class="absolute inset-0 w-full h-full object-cover object-center">
                </picture>
                <div class="absolute inset-0 bg-(--dark) opacity-50"></div>
                <div class="relative z-10 flex flex-col items-center justify-center h-full text-center text-(--light)">
                    <h1 class="text-7xl font-bold">BERGCOFFEE</h1>
                    <p class="text-xl font-light mb-4">Grounded</p>
                </div>
            </div>
        </section>

        {{-- About section --}}
        <section id="about" class="bg-(--light) py-16">
            <div class="w-5/6 mx-auto">
                <h2 class="text-4xl font-bold">About Us</h2>
                <hr class="h-px bg-(--primary) border-0 mb-4">
                <div class="flex flex-row mx-auto items-center w-4/6 gap-10 container bg-(--accent) p-4 rounded-xl">
                    <div class="flex-col">
                        <p class="max-w-3xl mx-auto text-center text-sm leading-relaxed px-3">
                            From the middle of nowhere to the middle of somewhere, we at BergCoffee source our beans from
                            the
                            finest farms around the world, ensuring that every cup of coffee you create is like a little
                            angel
                            ####### in your mouth.
                        </p>
                        <div class="flex justify-center mt-6">
                            <a href="{{ route('sites.show', ['slug' => 'bergcoffee', 'page' => 'store']) }}"
                                class="inline-block bg-(--primary) text-(--light) text-sm px-6 py-3 rounded-lg shadow-md hover:bg-(--accent) transition-colors duration-200">
                                Visit Our Store
                            </a>
                        </div>
                    </div>
                    <div>
                        <img src="{{ asset('assets/BergCoffee/bag-small.jpg') }}" alt="Bag - Photo by Point Normal on Unsplash"
                            class="max-w-75 h-auto object-cover object-center rounded-lg shadow-lg">
                    </div>
                </div>
            </div>
        </section>
        
        {{-- Coffee selection - Infinite carousel --}}
        <section id="selection" class="py-16 overflow-hidden">
            <div class="w-5/6 mx-auto">
                <h2 class="text-4xl font-bold">Our Coffee Selection</h2>
                <hr class="h-px bg-(--primary) border-0 mb-8">
            </div>

            <div class="relative" x-data="{
                offset: 0,
                cardWidth: 320,
                totalCards: {{ $coffees->count() }},
                busy: false,
                animate: true,

                init() {
                    this.cardWidth = this.$refs.track.firstElementChild.offsetWidth + 32;
                },

                next() {
                    if (this.busy) return;
                    this.busy = true;
                    const max = this.cardWidth * this.totalCards;

                    this.animate = true;
                    this.offset += this.cardWidth;

                    if (this.offset >= max) {
                        setTimeout(() => {
                            this.animate = false;
                            this.$nextTick(() => {
                                this.offset = 0;
                                requestAnimationFrame(() => {
                                    requestAnimationFrame(() => { this.busy = false; });
                                });
                            });
                        }, 510);
                    } else {
                        setTimeout(() => { this.busy = false; }, 510);
                    }
                },

                prev() {
                    if (this.busy) return;
                    this.busy = true;
                    const max = this.cardWidth * this.totalCards;

                    if (this.offset <= 0) {
                        this.animate = false;
                        this.$nextTick(() => {
                            this.offset = max;
                            requestAnimationFrame(() => {
                                requestAnimationFrame(() => {
                                    this.animate = true;
                                    this.$nextTick(() => {
                                        this.offset -= this.cardWidth;
                                        setTimeout(() => { this.busy = false; }, 510);
                                    });
                                });
                            });
                        });
                    } else {
                        this.animate = true;
                        this.offset -= this.cardWidth;
                        setTimeout(() => { this.busy = false; }, 510);
                    }
                }
            }">
                {{-- Previous button --}}
                <button @click="prev()"
                    class="absolute left-2 top-1/2 -translate-y-1/2 z-20 bg-(--primary) text-(--light) w-10 h-10 rounded-full shadow-lg flex items-center justify-center hover:bg-(--accent) transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                {{-- Track --}}
                <div class="overflow-hidden mx-14">
                    <div class="flex gap-8"
                        :style="`transform: translateX(-${offset}px);${animate ? ' transition: transform 500ms ease-in-out;' : ''}`"
                        x-ref="track">

                        @foreach ($coffees as $coffee)
                            <div class="flex-shrink-0 w-72 bg-(--light) rounded-lg shadow-lg overflow-hidden">
                                <img src="{{ asset('assets/BergCoffee/bag-small.jpg') }}"
                                    alt="{{ $coffee->name }}"
                                    class="w-full h-48 object-cover object-center">
                                <div class="p-4">
                                    <h3 class="text-lg font-semibold mb-1">{{ $coffee->name }}</h3>
                                    <p class="text-xs text-(--muted) mb-1">{{ $coffee->origin }} &middot; {{ ucfirst($coffee->roast_level) }} roast</p>
                                    <p class="text-sm text-(--dark) mb-2">{{ $coffee->description }}</p>
                                    @if ($coffee->flavor_notes)
                                        <div class="flex flex-wrap gap-1 mb-2">
                                            @foreach ($coffee->flavor_notes as $note)
                                                <span class="text-xs bg-(--accent) text-(--light) px-2 py-0.5 rounded-full">{{ $note }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                    <p class="text-sm font-bold text-(--primary)">&euro;{{ number_format($coffee->price_eur, 2) }}</p>
                                </div>
                            </div>
                        @endforeach

                        {{-- Duplicate set for seamless infinite loop --}}
                        @foreach ($coffees as $coffee)
                            <div class="flex-shrink-0 w-72 bg-(--light) rounded-lg shadow-lg overflow-hidden">
                                <img src="{{ asset('assets/BergCoffee/bag-small.jpg') }}"
                                    alt="{{ $coffee->name }}"
                                    class="w-full h-48 object-cover object-center">
                                <div class="p-4">
                                    <h3 class="text-lg font-semibold mb-1">{{ $coffee->name }}</h3>
                                    <p class="text-xs text-(--muted) mb-1">{{ $coffee->origin }} &middot; {{ ucfirst($coffee->roast_level) }} roast</p>
                                    <p class="text-sm text-(--dark) mb-2">{{ $coffee->description }}</p>
                                    @if ($coffee->flavor_notes)
                                        <div class="flex flex-wrap gap-1 mb-2">
                                            @foreach ($coffee->flavor_notes as $note)
                                                <span class="text-xs bg-(--accent) text-(--light) px-2 py-0.5 rounded-full">{{ $note }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                    <p class="text-sm font-bold text-(--primary)">&euro;{{ number_format($coffee->price_eur, 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Next button --}}
                <button @click="next()"
                    class="absolute right-2 top-1/2 -translate-y-1/2 z-20 bg-(--primary) text-(--light) w-10 h-10 rounded-full shadow-lg flex items-center justify-center hover:bg-(--accent) transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </section>

        {{-- Contact and footer --}}
    </div>
@endsection
