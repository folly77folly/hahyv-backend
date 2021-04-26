<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserSeeder::class);
        // $this->call(FollowerSeeder::class);
        // $this->call(CountrySeeder::class);
        // $this->call(PostTypeSeeder::class);
        // $this->call(PreferenceSeeder::class);
        // $this->call(SubscriptionTypeSeeder::class);
        // $this->call(TypeSeeder::class);

        // $this->call(RoleSeeder::class);
        // $this->call(ReferEarnSeeder::class);
        // $this->call(ReferralSeeder::class);

        $this->call(EarningTypeSeeder::class);
        $this->call(PaymentPercentageSeeder::class);
    }
}
