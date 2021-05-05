<?php

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $admin = new Role();
        $admin->id = 1;
        $admin->name = 'admin';
        $admin->save();

        $user = new Role();
        $user->id = 2;
        $user->name = 'user';
        $user->save();
    }
}
