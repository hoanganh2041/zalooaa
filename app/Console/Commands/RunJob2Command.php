<?php

namespace App\Console\Commands;

use App\Helpers\ZaloAPIHelper;
use App\Jobs\KichBan2Job;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RunJob2Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $zaloAPI;

    protected $signature = 'kichban:2';

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
    public function __construct(ZaloAPIHelper $zaloAPI)
    {
        $this->zaloAPI = $zaloAPI;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //Mỗi lần chạy trên server sau khi upcode phải run  systemctl restart supervisord trước để xóa cache
        $query = "select * from zalobgg.LOG_BAOHONG a, zalobgg.VW_NGUOIDUNG_QUANTAM b where da_gui_tin=0 and substr(b.SODT,3)= substr(SO_DT_TT,2)";
        $data = DB::connection('oracle')->select($query);
        foreach ($data as $obj) {
            KichBan2Job::dispatch($this->zaloAPI, $obj);
        }

        //Log::channel('daily')->info('[BAOHONG] Job has finished running. Total: ' . count($data));
    }
}
