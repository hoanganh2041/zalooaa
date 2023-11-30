<?php

namespace App\Console\Commands;

use App\Helpers\ZaloAPIHelper;
use App\Jobs\KichBan12Job;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RunJob12Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $zaloAPI;

    protected $signature = 'kichban:12';

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
        $query = "SELECT * FROM zalobgg.LOG_HETHAN_CUOCTRUOC a, zalobgg.VW_NGUOIDUNG_QUANTAM b WHERE da_gui_tin = 0 AND SUBSTR (b.SODT, 3) = SUBSTR (a.DT_TT_KH, 2) AND thang_gui_tin = 202311";
        $data = DB::connection('oracle')->select($query);
        foreach ($data as $obj) {
            KichBan12Job::dispatch($this->zaloAPI, $obj);
        }
    }
}
