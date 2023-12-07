<?php

namespace App\Models;

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['id' => Role::ADMIN_ID, 'name' => 'Admin']);
        Role::create(['id' => Role::CUSTOMER_ID, 'name' => 'Customer']);
    }
}
