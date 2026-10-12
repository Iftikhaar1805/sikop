<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SetupSuperAdmin extends Command
{
    protected $signature = 'koperasi:setup-admin';

    protected $description = 'Create or reset the Koperasi super admin account';

    public function handle(): int
    {
        $name = trim((string) $this->ask('Nama super admin'));
        $email = strtolower(trim((string) $this->ask('Email super admin')));

        $validator = Validator::make(
            ['name' => $name, 'email' => $email],
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->components->error($error);
            }

            return self::FAILURE;
        }

        $password = (string) $this->secret('Kata sandi baru (minimal 8 karakter)');
        $passwordConfirmation = (string) $this->secret('Ulangi kata sandi');

        $passwordValidator = Validator::make(
            ['password' => $password, 'password_confirmation' => $passwordConfirmation],
            ['password' => ['required', 'string', 'min:8', 'confirmed']]
        );

        if ($passwordValidator->fails()) {
            foreach ($passwordValidator->errors()->all() as $error) {
                $this->components->error($error);
            }

            return self::FAILURE;
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'role' => 'super_admin',
            ]
        );

        $this->components->info('Akun super admin berhasil disimpan.');
        $this->line('Database: '.config('database.default').' / '.config('database.connections.'.config('database.default').'.database'));
        $this->line('Gunakan email yang baru dimasukkan dan kata sandi yang baru dibuat untuk masuk.');

        return self::SUCCESS;
    }
}
