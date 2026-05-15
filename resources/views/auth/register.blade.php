@extends('layouts.app')

@section('title', 'Register - LookAtMe')

@section('content')
    <section class="min-h-screen flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-2xl rounded-2xl border border-[#bdbdbd] bg-[#f7f7f7] p-8 shadow-sm"
            x-data="simpleRegistration()">
            <h1 class="text-3xl font-bold text-[#212121] mb-2">Create account</h1>
            <p class="text-[#4b5563] mb-8">Simple onboarding: display name, gender, email, and generated password.</p>

            @if ($errors->any())
                <div class="mb-6 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-red-700 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('auth.register.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-semibold text-[#212121] mb-2">Display name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required
                        class="w-full rounded-lg border border-[#bdbdbd] bg-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-black/20">
                </div>

                <div>
                    <label for="gender" class="block text-sm font-semibold text-[#212121] mb-2">Gender</label>
                    <select id="gender" name="gender" required
                        class="w-full rounded-lg border border-[#bdbdbd] bg-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-black/20">
                        <option value="">Select gender</option>
                        <option value="male" @selected(old('gender') === 'male')>Male</option>
                        <option value="female" @selected(old('gender') === 'female')>Female</option>
                        <option value="non_binary" @selected(old('gender') === 'non_binary')>Non-binary</option>
                        <option value="other" @selected(old('gender') === 'other')>Other</option>
                        <option value="prefer_not_to_say" @selected(old('gender') === 'prefer_not_to_say')>Prefer not to say</option>
                    </select>
                </div>

                <div>
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                        <label for="email" class="block text-sm font-semibold text-[#212121]">Email (optional)</label>
                        <button type="button" @click="fillFakeEmail()"
                            class="text-sm font-semibold text-[#212121] hover:underline">Use fake @lookatme.com</button>
                    </div>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" x-model="email"
                        class="w-full rounded-lg border border-[#bdbdbd] bg-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-black/20">
                    <p class="mt-2 text-xs text-[#6b7280]">Leave blank to auto-generate one.</p>
                </div>

                <div>
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                        <label for="password" class="block text-sm font-semibold text-[#212121]">Generated password</label>
                        <button type="button" @click="fillGeneratedPassword()"
                            class="text-sm font-semibold text-[#212121] hover:underline">Generate password</button>
                    </div>
                    <input id="password" name="password" type="text" value="{{ old('password') }}" x-model="password" required
                        class="w-full rounded-lg border border-[#bdbdbd] bg-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-black/20">
                    <p class="mt-2 text-xs text-[#6b7280]">Real-word style password with numbers, stored hashed in the database.</p>
                </div>

                <button type="submit"
                    class="w-full rounded-lg bg-[#212121] px-4 py-3 text-white font-semibold hover:bg-black transition-colors">
                    Create account
                </button>
            </form>

            <p class="mt-6 text-sm text-[#4b5563]">
                Already have an account?
                <a href="{{ route('auth.login') }}" class="font-semibold text-[#212121] hover:underline">Login</a>
            </p>
        </div>
    </section>

    <script>
        function simpleRegistration() {
            return {
                email: @js(old('email', '')),
                password: @js(old('password', '')),

                async fillGeneratedPassword() {
                    const res = await fetch('{{ route('auth.password.suggestion') }}');
                    const data = await res.json();
                    this.password = data.password || '';
                },

                async fillFakeEmail() {
                    const name = document.getElementById('name')?.value || 'guest';
                    const res = await fetch(`{{ route('auth.email.suggestion') }}?name=${encodeURIComponent(name)}`);
                    const data = await res.json();
                    this.email = data.email || '';
                }
            };
        }
    </script>
@endsection
