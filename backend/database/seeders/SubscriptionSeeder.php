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
        // Create 10 test subscriptions
        for ($i = 1; $i <= 10; $i++) {
            Subscription::updateOrCreate(
                ['id' => $i], // Ensure IDs 1-10
                [
                    'name' => "Test Subscription {$i}",
                    'cost' => 100 * $i, // example cost
                    'description' => "This is a test subscription number {$i}."
                ]
            );
        }
    }
}
