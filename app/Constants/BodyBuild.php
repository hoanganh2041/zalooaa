<?php


namespace App\Constants;


class BodyBuild
{
    public function SaiCuPhap()
    {
        return '💥💥Sai cú pháp. Quý khách vui lòng thử lại💥💥';
    }

    public function CamOnQuyKhach()
    {
        return '💥💥Cảm ơn quý khách đã sử dụng dịch vụ của VNPT Bắc Giang💥💥';
    }

    public function LayThongTinKH($userId)
    {
        $dataMess = [
            'title' => 'VNPT Bắc Giang',
            'subtitle' => 'Để có thể phục vụ quý khách hàng được tốt hơn, xin vui lòng cung cấp cho chúng tôi một số thông tin sau. Xin chân thành cảm ơn!',
            'image_url' => 'https://zaloapi.vnptbacgiang.com.vn/storage/zalo/request-user-info-banner1.jpg'
        ];
        $postInput = [
            'recipient' => [
                'user_id' => $userId
            ],
            'message' => [
                'attachment' => [
                    'type' => 'template',
                    'payload' => [
                        'template_type' => 'request_user_info',
                        'elements' => [$dataMess],
                    ]
                ]
            ]
        ];

        return $postInput;
    }

    public function SaiCuPhap1()
    {
        $message = [
            'attachment' => [
                'type' => 'template',
                'payload' => [
                    'template_type' => 'promotion',
                    'elements' => [
                        [
                            'type' => 'banner',
                            'image_url' => 'https://zaloapi.vnptbacgiang.com.vn/storage/zalo/logo-5gs.png'
                        ],
                        [
                            'type' => 'header',
                            'content' => '💥💥Sai cú pháp. Quý khách vui lòng thử lại💥💥'
                        ]
                    ],
                ]
            ]
        ];

        return $message;
    }
}
