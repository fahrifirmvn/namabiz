<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateAdminUser extends Command
{
    protected $signature = 'namabiz:create-admin
        {--name= : Nama lengkap administrator}
        {--email= : Alamat email administrator}
        {--password= : Kata sandi administrator (disarankan via prompt interaktif)}';

    protected $description = 'Buat akun administrator Namabiz secara aman tanpa password seed bawaan';

    public function handle(): int
    {
        $name = (string) ($this->option('name') ?: $this->ask('Nama lengkap administrator'));
        $email = strtolower(trim((string) ($this->option('email') ?: $this->ask('Alamat email administrator'))));
        $password = (string) ($this->option('password') ?: $this->secret('Kata sandi administrator (min. 10 karakter disarankan)'));

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class.',email'],
            'password' => ['required', 'string', Password::min(8)],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = new User([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $user->forceFill([
            'is_admin' => true,
            'email_verified_at' => now(),
        ])->save();

        $this->info("Akun administrator '{$user->email}' berhasil dibuat.");

        return self::SUCCESS;
    }
}
