<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        User::factory()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com',
        ]);

        /** @var User $admin */
        $admin = User::factory()->create([
            'name'  => 'Super Admin User',
            'email' => 'admin@example.com',
        ]);
        $admin->roles()->attach(Role::whereName('super-admin')->first());
    }
}
