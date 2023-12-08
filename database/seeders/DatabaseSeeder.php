<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use app\Models\Authenication\Permissions;
use app\Models\Authenication\Role;
use app\Models\Authenication\User;
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
        //
        foreach ($this->roles as $role => $permissions) {
            $role = Role::create(['name' => $role]);
            foreach ($permissions as $permission) {
                $permission = Permissions::create(['name' => $permission]);
                $role->permissions()->attach($permission);
            }
        }
        $users = User::create([
            'name' => Str::random(10),
            'email' => Str::random(10).'@gmail.com',
            'password' => Hash::make('password'),
        ]);
        $role = Role::where('name', 'CUSTOMER')->first();
        $users->role()->attach($role);
    }
}
