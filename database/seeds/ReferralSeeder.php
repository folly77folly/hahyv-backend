<?php

use App\User;
use App\Traits\ReferralTrait;
use Illuminate\Database\Seeder;

class ReferralSeeder extends Seeder
{
    use ReferralTrait;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $users = User::where('username', '!=' , null )->get();
        foreach ($users as $user){
            $token = Str::uuid()->toString();
            $user->rf_token =$token;
            $url = env('BASE_URL','http://127.0.0.1:3001').'/signup/?rf_token='.$token;
            $response = $this->shortUrl($url);
            $user->referral_url = $response;
            $user->save();
        }
    }
}
