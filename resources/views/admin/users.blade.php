@extends('layouts.app')

@section('title', 'Admin - Users')

@section('content')
    <section class="min-h-screen px-6 py-10">
        <div class="max-w-6xl mx-auto">
            <h1 class="text-3xl font-bold text-[#212121] mb-2">Admin panel</h1>
            <p class="text-[#4b5563] mb-8">Users tab</p>

            @include('admin.tabs')

            @if (session('status'))
                <div class="mb-6 rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-green-700 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <div class="mb-4 flex justify-end">
                <form method="POST" action="{{ route('admin.users.destroy-all') }}"
                    onsubmit="return confirm('Delete all users except your own admin account?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-white font-semibold hover:bg-red-700 transition-colors">
                        Delete all users
                    </button>
                </form>
            </div>

            <div class="overflow-x-auto rounded-xl border border-[#d1d5db] bg-white">
                <table class="min-w-full text-sm">
                    <thead class="bg-[#f3f4f6] text-[#374151]">
                        <tr>
                            <th class="px-4 py-3 text-left">ID</th>
                            <th class="px-4 py-3 text-left">Name</th>
                            <th class="px-4 py-3 text-left">Email</th>
                            <th class="px-4 py-3 text-left">Role</th>
                            <th class="px-4 py-3 text-left">Gender</th>
                            <th class="px-4 py-3 text-left">Passkey</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr class="border-t border-[#e5e7eb]">
                                <td class="px-4 py-3">{{ $user->id }}</td>
                                <td class="px-4 py-3">{{ $user->name }}</td>
                                <td class="px-4 py-3">{{ $user->email }}</td>
                                <td class="px-4 py-3">{{ $user->role }}</td>
                                <td class="px-4 py-3">{{ $user->gender }}</td>
                                <td class="px-4 py-3">{{ $user->passkey_enabled_at ? 'Enabled' : 'No' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <form method="POST" action="{{ route('admin.users.role', $user) }}" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="role" value="{{ $user->role === 'admin' ? 'user' : 'admin' }}">
                                        <button type="submit"
                                            class="rounded-md border border-blue-200 px-3 py-1.5 text-blue-700 hover:bg-blue-50 transition-colors">
                                            {{ $user->role === 'admin' ? 'Demote' : 'Promote' }}
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.users.reset-password', $user) }}"
                                        onsubmit="return confirm('Reset password for this user?')" class="inline ml-2">
                                        @csrf
                                        <button type="submit"
                                            class="rounded-md border border-amber-200 px-3 py-1.5 text-amber-700 hover:bg-amber-50 transition-colors">
                                            Reset password
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                        onsubmit="return confirm('Delete this user?')" class="inline ml-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="rounded-md border border-red-200 px-3 py-1.5 text-red-700 hover:bg-red-50 transition-colors">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-[#6b7280]">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
