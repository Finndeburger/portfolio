@extends('layouts.app')

@section('content')
	<section class="min-h-screen flex items-center justify-center px-6">
		<div class="w-full max-w-xl rounded-2xl border border-[#bdbdbd] bg-[#f8f8f8] p-8">
			<h1 class="text-3xl font-bold text-[#212121] mb-4">Authentication</h1>
			<p class="text-[#4b5563] mb-6">Quick account access for your portfolio profile.</p>

			<div class="flex flex-wrap gap-3">
				<a href="{{ route('auth.login') }}"
					class="inline-flex items-center rounded-lg bg-[#212121] px-4 py-2 text-white hover:bg-black transition-colors">Login</a>
				<a href="{{ route('auth.register') }}"
					class="inline-flex items-center rounded-lg border border-[#212121] px-4 py-2 text-[#212121] hover:bg-[#ececec] transition-colors">Register</a>
			</div>
		</div>
	</section>
@endsection