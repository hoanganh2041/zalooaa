<?php


namespace App\Helpers;


use App\Constants\BodyBuild;
use App\Constants\ZaloURL;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ZaloAPIHelper
{
    protected $bodyBuild;

    public function __construct(BodyBuild $bodyBuild)
    {
        $this->bodyBuild = $bodyBuild;
    }

    public function SendZnsTemplate($phoneNumber, $templateId, $templateData, $kichBanId, $trackingId = 'tracking_id')
    {
        try {
            $apiURL = ZaloURL::SEND_MESSAGE_ZNS;
            $postInput = [
                'phone' => $phoneNumber,
                'template_id' => $templateId,
                'template_data' => $templateData,
                'tracking_id' => $trackingId
            ];

            $headers = $this->BuildHeader();

            $response = Http::withHeaders($headers)->timeout(5)->post($apiURL, $postInput);
            $data = json_decode($response->body());

            $this->LogAPI(ZaloURL::SEND_MESSAGE_ZNS_CODE, null, json_encode($postInput, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES), $response->body(), $templateId, $phoneNumber, $data->error, $data->message, $kichBanId);

            return $data->error;
        }catch (\Exception $ex){
            $this->LogAPI(ZaloURL::SEND_MESSAGE_ZNS_CODE, null, json_encode($postInput, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES), null, $templateId, $phoneNumber, 99, $ex->getMessage(), $kichBanId);
            return 99;
        }

    }

    public function SendMsgTruyenThong($userId, $message, $templateId, $phone, $kichBanId)
    {
        $apiURL = ZaloURL::SEND_MESSAGE_TRUYENTHONG;
        $postInput = [
            'recipient' => [
                'user_id' => $userId
            ],
            'message' => $message
        ];

        $headers = $this->BuildHeader();

        $response = Http::withHeaders($headers)->timeout(5)->post($apiURL, $postInput);
        $data = json_decode($response->body());

        $this->LogAPI(ZaloURL::SEND_MESSAGE_TRUYENTHONG_CODE, $userId, json_encode($postInput, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES), $response->body(), $templateId, $phone, $data->error, $data->message, $kichBanId);

        //$this->LogGuiTin($userId, self::ID_MAU_1, json_encode($message), null, null);

        return $data->error;
    }

    public function SendMsgGiaoDich($userId, $message, $kichBanId)
    {
        try{
            $apiURL = ZaloURL::SEND_MESSAGE_GIAODICH;
            $postInput = [
                'recipient' => [
                    'user_id' => $userId
                ],
                'message' => $message
            ];

            $headers = $this->BuildHeader();

            $response = Http::withHeaders($headers)->timeout(5)->post($apiURL, $postInput);
            $data = json_decode($response->body());

            $this->LogAPI(ZaloURL::SEND_MESSAGE_GIAODICH_CODE, $userId, json_encode($postInput, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES), $response->body(), null, null, $data->error, $data->message, $kichBanId);

            return $data->error;
        }catch (\Exception $ex){
            $this->LogAPI(ZaloURL::SEND_MESSAGE_GIAODICH_CODE, $userId, json_encode($postInput, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES), null, null, null, 99, $ex->getMessage(), $kichBanId);
            return 99;
        }

    }

    public function SendMsgTuVan($userId, $message, $templateId, $phone, $kichBanId)
    {
        try{
            $apiURL = ZaloURL::SEND_MESSAGE_TUVAN;
            $postInput = [
                'recipient' => [
                    'user_id' => $userId
                ],
                'message' => [
                    'text' => $message
                ]
            ];

            $headers = $this->BuildHeader();

            $response = Http::withHeaders($headers)->timeout(5)->post($apiURL, $postInput);
            $data = json_decode($response->body());

            $this->LogAPI(ZaloURL::SEND_MESSAGE_TUVAN_CODE, $userId, json_encode($postInput, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES), $response->body(), $templateId, $phone, $data->error, $data->message, $kichBanId);

            return $data->error;
        }catch (\Exception $ex){
            $this->LogAPI(ZaloURL::SEND_MESSAGE_TUVAN_CODE, $userId, json_encode($postInput, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES), null, $templateId, $phone, 99, $ex->getMessage(), $kichBanId);
            return 99;
        }

    }

    public function SendMsgRequestUserInfo($userId, $kichBanId)
    {
        try {
            $apiURL = ZaloURL::REQUEST_USER_INFO;

            $postInput = $this->bodyBuild->LayThongTinKH($userId);
            $headers = $this->BuildHeader();

            $response = Http::withHeaders($headers)->timeout(5)->post($apiURL, $postInput);
            $data = json_decode($response->body());

            $this->LogAPI(ZaloURL::REQUEST_USER_INFO_CODE, $userId, json_encode($postInput, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES), $response->body(), null, null, $data->error, $data->message, $kichBanId);

            return $data->error;
        }catch (\Exception $ex){
            $this->LogAPI(ZaloURL::REQUEST_USER_INFO_CODE, $userId, json_encode($postInput, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES), null, null, null, 99, $ex->getMessage(), $kichBanId);
            return 99;
        }

    }

    public function BuildHeader()
    {
        $zaloToken = DB::table('zalo_token')->latest('id')->first();
        return [
            'Content-Type' => 'application/json',
            'access_token' => $zaloToken->access_token
        ];;
    }

    public function LogAPI($type, $userId, $body, $response, $templateId, $phone, $rescode, $resmessage, $kichBanId)
    {
        DB::connection('oracle')->table('zalobgg.API_HISTORY')->insert(
            array(
                'TYPE' => $type,
                'USER_ID' => $userId,
                'BODY' => $body,
                'RESPONSE' => $response,
                'TEMPLATE_ID' => $templateId,
                'PHONE' => $phone,
                'RES_CODE' => $rescode,
                'RES_MESSAGE' => $resmessage,
                'CREATED_DATE' => Carbon::now(),
                'KICHBAN_ID' => $kichBanId
            )
        );
    }
}
