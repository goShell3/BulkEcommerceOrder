<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class EnsureAdminRole extends Command
{
    protected $signature = 'admin:ensure-role';
    protected $description = 'Ensure admin user has admin role';

    public function handle()
    {
        $adminRole = Role::where('slug', 'admin')->first();
        if (!$adminRole) {
            $this->error('Admin role not found!');
            return;
        }

        $adminUser = User::where('email', 'admin@example.com')->first();
        if (!$adminUser) {
            $this->error('Admin user not found!');
            return;
        }

        if (!$adminUser->hasRole('admin')) {
            $adminUser->roles()->attach($adminRole);
            $this->info('Admin role assigned to admin user.');
        } else {
            $this->info('Admin user already has admin role.');
        }
    }
} 