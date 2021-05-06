<?php

use App\Models\EarningType;
use Illuminate\Database\Seeder;

class EarningTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $earn_type1 = new EarningType();
        $earn_type1->id = 1;
        $earn_type1->name = 'card';
        $earn_type1->save();

        $earn_type2 = new EarningType();  
        $earn_type2->id = 2;
        $earn_type2->name = 'wallet';
        $earn_type2->save();

        $earn_type3 = new EarningType();
        $earn_type3->id = 3;
        $earn_type3->name = 'referral';
        $earn_type3->save();

        $earn_type4 = new EarningType();
        $earn_type4->id = 4;
        $earn_type4->name = 'crypto';
        $earn_type4->save();
    }
}
