@extends('layouts.browser')

@section('title', $site->title)
@section('browser-url', $site->dummy_url)

@section('content')
    <div class="p-10">
        <h1 class="text-3xl font-bold mb-4">{{ $site->title }}</h1>
        <p>{{ $site->description }}</p>
    </div>

@endsection
