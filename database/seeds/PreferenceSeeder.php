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
        $all->preference = "all";
        $all->save();

        $all = new Preference();
        $all->preference = "straight";
        $all->save();

        $all = new Preference();
        $all->preference = "lgbt";
        $all->save();

    }
}
