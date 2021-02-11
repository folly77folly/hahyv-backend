<?php

use App\Models\ReferEarnSetup;
use Illuminate\Database\Seeder;

class ReferEarnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $setup = new ReferEarnSetup();
        $setup->number_to_refer = 1;
        $setup->amount = 1;
        $setup->save();
    }
}
