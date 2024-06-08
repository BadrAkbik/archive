<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        Role::create(['name' => 'owner']);
        Role::create(['name' => 'user']);
        Role::create(['name' => 'admin']);

        $permissions = include base_path('data/permissions.php');
        foreach ($permissions as $key => $value) {
            Permission::create([
                'name' => $key,
                'name_ar' => $value,
            ]);
        }
    }
}
