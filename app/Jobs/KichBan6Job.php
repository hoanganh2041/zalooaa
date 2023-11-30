<?php

namespace App\Jobs;

use App\Helpers\ZaloAPIHelper;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KichBan6Job implements ShouldQueue
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
            $templateId = 260524;
            $kichBanId = 6;
            $phone = '84' . substr($this->obj->so_lienhe, 1);
            $templateData = [
                'thang' => Carbon::createFromFormat('Ym', $this->obj->slthang)->format('m/Y'),
                'ma_tt' => $this->obj->ma_tt,
                'no_phat_sinh' => number_format($this->obj->tien, 0, ',', '.') . ' VNĐ',
                'ten' => $this->obj->coquantt
            ];
            $this->zaloAPI->SendZnsTemplate($phone, $templateId, $templateData, $kichBanId);
        } catch (\Exception $ex) {
            Log::channel('daily')->error('ERROR-KICHBAN-6: ' . $ex->getMessage());
        }

    }
}
