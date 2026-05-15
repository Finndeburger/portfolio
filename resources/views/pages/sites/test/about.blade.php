@extends('layouts.browser')

@section('title', 'Test - About')
@section('browser-url', 'https://finnharmens.dev/test/about')

@section('content')
<div class="p-10 space-y-4">
    <h1 class="text-3xl font-bold">About the test mini site</h1>
    <p class="text-gray-700">This is a subpage example loaded from pages/sites/test/about.blade.php.</p>

    <a href="{{ route('sites.show', ['slug' => 'test']) }}" class="text-blue-700 hover:underline">Back to test home</a>
</div>
@endsection
