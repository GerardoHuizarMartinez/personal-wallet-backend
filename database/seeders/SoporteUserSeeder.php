<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class SoporteUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRoleId = Role::where('slug', 'administrador')->value('id');

        User::firstOrCreate(
            ['email' => 'soporte@test.com'],
            [
                'name' => 'Soporte',
                'first_last_name' => 'Tidingo',
                'gender' => 'Male',
                'password' => bcrypt('123qwe2'),
                'role_id' => $adminRoleId,
                'status' => 'Active',
            ]
        );
    }
}
