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

class KichBan12Job implements ShouldQueue
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
            //$userId = '6929160182354365063';
            $kichBanId = 12;
            $msgChiTiet = '• Quý khách có thể gia hạn bằng cách liên hệ nhân viên kinh doanh %s, %s hoặc VNPT %s, %s <br>';
            $msgChiTiet .= '• Hoặc có thể gia hạn qua App My VNPT<br>';
            $msgChiTiet .= '• Cảm ơn Quý khách đã tin dùng dịch vụ của VNPT';
            $msgChiTiet = sprintf($msgChiTiet, $this->obj->ten_nvkd, $this->obj->dt_nvkd, $this->obj->ten_diaban, $this->obj->dt_giaodich);

            $message = [
                'attachment' => [
                    'type' => 'template',
                    'payload' => [
                        'template_type' => 'transaction_event',
                        'language' => 'VI',
                        'elements' => [
                            [
                                'image_url' => 'https://zaloapi.vnptbacgiang.com.vn/storage/zalo/dongcuoctruoc.jpg',
                                'type' => 'banner'
                            ],
                            [
                                'type' => 'header',
                                'content' => 'GÓI CƯỚC THANH TOÁN TRẢ TRƯỚC',
                                'align' => 'center',
                            ],
                            [
                                'type' => 'text',
                                'content' => '• VNPT Bắc Giang trân trọng thông báo gói cước thanh toán trước của Quý khách:',
                                'align' => 'left',
                            ],
                            [
                                'type' => 'table',
                                'content' => [
                                    [
                                        'key' => 'Tên khách hàng',
                                        'value' => $this->obj->ten_tt,
                                    ],
                                    [
                                        'key' => 'Địa chỉ',
                                        'value' => $this->obj->diachi_ct,
                                    ],
                                    [
                                        'key' => 'Mã thanh toán',
                                        'value' => $this->obj->ma_tt,
                                    ],
                                    [
                                        'key' => 'Thời hạn gói cước trả trước',
                                        'value' => 'Tháng ' . Carbon::createFromFormat('Ym', $this->obj->thang_kt)->format('m/Y'),
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
                                'title' => 'Gọi nhân viên kinh doanh',
                                'image_icon' => 'gNf2KPUOTG-ZSqLJaPTl6QTcKqIIXtaEfNP5Kv2NRncWPbDJpC4XIxie20pTYMq5gYv60DsQRHYn9XyVcuzu4_5o21NQbZbCxd087DcJFq7bTmeUq9qwGVie2ahEpZuLg2KDJfJ0Q12c85jAczqtKcSYVGJJ1cZMYtKR',
                                'type' => 'oa.open.phone',
                                'payload' => [
                                    'phone_code' => $this->obj->dt_nvkd
                                ]
                            ],
                            [
                                'title' => 'Gọi VNPT ' . $this->obj->ten_diaban,
                                'image_icon' => 'gNf2KPUOTG-ZSqLJaPTl6QTcKqIIXtaEfNP5Kv2NRncWPbDJpC4XIxie20pTYMq5gYv60DsQRHYn9XyVcuzu4_5o21NQbZbCxd087DcJFq7bTmeUq9qwGVie2ahEpZuLg2KDJfJ0Q12c85jAczqtKcSYVGJJ1cZMYtKR',
                                'type' => 'oa.open.phone',
                                'payload' => [
                                    'phone_code' => $this->obj->dt_giaodich
                                ]
                            ],
                            [
                                'title' => 'Tải App My VNPT',
                                'image_icon' => '',
                                'type' => 'oa.open.url',
                                'payload' => [
                                    'url' => 'https://my.vnpt.com.vn/app'
                                ]
                            ]
                        ]
                    ]
                ]
            ];
            $this->zaloAPI->SendMsgGiaoDich($userId, $message, $kichBanId);
            //Đánh dấu đã gửi tin
            DB::connection('oracle')->table('zalobgg.LOG_HETHAN_CUOCTRUOC')
                ->where('ma_tb', $this->obj->ma_tb)
                ->where('thang_gui_tin', $this->obj->thang_gui_tin)
                ->update(['da_gui_tin' => 1]);

        } catch (\Exception $ex) {
            Log::channel('daily')->error('ERROR-KICHBAN-12: ' . $ex->getMessage());
        }
    }
}
