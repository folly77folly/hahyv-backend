<?php

namespace App\Console\Commands;

use Illuminate\Support\Carbon;
use App\Models\SubscribersList;
use Illuminate\Console\Command;

class UnsubscribeUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:unSubscribe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command un-subscribe users with expired subscription';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $expired = SubscribersList::where('expiry', '<', Carbon::now())->update(['is_active' => false]);
    }
}
