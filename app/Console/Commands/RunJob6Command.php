<?php

namespace App\Console\Commands;

use App\Helpers\ZaloAPIHelper;
use App\Jobs\KichBan6Job;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RunJob6Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $zaloAPI;

    protected $signature = 'kichban:6';

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
        $query = "select * from zalobgg.zns_thongbao_cuoc where SLTHANG = 202305 and SUBSTR(SO_LIENHE, 2) NOT IN (SELECT SUBSTR(PHONE, 3) FROM API_HISTORY WHERE KICHBAN_ID = 6 AND RES_CODE = 0 AND TO_CHAR(CREATED_DATE, 'yyyyMM') = 202306)";
        $data = DB::connection('oracle')->select($query);
        foreach ($data as $obj) {
            KichBan6Job::dispatch($this->zaloAPI, $obj);
        }
    }
}
