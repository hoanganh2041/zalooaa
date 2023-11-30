<?php

namespace App\Jobs;

use App\Helpers\ZaloAPIHelper;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class KichBan7Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $zaloAPI, $obj;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ZaloAPIHelper $zaloAPI, $obj)
    {
        $this->zaloAPI = $zaloAPI;
        $this->obj = $obj;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $templateId = 266193;
            $kichBanId = 7;
            $phone = '84' . substr($this->obj->so_lienhe, 1);
            $templateData = [
                'thang' => Carbon::createFromFormat('Ym', $this->obj->slthang)->format('m/Y'),
                'ma_tt' => $this->obj->ma_tt,
                'tien_no' => number_format($this->obj->tong_no, 0, ',', '.') . ' VNĐ',
                'ten_KH' => $this->obj->ten_thanhtoan,
                'ngay' => $this->obj->ngay_thanhtoan
            ];
            $this->zaloAPI->SendZnsTemplate($phone, $templateId, $templateData, $kichBanId);
        } catch (\Exception $ex) {
            Log::channel('daily')->error('ERROR-KICHBAN-7: ' . $ex->getMessage());
        }

    }
}
