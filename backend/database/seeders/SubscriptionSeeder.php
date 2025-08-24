<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subscription;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Example subscriptions
        $subscriptions = [
            [
                'name' => 'Basic Plan',
                'no_of_posts' => 10,
                'cost' => 9.99,
                'is_active' => true,
            ],
            [
                'name' => 'Standard Plan',
                'no_of_posts' => 30,
                'cost' => 19.99,
                'is_active' => true,
            ],
            [
                'name' => 'Premium Plan',
                'no_of_posts' => 100,
                'cost' => 49.99,
                'is_active' => false,
            ],
        ];

        // Insert into database
        foreach ($subscriptions as $sub) {
            Subscription::create($sub);
        }
    }
}
