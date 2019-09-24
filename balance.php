<?php

namespace app\Console\Commands;

use Illuminate\Console\Command;

class balance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:balance {gateway_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return mixed
     */
    public function handle()
    {
        try {
            $sms = \app()->make($this->argument('gateway_name'));
        } catch (\Exception $e) {
            echo "incorrect gateway_name\n";
            exit;
        }

        $balance = $sms->SmsGetBalance();
        $this->info($balance);
        return $balance;
    }
}
