@extends('layouts.app')

@section('title', 'Passkey - LookAtMe')

@section('content')
    <section class="min-h-screen flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-2xl rounded-2xl border border-[#bdbdbd] bg-[#f7f7f7] p-8 shadow-sm">
            <h1 class="text-3xl font-bold text-[#212121] mb-2">Account security</h1>
            <p class="text-[#4b5563] mb-8">You are signed in as <span class="font-semibold">{{ auth()->user()->name }}</span>.</p>

            @if (session('status'))
                <div class="mb-6 rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-green-700 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <div class="mb-8 rounded-xl border border-[#d1d5db] bg-white p-5">
                <h2 class="text-lg font-semibold text-[#212121] mb-2">Passkey setup</h2>

                @if (auth()->user()->passkey_enabled_at)
                    <p class="text-sm text-[#4b5563] mb-4">
                        Passkey profile enabled on {{ auth()->user()->passkey_enabled_at->format('Y-m-d H:i') }}.
                    </p>
                @else
                    <p class="text-sm text-[#4b5563] mb-4">
                        Add a passkey for your device to use secure passwordless sign-in.
                    </p>
                @endif

                <div x-data="passkeyRegister()" class="space-y-4">
                    <button type="button" @click="registerPasskey()"
                        class="rounded-lg bg-[#212121] px-4 py-2 text-white font-semibold hover:bg-black transition-colors">
                        Register passkey on this device
                    </button>

                    <p x-show="message" x-text="message" class="text-sm text-[#4b5563]"></p>
                </div>

                @php
                    $credentials = auth()->user()->passkey_credentials['credentials'] ?? [];
                @endphp

                <div class="mt-6 border-t border-[#e5e7eb] pt-4" x-data="passkeyManager()">
                    <h3 class="text-sm font-semibold text-[#212121] mb-2">Registered passkeys</h3>

                    @if (count($credentials) > 0)
                        <div class="space-y-2">
                            @foreach ($credentials as $credential)
                                <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-[#e5e7eb] bg-[#fafafa] px-3 py-2">
                                    <div>
                                        <p class="text-sm text-[#212121] font-medium">Credential {{ \Illuminate\Support\Str::limit($credential['publicKeyCredentialId'] ?? '', 20, '...') }}</p>
                                        <p class="text-xs text-[#6b7280]">Counter: {{ $credential['counter'] ?? 0 }}</p>
                                    </div>
                                    <button
                                        type="button"
                                        @click="removeCredential('{{ $credential['publicKeyCredentialId'] ?? '' }}')"
                                        class="rounded-md border border-red-200 px-3 py-1.5 text-xs text-red-700 hover:bg-red-50 transition-colors"
                                    >
                                        Remove
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-[#6b7280]">No passkeys registered yet.</p>
                    @endif

                    <p x-show="deleteMessage" x-text="deleteMessage" class="mt-2 text-xs text-[#4b5563]"></p>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('home') }}"
                    class="inline-flex items-center rounded-lg border border-[#212121] px-4 py-2 text-[#212121] hover:bg-[#ececec] transition-colors">
                    Back to home
                </a>

                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.users') }}"
                        class="inline-flex items-center rounded-lg bg-[#212121] px-4 py-2 text-white hover:bg-black transition-colors">
                        Open admin panel
                    </a>
                @endif
            </div>
        </div>
    </section>

    <script>
        function passkeyRegister() {
            return {
                message: '',

                async registerPasskey() {
                    this.message = 'Preparing passkey registration...';

                    try {
                        const headers = this.headers();

                        const optionsRes = await fetch('{{ route('auth.passkey.register.options') }}', {
                            method: 'POST',
                            headers,
                        });

                        const optionsData = await optionsRes.json();
                        if (!optionsRes.ok) throw new Error(optionsData.message || 'Unable to fetch passkey options.');

                        const publicKey = this.hydrateCreationOptions(optionsData.publicKey);
                        const credential = await navigator.credentials.create({ publicKey });
                        if (!credential) throw new Error('No credential returned by browser.');

                        const verifyRes = await fetch('{{ route('auth.passkey.register.verify') }}', {
                            method: 'POST',
                            headers,
                            body: JSON.stringify({ credential: this.publicKeyCredentialToJson(credential) }),
                        });

                        const verifyData = await verifyRes.json();
                        if (!verifyRes.ok) throw new Error(verifyData.message || 'Passkey registration failed.');

                        this.message = verifyData.message || 'Passkey registered successfully.';
                        setTimeout(() => window.location.reload(), 1200);
                    } catch (error) {
                        this.message = error?.message || 'Passkey registration failed.';
                    }
                },

                headers() {
                    return {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    };
                },

                hydrateCreationOptions(publicKey) {
                    return {
                        ...publicKey,
                        challenge: this.base64urlToBuffer(publicKey.challenge),
                        user: {
                            ...publicKey.user,
                            id: this.base64urlToBuffer(publicKey.user.id),
                        },
                        excludeCredentials: (publicKey.excludeCredentials || []).map((cred) => ({
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
                            attestationObject: this.bufferToBase64url(credential.response.attestationObject),
                            clientDataJSON: this.bufferToBase64url(credential.response.clientDataJSON),
                            transports: credential.response.getTransports ? credential.response.getTransports() : [],
                        },
                        clientExtensionResults: credential.getClientExtensionResults(),
                    };
                },
            };
        }

        function passkeyManager() {
            return {
                deleteMessage: '',

                async removeCredential(credentialId) {
                    if (!confirm('Remove this passkey from your account?')) {
                        return;
                    }

                    this.deleteMessage = 'Removing passkey...';

                    try {
                        const res = await fetch('{{ route('auth.passkey.credential.delete') }}', {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            },
                            body: JSON.stringify({ credential_id: credentialId }),
                        });

                        const data = await res.json();

                        if (!res.ok) {
                            throw new Error(data.message || 'Unable to remove passkey.');
                        }

                        this.deleteMessage = data.message || 'Passkey removed.';
                        setTimeout(() => window.location.reload(), 700);
                    } catch (error) {
                        this.deleteMessage = error?.message || 'Unable to remove passkey.';
                    }
                },
            };
        }
    </script>
@endsection
