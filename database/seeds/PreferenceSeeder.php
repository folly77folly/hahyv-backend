<?php

use Illuminate\Database\Seeder;
use App\Models\Preference;

class PreferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $all = new Preference();
        $all->id = 1;
        $all->preference = "all";
        $all->save();

        $straight = new Preference();
        $straight->id = 2;
        $straight->preference = "straight";
        $straight->save();

        $lgbt = new Preference();
        $lgbt->id = 3;
        $lgbt->preference = "lgbt";
        $lgbt->save();

    }
}
