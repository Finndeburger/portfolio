<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'gender' => ['required', 'in:male,female,non_binary,other,prefer_not_to_say'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
        ]);

        $email = $validated['email'] ?: $this->makeFakeEmail($validated['name']);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $email,
            'gender' => $validated['gender'],
            'profile_picture' => $this->defaultProfilePictureForGender($validated['gender']),
            'password' => $validated['password'],
            'remember_token' => Str::random(60),
            'role' => 'user',
        ]);

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->route('auth.passkey')
            ->with('status', 'Account created. You are now signed in.');
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $remember = (bool) ($credentials['remember'] ?? false);

        if (! Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ], $remember)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Invalid login credentials.',
                ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function passwordSuggestion(): JsonResponse
    {
        return response()->json([
            'password' => $this->makeReadablePassword(),
        ]);
    }

    public function fakeEmailSuggestion(Request $request): JsonResponse
    {
        $name = (string) $request->query('name', 'guest');

        return response()->json([
            'email' => $this->makeFakeEmail($name),
        ]);
    }

    public function passkey(): View
    {
        return view('auth.passkey');
    }

    public function enablePasskey(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'label' => ['nullable', 'string', 'max:120'],
        ]);

        /** @var User $user */
        $user = $request->user();

        $user->forceFill([
            'passkey_enabled_at' => now(),
            'passkey_credentials' => [
                'label' => $validated['label'] ?? 'Primary device',
                'setup_mode' => 'placeholder',
                'updated_at' => now()->toIso8601String(),
            ],
        ])->save();

        return back()->with('status', 'Passkey profile created. Full WebAuthn verification can be added next.');
    }

    private function defaultProfilePictureForGender(string $gender): string
    {
        return match ($gender) {
            'male' => 'assets/general/pfpmale.png',
            'female' => 'assets/general/pfpfemale.png',
            default => 'assets/general/pfpnb.png',
        };
    }

    private function makeFakeEmail(string $name): string
    {
        $base = Str::slug(Str::lower($name), '.');
        $base = $base !== '' ? $base : 'guest';

        do {
            $candidate = sprintf('%s.%s@lookatme.com', $base, random_int(100, 999));
        } while (User::query()->where('email', $candidate)->exists());

        return $candidate;
    }

    private function makeReadablePassword(): string
    {
        $adjectives = [
            'brisk', 'clear', 'mellow', 'steady', 'bright', 'silent', 'nimble', 'gentle',
            'happy', 'fierce', 'swift', 'calm', 'shiny', 'cozy', 'sharp', 'noble',
        ];

        $nouns = [
            'river', 'forest', 'comet', 'canyon', 'coffee', 'anchor', 'summit', 'ocean',
            'ember', 'harbor', 'signal', 'falcon', 'planet', 'studio', 'voyage', 'thunder',
        ];

        return sprintf(
            '%s-%s-%d',
            $adjectives[array_rand($adjectives)],
            $nouns[array_rand($nouns)],
            random_int(10, 99)
        );
    }
}
