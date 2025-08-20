<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::firstOrCreate(
      ['email' => 'admin@example.com'],
      [
        'name' => 'Admin',
        'password' => bcrypt('Admin@12345'),
        'is_admin' => true,
        'is_approved' => true, // clubhouse-style gate
        'is_verified' => true, // email verified
        'national_id' => null,
      ]
    );
  

    }
}
