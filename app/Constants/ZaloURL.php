<?php


namespace App\Constants;


class ZaloURL
{
    const GET_ACCESS_TOKEN = 'https://oauth.zaloapp.com/v4/oa/access_token';
    const GET_ACCESS_TOKEN_CODE = 'access_token';

    const SEND_MESSAGE_TRUYENTHONG = 'https://openapi.zalo.me/v3.0/oa/message/promotion';
    const SEND_MESSAGE_TRUYENTHONG_CODE = 'truyenthong';

    const REQUEST_USER_INFO = 'https://openapi.zalo.me/v3.0/oa/message/cs';
    const REQUEST_USER_INFO_CODE = 'request_user_info';

    const SEND_MESSAGE_ZNS = 'https://business.openapi.zalo.me/message/template';
    const SEND_MESSAGE_ZNS_CODE = 'zns_template';

    const SEND_MESSAGE_TUVAN = 'https://openapi.zalo.me/v3.0/oa/message/cs';
    const SEND_MESSAGE_TUVAN_CODE = 'tuvan';

    const SEND_MESSAGE_GIAODICH = 'https://openapi.zalo.me/v3.0/oa/message/transaction';
    const SEND_MESSAGE_GIAODICH_CODE = 'giaodich';
}
