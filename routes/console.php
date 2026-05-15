<?php

use App\Models\User;
use App\Support\ReadableSecrets;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('auth:bootstrap-admin {email?}', function (?string $email = null) {
    $targetEmail = $email ?: (string) config('auth.admin_bootstrap_email', 'finnharmens@gmail.com');

    if ($targetEmail === '') {
        $this->error('No admin email configured.');
        return;
    }

    $user = User::query()->firstWhere('email', $targetEmail);

    if (! $user) {
        $password = ReadableSecrets::password();

        $user = User::query()->create([
            'name' => 'Finn Harmens',
            'email' => $targetEmail,
            'gender' => 'male',
            'profile_picture' => 'assets/general/pfpmale.png',
            'password' => $password,
            'remember_token' => Str::random(60),
            'role' => 'admin',
        ]);

        $this->warn('Admin account did not exist, so it was created.');
        $this->warn('Generated password: '.$password);
    }

    if ($user->role !== 'admin') {
        $user->forceFill(['role' => 'admin'])->save();
    }

    $this->info('Admin ready: '.$targetEmail);
})->purpose('Create or promote an admin user by email');
