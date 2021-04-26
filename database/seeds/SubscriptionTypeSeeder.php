<?php

use Illuminate\Database\Seeder;
use App\Models\SubscriptionType;

class SubscriptionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $basic = new SubscriptionType();
        $basic->name = "basic";
        $basic->period = 1;
        $basic->save();

        $standard = new SubscriptionType();
        $standard->name = "standard discount";
        $standard->period = 3;
        $standard->save();

        $premium = new SubscriptionType();
        $premium->name = "premium discount";
        $premium->period = 6;
        $premium->save();
    }
}
