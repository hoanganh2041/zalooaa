<?php


namespace App\Http\Controllers;

use App\Constants\BodyBuild;
use App\Constants\ZaloURL;
use App\Helpers\KhachHangHelper;
use App\Helpers\KhaoSatHelper;
use App\Helpers\TraCuuHelper;
use App\Helpers\ZaloAPIHelper;
use App\Models\LogAPI;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Constants\ResCode;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    protected $bodyBuild, $zaloAPI, $traCuu, $khaoSat, $khachHang;

    private const ID_MAU_1 = 1;

    public function __construct(
        BodyBuild $bodyBuild,
        ZaloAPIHelper $zaloAPI,
        TraCuuHelper $traCuu,
        KhaoSatHelper $khaoSat,
        KhachHangHelper $khachHang
    )
    {
        $this->bodyBuild = $bodyBuild;
        $this->zaloAPI = $zaloAPI;
        $this->traCuu = $traCuu;
        $this->khaoSat = $khaoSat;
        $this->khachHang = $khachHang;
    }

    public function Webhook(Request $request)
    {
        Log::channel('daily')->info("Webhook Captured", $request->all());
        $message = ResCode::SUCCESS_MSG;
        try{
            switch ($request->event_name) {
                case 'follow':
                    $userId = json_decode(json_encode($request->follower, JSON_FORCE_OBJECT))->id;
                    $this->khachHang->KhachHangFollow($userId);
                    break;
                case 'unfollow':
                    $userId = json_decode(json_encode($request->follower, JSON_FORCE_OBJECT))->id;
                    $this->khachHang->KhachHangUnFollow($userId);
                    //$this->zaloAPI->SendMsgTuVan($userId, $this->bodyBuild->CamOnQuyKhach(), null, null, null);
                    break;
                case 'user_send_text':
                    $userId = json_decode(json_encode($request->sender, JSON_FORCE_OBJECT))->id;
                    $text = json_decode(json_encode($request->message, JSON_FORCE_OBJECT))->text;
                    $text = preg_replace('/\s+/', ' ', strtolower(trim($text)));

                    $arrText = explode(' ', $text);
                    $command = $arrText[0];

                    switch ($command) {
                        case "#dk":
                            $this->khachHang->DangKyMaKH($userId, $arrText);
                            break;
                        case "#tracuucuoc":
                            $this->traCuu->TraCuuCuoc($userId);
                            break;
                        case "#tracuuno":
                            $this->traCuu->TraCuuNo($userId);
                            break;
                        case "#khaosat":
                            $this->KhaoSatDoHaiLong($userId);
                            break;
                        case "#khaosathailong":
                            $this->KhaoSat_HaiLong($userId);
                            break;
                        case "#khaosatkhonghailong":
                            $this->KhaoSat_KhongHaiLong($userId);
                            break;
                        case "#khaosatkhonghailong_chatluong":
                            //$message = "Cảm ơn Quý Khách đã phản hồi về chất lượng dịch vụ!\nBộ phận CSKH sẽ liên hệ Quý Khách trong thời gian sớm nhất!";
                            // $this->SendMessageToKH($userId, $message);
                            // $this->LogPhanHoi($userId, self::ID_MAU_1, 3);
                            break;
                        case "#khaosatkhonghailong_thaido":
                            //$message = "Cảm ơn Quý Khách đã phản hồi về thái độ phục vụ của nhân viên!\nBộ phận CSKH sẽ liên hệ Quý Khách trong thời gian sớm nhất!";
                            //$this->SendMessageToKH($userId, $message);
                            //$this->LogPhanHoi($userId, self::ID_MAU_1, 4);
                            break;
                        case "#cskh_saukyhd":
                            $this->khachHang->CSKH_SauKhiKyHD($userId);
                            break;
                        case "#cskh_khibaohon":
                            $this->khachHang->CSKH_KhiBaoHong($userId);
                            break;
                        default: //TH Sai cú pháp
                            //$this->zaloAPI->SendMsgTuVan($userId, $this->bodyBuild->SaiCuPhap(), null, null, null);
                    }
                    break;
                case 'user_submit_info':
                    $this->khachHang->CapNhatTTKH($request);
                    break;
                default:
            }
        }catch (\Exception $e){
            $exceptionArray = [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];
            Log::channel('daily')->error("Webhook Captured Error: ", $exceptionArray);
            Log::channel('daily')->error("Webhook Captured Error: ", $request->all());
        }

        return response()->json([
            'error_code' => ResCode::SUCCESS,
            'message' => $message
        ], Response::HTTP_OK);
    }





    public function KhaoSatDoHaiLong($userId)
    {
        $message = [
            'attachment' => [
                'type' => 'template',
                'payload' => [
                    'template_type' => 'list',
                    'elements' => [
                        [
                            'title' => 'Khảo sát độ hài lòng khách hàng khi sử dụng dịch vụ',
                            'subtitle' => 'VNPT Bắc Giang trân trọng cảm ơn Quý Khách hàng đã luôn tin tưởng và ủng hộ trong suốt thời gian qua. Để không ngừng nâng cao hơn nữa chất lượng dịch vụ, chúng tôi rất mong nhận được những góp ý, phản hồi của Quý Khách trong quá trình sử dụng dịch vụ.',
                            'image_url' => 'https://zaloapi.vnptbacgiang.com.vn/storage/zalo/Screenshot_1-removebg-preview.png'
                        ],
                        [
                            'title' => 'Hài Lòng!',
                            'image_url' => 'https://zaloapi.vnptbacgiang.com.vn/storage/zalo/like.png',
                            'default_action' => [
                                'type' => 'oa.query.hide',
                                'payload' => '#khaosathailong'
                            ]
                        ],
                        [
                            'title' => 'Không Hài Lòng!',
                            'image_url' => 'https://zaloapi.vnptbacgiang.com.vn/storage/zalo/cry.png',
                            'default_action' => [
                                'type' => 'oa.query.hide',
                                'payload' => '#khaosatkhonghailong'
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $this->CallAPISendMessageWithList($userId, $message);
    }

    public function KhaoSat_KhongHaiLong($userId)
    {
        $message = [
            'attachment' => [
                'type' => 'template',
                'payload' => [
                    'template_type' => 'list',
                    'elements' => [
                        [
                            'title' => 'Cảm ơn Quý Khách đã tham gia khảo sát',
                            'subtitle' => 'Vui lòng cho biết nguyên nhân Quý Khách không hài lòng',
                            'image_url' => 'https://zaloapi.vnptbacgiang.com.vn/storage/zalo/ketquakhaosat2.png'
                        ],
                        [
                            'title' => '1. Chất lượng dịch vụ',
                            'image_url' => 'https://zaloapi.vnptbacgiang.com.vn/storage/zalo/quality.png',
                            'default_action' => [
                                'type' => 'oa.query.hide',
                                'payload' => '#khaosatkhonghailong_chatluong'
                            ]
                        ],
                        [
                            'title' => '2. Thái độ phục vụ',
                            'image_url' => 'https://zaloapi.vnptbacgiang.com.vn/storage/zalo/service.png',
                            'default_action' => [
                                'type' => 'oa.query.hide',
                                'payload' => '#khaosatkhonghailong_thaido'
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $this->CallAPISendMessageWithList($userId, $message);
        $this->LogPhanHoi($userId, self::ID_MAU_1, 2);
    }

    public function KhaoSat_HaiLong($userId)
    {
        $message = [
            'attachment' => [
                'type' => 'template',
                'payload' => [
                    'template_type' => 'list',
                    'elements' => [
                        [
                            'title' => 'Khảo sát độ hài lòng khách hàng khi sử dụng dịch vụ',
                            'subtitle' => 'Cảm ơn Quý Khách đã tham gia khảo sát, khi cần hỗ trợ Quý Khách vui lòng liên hệ VNPT',
                            'image_url' => 'https://zaloapi.vnptbacgiang.com.vn/storage/zalo/ketquakhaosat2.png'
                        ],
                        [
                            'title' => '1. Báo hỏng dịch vụ qua Website',
                            'image_url' => 'https://zaloapi.vnptbacgiang.com.vn/storage/zalo/www.png',
                            'default_action' => [
                                'type' => 'oa.open.url',
                                'url' => 'https://vnptbacgiang.com.vn/'
                            ]
                        ],
                        [
                            'title' => '2. Hotline 18001166 (miễn phí)',
                            'image_url' => 'https://zaloapi.vnptbacgiang.com.vn/storage/zalo/callcenter.png',
                            'default_action' => [
                                'type' => 'oa.open.phone',
                                'payload' => [
                                    'phone_code' => '18001166'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $this->CallAPISendMessageWithList($userId, $message);
        $this->LogPhanHoi($userId, self::ID_MAU_1, 1);
    }


    /*public
    function GetAccessToken()
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

        DB::table('zalo_token')->insert([
            'access_token' => $access_token,
            'refresh_token' => $refresh_token,
            'created_at' => Carbon::now()
        ]);
    }*/

    public function LogGuiTin($IdZalo, $IdMau, $NoiDung, $MaTB, $MaTT)
    {
        DB::connection('oracle')->table('zalobgg.log_guitin')->insert(
            array(
                'id_zalo' => $IdZalo,
                'id_mau' => $IdMau,
                'noi_dung' => $NoiDung,
                'trang_thai' => 0,
                'ma_tb' => $MaTB,
                'ma_tt' => $MaTT,
                'create_date' => Carbon::now()
            )
        );
    }

    public function LogPhanHoi($IdZalo, $IdMau, $IdLuaChon)
    {
        DB::connection('oracle')->table('zalobgg.log_phanhoi')->insert(
            array(
                'id_zalo' => $IdZalo,
                'id_mau' => $IdMau,
                'id_luachon' => $IdLuaChon,
                'create_date' => Carbon::now()
            )
        );
    }

    public
    function CallAPISendMessageWithList($userId, $message)
    {
        $zaloToken = DB::table('zalo_token')->latest('id')->first();
        $access_token = $zaloToken->access_token;

        $apiURL = ZaloURL::SEND_MESSAGE;
        $postInput = [
            'recipient' => [
                'user_id' => $userId
            ],
            'message' => $message
        ];
        $headers = [
            'Content-Type' => 'application/json',
            'access_token' => $access_token
        ];
        $response = Http::withHeaders($headers)->post($apiURL, $postInput);
        $data = json_decode($response->body());

        return $data->error;
    }

    /*public
    function LayDsQuan(Request $request)
    {
        $cacheKey = 'dsquan';
        if (Cache::has($cacheKey)) {
            $value = Cache::get($cacheKey);
        } else {
            $procedureName = 'ZALOBGG.pkg_danhmuc.DanhSachQuan';
            $bindings = [
                ['name' => 'vOut', 'type' => PDO::PARAM_STMT],
            ];
            $value = $this->executeProcedureWithCursor($procedureName, $bindings);
            Cache::put($cacheKey, $value, AppCons::CACHE_EXPIRED_SECOND);
        }
        return response()->json([
            'error_code' => ResCode::SUCCESS,
            'data' => $value["vOut"]
        ]);
    }

    public
    function LayDsPhuong(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'idquan' => ['required', 'integer']
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error_code' => ResCode::VALIDATE_FAIL,
                'message' => $validator->errors()
            ], 200);
        }

        $cacheKey = 'dsphuong-' . $request->idquan;
        if (Cache::has($cacheKey)) {
            $value = Cache::get($cacheKey);
        } else {
            $procedureName = 'ZALOBGG.pkg_danhmuc.DanhSachPhuong';
            $bindings = [
                ['name' => 'vIdQuan', 'value' => $request->idquan, 'type' => PDO::PARAM_INT],
                ['name' => 'vOut', 'type' => PDO::PARAM_STMT],
            ];
            $value = $this->executeProcedureWithCursor($procedureName, $bindings);
            Cache::put($cacheKey, $value, AppCons::CACHE_EXPIRED_SECOND);
        }
        return response()->json([
            'error_code' => ResCode::SUCCESS,
            'data' => $value["vOut"]
        ]);
    }*/


}
