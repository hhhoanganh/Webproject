<?php

namespace Database\Seeders;

use App\Models\Authenication\Enum\RoleEnum;
use App\Models\Authenication\Permissions;
use App\Models\Authenication\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    private $roles = [
        'CUSTOMER' => ['READ', 'UPDATE'],
        'ADMIN' => ['UPDATE'],
        'SUPERADMIN' => ['CREATE', 'READ', 'UPDATE', 'DELETE'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed roles and permissions
        foreach ($this->roles as $roleName => $permissions) {
            $role = Role::create(['name' => $roleName]);

            foreach ($permissions as $permissionName) {
                $permission = Permissions::create(['name' => $permissionName]);
                $permission->roles()->attach($role);
            }
        }

        // Seed users with roles
        for ($x = 0; $x <= 10; $x++) {
            $user = User::create([
                'name' => Str::random(10),
                'email' => Str::random(10) . '@gmail.com',
                'password' => Hash::make('123456'),
                'phone' => Str::random(10),
                'status'=> 1,
            ]);

            $role = Role::where('name', RoleEnum::CUSTOMER)->first();
            $role->users()->attach($user);
        }
    }
}
