<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

#[Signature('app:create-admin')]
#[Description('Create the admin account for ADMIN_EMAIL, or reset its password if it already exists')]
class CreateAdmin extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $admin_email = config('auth.admin_email');

        if (blank($admin_email)) {
            $this->error('ADMIN_EMAIL is not set. Add it to your environment and try again.');

            return self::FAILURE;
        }

        $admin_email = Str::lower($admin_email);
        $existing_admin = User::where('email', $admin_email)->first();

        $name = text(
            label: 'Name',
            default: $existing_admin->name ?? '',
            required: true,
        );

        $password = password(
            label: 'Password',
            required: true,
            validate: fn (string $value): ?string => Str::length($value) < 12
                ? 'The password must be at least 12 characters.'
                : null,
        );

        $password_confirmation = password(label: 'Confirm password', required: true);

        if ($password !== $password_confirmation) {
            $this->error('The passwords do not match. Nothing was changed.');

            return self::FAILURE;
        }

        $admin = User::updateOrCreate(
            ['email' => $admin_email],
            ['name' => $name, 'password' => $password],
        );

        // Invalidate any "remember me" cookies issued under the old password.
        $admin->forceFill(['remember_token' => Str::random(60)])->save();

        $this->info($admin->wasRecentlyCreated
            ? "Admin account created for {$admin_email}."
            : "Password reset for {$admin_email}.");

        return self::SUCCESS;
    }
}
