<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

#[Signature('gulraiz:make-admin {email : Email address to log in with}')]
#[Description('Create the site admin account, or give an existing account admin access')]
class MakeAdmin extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = strtolower($this->argument('email'));
        $user = User::query()->where('email', $email)->first();

        if ($user === null) {
            $name = $this->ask('Name', 'Mohammed Gulraiz');
            $password = $this->secret('Password');

            $validator = Validator::make(
                ['email' => $email, 'password' => $password],
                ['email' => ['required', 'email'], 'password' => ['required', Password::defaults()]],
            );

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $this->error($error);
                }

                return self::FAILURE;
            }

            $user = User::create(['name' => $name, 'email' => $email, 'password' => $password]);
        }

        $user->forceFill(['is_admin' => true, 'email_verified_at' => $user->email_verified_at ?? now()])->save();

        $this->info("{$user->email} is now an admin.");

        return self::SUCCESS;
    }
}
