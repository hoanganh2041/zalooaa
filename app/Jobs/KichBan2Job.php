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

class KichBan2Job implements ShouldQueue
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
            $userId = $this->obj->idzalo;
            //$userId = '5531958733751126635';
            $kichBanId = 2;
            $msgChiTiet = '• Xin lỗi Quý khách về sự bất tiện này. Quý khách có thể liên hệ với:<br>';
            $msgChiTiet .= '- Nhân viên kỹ thuật xử lý sự cố %s, %s <br>';
            $msgChiTiet .= '- Nhân viên kinh doanh %s, %s';
            $msgChiTiet = sprintf($msgChiTiet,  $this->obj->ten_nvkt, $this->obj->dt_kt, $this->obj->ten_nvkd, $this->obj->dt_kd);

            $message = [
                'attachment' => [
                    'type' => 'template',
                    'payload' => [
                        'template_type' => 'transaction_booking',
                        'language' => 'VI',
                        'elements' => [
                            [
                                'image_url' => 'https://zaloapi.vnptbacgiang.com.vn/storage/zalo/baohong.jpg',
                                'type' => 'banner'
                            ],
                            [
                                'type' => 'header',
                                'content' => 'Tiếp nhận báo hỏng',
                                'align' => 'center',
                            ],
                            [
                                'type' => 'text',
                                'content' => '• Cảm ơn Quý khách đã luôn tin tưởng và sử dụng dịch vụ của VNPT Bắc Giang.<br>• Thông tin tiếp nhận báo hỏng của Quý khách như sau.',
                                'align' => 'left',
                            ],
                            [
                                'type' => 'table',
                                'content' => [
                                    [
                                        'key' => 'Mã thuê bao',
                                        'value' => $this->obj->ma_tb,
                                    ],
                                    [
                                        'key' => 'Tên thuê bao',
                                        'value' => $this->obj->ten_tb,
                                    ],
                                    [
                                        'key' => 'Địa chỉ lắp đặt',
                                        'value' => $this->obj->diachi_ld,
                                    ],
                                    [
                                        'key' => 'Thời gian xử lý',
                                        'value' => 'Trước ' . Carbon::createFromFormat('Y-m-d H:i:s', $this->obj->han_xuly)->format('H:i d/m/Y'),
                                    ],
                                ],
                            ],
                            [
                                'type' => 'text',
                                'content' => $msgChiTiet,
                                'align' => 'left',
                            ]
                        ],
                        'buttons' => [
                            [
                                'title' => 'Gọi nhân viên kĩ thuật',
                                'image_icon' => 'gNf2KPUOTG-ZSqLJaPTl6QTcKqIIXtaEfNP5Kv2NRncWPbDJpC4XIxie20pTYMq5gYv60DsQRHYn9XyVcuzu4_5o21NQbZbCxd087DcJFq7bTmeUq9qwGVie2ahEpZuLg2KDJfJ0Q12c85jAczqtKcSYVGJJ1cZMYtKR',
                                'type' => 'oa.open.phone',
                                'payload' => [
                                    'phone_code' => $this->obj->dt_kt
                                ]
                            ],
                            [
                                'title' => 'Gọi nhân viên kinh doanh',
                                'image_icon' => 'gNf2KPUOTG-ZSqLJaPTl6QTcKqIIXtaEfNP5Kv2NRncWPbDJpC4XIxie20pTYMq5gYv60DsQRHYn9XyVcuzu4_5o21NQbZbCxd087DcJFq7bTmeUq9qwGVie2ahEpZuLg2KDJfJ0Q12c85jAczqtKcSYVGJJ1cZMYtKR',
                                'type' => 'oa.open.phone',
                                'payload' => [
                                    'phone_code' => $this->obj->dt_kd
                                ]
                            ],
                            [
                                'title' => 'Gọi tổng đài',
                                'image_icon' => 'gNf2KPUOTG-ZSqLJaPTl6QTcKqIIXtaEfNP5Kv2NRncWPbDJpC4XIxie20pTYMq5gYv60DsQRHYn9XyVcuzu4_5o21NQbZbCxd087DcJFq7bTmeUq9qwGVie2ahEpZuLg2KDJfJ0Q12c85jAczqtKcSYVGJJ1cZMYtKR',
                                'type' => 'oa.open.phone',
                                'payload' => [
                                    'phone_code' => '18001166'
                                ]
                            ]
                        ]
                    ]
                ]
            ];

            $this->zaloAPI->SendMsgGiaoDich($userId, $message, $kichBanId);
            //Đánh dấu đã gửi tin
            DB::connection('oracle')->table('zalobgg.LOG_BAOHONG')->where('phieu_id', $this->obj->phieu_id)->update(['da_gui_tin' => 1]);

        } catch (\Exception $ex) {
            Log::channel('daily')->error('ERROR-KICHBAN-2: ' . $ex->getMessage());
        }
    }
}
