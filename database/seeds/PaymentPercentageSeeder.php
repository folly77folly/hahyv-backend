<?php

use Illuminate\Database\Seeder;
use App\Models\PaymentPercentage;

class PaymentPercentageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $earn_type1 = new PaymentPercentage();
        $earn_type1->id = 1;
        $earn_type1->rate = 70;
        $earn_type1->save();
    }
}
