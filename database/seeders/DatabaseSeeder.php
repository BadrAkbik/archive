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
        Role::firstOrCreate(['name' => 'owner']);
        Role::firstOrCreate(['name' => 'user']);
        Role::firstOrCreate(['name' => 'admin']);

        $permissions = include base_path('data/permissions.php');
        foreach ($permissions as $key => $value) {
            Permission::firstOrCreate([
                'name' => $key,
                'name_ar' => $value,
            ]);
        }
        $user = User::where('username', 'admin')->get();
        if (!$user) {
            User::create([
                'name' => 'admin',
                'username' => 'admin',
                'email' => 'admin@admin.com',
                'password' => bcrypt('admin123456'),
                'role_id' => 1
            ]);
        }
    }
}
