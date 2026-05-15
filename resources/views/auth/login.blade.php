@extends('layouts.app')

@section('title', 'Login - LookAtMe')

@section('content')
    <section class="min-h-screen flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-xl rounded-2xl border border-[#bdbdbd] bg-[#f7f7f7] p-8 shadow-sm">
            <h1 class="text-3xl font-bold text-[#212121] mb-2">Welcome back</h1>
            <p class="text-[#4b5563] mb-8">Sign in with your email and generated password.</p>

            @if ($errors->any())
                <div class="mb-6 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-red-700 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('auth.login.attempt') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-semibold text-[#212121] mb-2">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required
                        class="w-full rounded-lg border border-[#bdbdbd] bg-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-black/20">
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-[#212121] mb-2">Password</label>
                    <input id="password" name="password" type="password" required
                        class="w-full rounded-lg border border-[#bdbdbd] bg-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-black/20">
                </div>

                <label class="inline-flex items-center gap-2 text-sm text-[#4b5563]">
                    <input type="checkbox" name="remember" value="1" class="rounded border-[#9ca3af]">
                    Remember me
                </label>

                <button type="submit"
                    class="w-full rounded-lg bg-[#212121] px-4 py-3 text-white font-semibold hover:bg-black transition-colors">
                    Login
                </button>
            </form>

            <div class="mt-4 border-t border-[#d1d5db] pt-4" x-data="passkeyLogin()">
                <button type="button" @click="loginWithPasskey()"
                    class="w-full rounded-lg border border-[#212121] px-4 py-3 text-[#212121] font-semibold hover:bg-[#ececec] transition-colors">
                    Sign in with passkey
                </button>
                <p x-show="message" x-text="message" class="mt-2 text-xs text-[#4b5563]"></p>
            </div>

            <p class="mt-6 text-sm text-[#4b5563]">
                No account yet?
                <a href="{{ route('auth.register') }}" class="font-semibold text-[#212121] hover:underline">Create one in 30 seconds</a>
            </p>
        </div>
    </section>

    <script>
        function passkeyLogin() {
            return {
                message: '',

                async loginWithPasskey() {
                    this.message = 'Preparing passkey login...';

                    try {
                        const email = document.getElementById('email')?.value || '';
                        const headers = this.headers();

                        const optionsRes = await fetch('{{ route('auth.passkey.login.options') }}', {
                            method: 'POST',
                            headers,
                            body: JSON.stringify({ email }),
                        });

                        const optionsData = await optionsRes.json();
                        if (!optionsRes.ok) throw new Error(optionsData.message || 'Unable to get passkey options.');

                        const publicKey = this.hydrateRequestOptions(optionsData.publicKey);
                        const assertion = await navigator.credentials.get({ publicKey });
                        if (!assertion) throw new Error('No passkey assertion returned by browser.');

                        const verifyRes = await fetch('{{ route('auth.passkey.login.verify') }}', {
                            method: 'POST',
                            headers,
                            body: JSON.stringify({ credential: this.publicKeyCredentialToJson(assertion) }),
                        });

                        const verifyData = await verifyRes.json();
                        if (!verifyRes.ok) throw new Error(verifyData.message || 'Passkey login failed.');

                        window.location.href = verifyData.redirect || '{{ route('home') }}';
                    } catch (error) {
                        this.message = error?.message || 'Passkey login failed.';
                    }
                },

                headers() {
                    return {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    };
                },

                hydrateRequestOptions(publicKey) {
                    return {
                        ...publicKey,
                        challenge: this.base64urlToBuffer(publicKey.challenge),
                        allowCredentials: (publicKey.allowCredentials || []).map((cred) => ({
                            ...cred,
                            id: this.base64urlToBuffer(cred.id),
                        })),
                    };
                },

                base64urlToBuffer(base64url) {
                    const base64 = base64url.replace(/-/g, '+').replace(/_/g, '/');
                    const padding = '='.repeat((4 - (base64.length % 4)) % 4);
                    const str = atob(base64 + padding);
                    const bytes = new Uint8Array(str.length);

                    for (let i = 0; i < str.length; i++) {
                        bytes[i] = str.charCodeAt(i);
                    }

                    return bytes.buffer;
                },

                bufferToBase64url(buffer) {
                    const bytes = new Uint8Array(buffer);
                    let str = '';

                    for (const byte of bytes) {
                        str += String.fromCharCode(byte);
                    }

                    return btoa(str).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/g, '');
                },

                publicKeyCredentialToJson(credential) {
                    return {
                        id: credential.id,
                        type: credential.type,
                        rawId: this.bufferToBase64url(credential.rawId),
                        response: {
                            authenticatorData: this.bufferToBase64url(credential.response.authenticatorData),
                            clientDataJSON: this.bufferToBase64url(credential.response.clientDataJSON),
                            signature: this.bufferToBase64url(credential.response.signature),
                            userHandle: credential.response.userHandle
                                ? this.bufferToBase64url(credential.response.userHandle)
                                : null,
                        },
                        clientExtensionResults: credential.getClientExtensionResults(),
                    };
                },
            };
        }
    </script>
@endsection
