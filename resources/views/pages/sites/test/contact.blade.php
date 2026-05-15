@extends('layouts.browser')

@section('title', 'Test - Contact')
@section('browser-url', 'https://finnharmens.dev/test/contact')

@section('content')
<div class="p-10 space-y-4">
    <h1 class="text-3xl font-bold">Contact</h1>
    <p class="text-gray-700">Another subpage example. Use this pattern per mini site folder.</p>

    <a href="{{ route('sites.show', ['slug' => 'test']) }}" class="text-blue-700 hover:underline">Back to test home</a>
</div>
@endsection
