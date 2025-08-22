<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    { 
            Subscription::create([
            'name' => 'Basic',
            'no_of_posts' => 10,
            'cost' => 50.0,
        ]);

        Subscription::create([
            'name' => 'Standard',
            'no_of_posts' => 50,
            'cost' => 150.0,
        ]);

        Subscription::create([
            'name' => 'Premium',
            'no_of_posts' => 200,
            'cost' => 500.0,
        ]);
    }

        
    
}
