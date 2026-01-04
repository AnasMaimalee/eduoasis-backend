<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate([
            'name' => 'superadmin',
            'guard_name' => 'sanctum',
        ]);

        Role::firstOrCreate([
            'name' => 'administrator',
            'guard_name' => 'sanctum',
        ]);

        Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'sanctum',
        ]);
    }
}
