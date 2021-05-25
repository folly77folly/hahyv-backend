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
        $credit->id = 1;
        $credit->name = "C";
        $credit->save();

        $debit = new Type();
        $debit->id = 2;
        $debit->name = "D";
        $debit->save();
    }
}
