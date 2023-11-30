<?php

namespace App\Console\Commands;

use App\Constants\ZaloURL;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ZaloAuth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zalo:gettoken';

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
     * @return int
     */
    public function handle()
    {
        $zaloToken = DB::table('zalo_token')->latest('id')->first();
        $refresh_token = $zaloToken->refresh_token;

        $apiURL = ZaloURL::GET_ACCESS_TOKEN . '?app_id=308588668650229744&grant_type=refresh_token&refresh_token=' . $refresh_token;
        $headers = [
            'secret_key' => 'X7yq4R5OWYL8kHP6yODV'
        ];
        $response = Http::withHeaders($headers)->post($apiURL);
        $data = json_decode($response->body());

        $access_token = $data->access_token;
        $refresh_token = $data->refresh_token;

        DB::table('zalo_token')->truncate();
        DB::table('zalo_token')->insert([
            'access_token' => $access_token,
            'refresh_token' => $refresh_token,
            'created_at' => Carbon::now()
        ]);
    }
}
