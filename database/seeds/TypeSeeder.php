<?php

use App\Models\Type;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $credit = new Type();
        $credit->name = "C";
        $credit->save();

        $credit = new Type();
        $credit->name = "D";
        $credit->save();
    }
}
