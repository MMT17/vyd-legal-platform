<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class MakeAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:make-admin {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign the administrador role to a user by email.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = (string) $this->argument('email');

        $user = User::query()
            ->where('email', $email)
            ->first();

        if (! $user) {
            $this->error("No user found with email [{$email}].");

            return self::FAILURE;
        }

        Role::findOrCreate('administrador', 'web');

        $user->assignRole('administrador');

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->info("Role [administrador] assigned to [{$email}].");

        return self::SUCCESS;
    }
}
