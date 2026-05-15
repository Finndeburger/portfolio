@extends('layouts.browser')

@section('title', 'Test')
@section('browser-url', 'https://finnharmens.dev/test')

@section('content')
<div class="p-10 space-y-4">
    <h1 class="text-3xl font-bold">Hallo, test test.</h1>
    <p class="text-gray-700">This is the mini site homepage. Only this root page is indexed for LookAtMe search.</p>

    <nav class="flex gap-4 text-sm">
        <a href="{{ route('sites.show', ['slug' => 'test']) }}" class="text-blue-700 hover:underline">Home</a>
        <a href="{{ route('sites.show', ['slug' => 'test', 'page' => 'about']) }}" class="text-blue-700 hover:underline">About</a>
        <a href="{{ route('sites.show', ['slug' => 'test', 'page' => 'contact']) }}" class="text-blue-700 hover:underline">Contact</a>
    </nav>
</div>
@endsection
