<?php

namespace App\Console\Commands;

use App\Helpers\ZaloAPIHelper;
use App\Jobs\KichBan7Job;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RunJob7Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $zaloAPI;

    protected $signature = 'kichban:7';

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
        $query = "select * from zalobgg.ZNS_NHACNO_CUOC where slthang=202310";
        $data = DB::connection('oracle')->select($query);
        foreach ($data as $obj) {
            KichBan7Job::dispatch($this->zaloAPI, $obj);
        }
    }
}
