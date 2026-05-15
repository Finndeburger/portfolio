<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\ReadableSecrets;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = (string) config('auth.admin_bootstrap_email', 'finnharmens@gmail.com');

        if ($email === '') {
            return;
        }

        $user = User::query()->firstWhere('email', $email);

        if (! $user) {
            $password = ReadableSecrets::password();

            $user = User::query()->create([
                'name' => 'Finn Harmens',
                'email' => $email,
                'gender' => 'male',
                'profile_picture' => 'assets/general/pfpmale.png',
                'password' => $password,
                'remember_token' => Str::random(60),
                'role' => 'admin',
            ]);

            $this->command?->warn('Admin account created. Save this generated password: '.$password);
        }

        if ($user->role !== 'admin') {
            $user->forceFill(['role' => 'admin'])->save();
            $this->command?->info('Admin role assigned to '.$email);
        }
    }
}
