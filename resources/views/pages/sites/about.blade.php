@extends('layouts.browser')

@section('title', 'About - Finn Harmens')
@section('browser-url', 'https://finnharmens.com/about')

@section('content')
<div style="font-family: 'Roboto Mono', monospace;">
    {{-- Sticky Bar --}}
    <div class="flex flex-row fixed left-0 w-screen p-10 overflow-hidden z-10">
        <h4 class="mb-4 text-[#212121] text-2xl underline">about.</h4>
    </div>
    <div class="flex flex-row justify-end fixed top-15 right-0 w-screen p-10 overflow-hidden z-10">
        <img src="{{ asset('assets/FinnHarmensLogo.jpeg') }}" alt="logo" class="w-18 mr-4"/>
    </div>

    {{-- Name + Image --}}
    <section class="bg-[#f37c90]">
        <div class="p-20">
            <div class="name relative flex items-center justify-center">
                <div class="name-image absolute w-full z-2 flex justify-center items-center" style="transform: translate3d(0px, 0px, 0px) scale(1) rotate(-3deg) skew(0deg); top: 0; left: 0;">
                    <img src="{{ asset('assets/Barcelona.jpeg') }}" alt="Barcelona" class="fade-in" style="width: 350px; max-width: 80%; box-shadow: rgba(255, 23, 68, 0.557) 15px 15px 5px 5px; animation: fadeIn 2s ease 1s forwards; opacity: 0;"/>
                </div>
                <div class="relative z-1">
                    <h1 class="flex items-center justify-center text-center h-full text-[#212121]" style="font-size: 280px; line-height: 1; font-weight: 700;">
                        <span>FINN<br/>HARMENS</span>
                    </h1>
                </div>
            </div>
        </div>

        <div class="flex flex-row justify-between items-center p-5">
            <h4 class="text-[#212121] text-3xl">1.0</h4>
            <h4 class="text-[#212121] text-3xl">&darr;</h4>
        </div>
    </section>

    {{-- About --}}
    <section class="bg-[#f37c90]">
        <div class="w-5/6 mx-auto flex flex-row justify-between py-20 text-[#212121]">
            <span class="border-l-2 border-[#c4455a] pl-3">About</span>
            <div>
                <p class="text-right"><span class="text-5xl">full stack web developer</span></p>
                <p class="text-right"><span class="text-5xl">game developer</span></p>
                <p class="text-right"><span class="text-5xl">app developer</span></p>
                <p class="text-right"><span class="text-5xl">database engineer</span></p>
                <p class="text-right"><span class="text-5xl">writer</span></p>
                <p class="text-right"><span class="text-5xl">worldbuilder</span></p>
                <p class="text-right"><span class="text-5xl">producer</span></p>
            </div>
        </div>
    </section>

    {{-- GitHub link --}}
    <section class="bg-[#f37c90]">
        <div class="w-5/6 mx-auto flex flex-row justify-between py-20 text-[#212121]">
            <div>
                <p class="text-left"><span class="text-3xl border-r-2 text-[#FF1744] border-[#FF1744] pr-2">look on</span></p>
                <a href="https://github.com/Finndeburger" class="hover:underline">
                    <div class="flex flex-row items-center gap-2">
                        <p class="text-center"><span class="text-3xl">GitHub</span></p>
                        <img alt="github" src="https://raw.githubusercontent.com/lucide-icons/lucide/785b2c63769c9ab1f39b4e51048618208a74f397/icons/github.svg" height="24">
                    </div>
                </a>
                <p class="text-right"><span class="text-3xl text-[#FF1744]">coming soon</span></p>
            </div>
            <span class="border-r-2 border-[#c4455a] pr-3">Portfolio</span>
        </div>
    </section>

    {{-- All projects --}}
    <section class="bg-[#ecf4f0] flex justify-center mx-auto text-black text-5xl items-center p-20">
        <a href="https://github.com/Finndeburger" class="underline">
            <h2>View all projects</h2>
        </a>
    </section>

    {{-- Contact footer --}}
    <section>
        <div class="flex flex-row justify-between w-5/6 mx-auto p-20 items-center">
            <div class="flex flex-col">
                <span class="text-[#ff1744] mb-5 text-3xl">Contact</span>
                <img class="h-10" src="{{ asset('assets/FinnHarmensLogoBlack.jpeg') }}" alt="Black logo">
            </div>
            <div class="flex flex-col">
                <form class="flex flex-col text-gray-800">
                    <span>E-Mail</span>
                    <input type="text" class="flex items-center rounded-xl bg-[#d9d9d9] border-2 border-[#a6a6a6] text-lg rounded-xl focus:outline-none bg-transparent">
                </form>
            </div>
        </div>
    </section>
</div>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
