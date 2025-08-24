<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; 

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL')],
            [
                'name'        => env('ADMIN_NAME'),
                'password'    => bcrypt(env('ADMIN_PASSWORD')),
                'is_admin'    => true,
                'is_approved' => true,
                
            ]
        );
    }

}
