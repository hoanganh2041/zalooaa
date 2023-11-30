<?php


namespace App\Helpers;


use App\Constants\ZaloURL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class KhachHangHelper
{
    protected $zaloAPI;

    public function __construct(
        ZaloAPIHelper $zaloAPI
    )
    {
        $this->zaloAPI = $zaloAPI;
    }

    public function KhachHangFollow($userId)
    {
        DB::connection('oracle')->executeFunction('ZALOBGG.pkg_danhmuc.QuanTam_Zalo', ['vIdZalo' => $userId]);
        $this->zaloAPI->SendMsgRequestUserInfo($userId, null);
    }

    public function KhachHangUnFollow($userId)
    {
        DB::connection('oracle')->executeFunction('ZALOBGG.pkg_danhmuc.Bo_QuanTam_Zalo', ['vIdZalo' => $userId]);
    }

    public function CapNhatTTKH($request)
    {
        $userId = json_decode(json_encode($request->sender, JSON_FORCE_OBJECT))->id;
        $address = json_decode(json_encode($request->info, JSON_FORCE_OBJECT))->address;
        $phone = json_decode(json_encode($request->info, JSON_FORCE_OBJECT))->phone;
        //$city = json_decode(json_encode($request->info, JSON_FORCE_OBJECT))->city;
        //$district = json_decode(json_encode($request->info, JSON_FORCE_OBJECT))->district;
       //$ward = json_decode(json_encode($request->info, JSON_FORCE_OBJECT))->ward;
        $name = json_decode(json_encode($request->info, JSON_FORCE_OBJECT))->name;
        DB::connection('oracle')->executeFunction('ZALOBGG.pkg_danhmuc.CapNhat_ThongTin', ['vIdZalo' => $userId, 'vHoTen' => $name, 'vSoDT' => $phone, 'vDiaChi' => $address]);
    }

    public function DangKyMaKH($userId, $arrText)
    {
        $sdt = $arrText[1];
        $matt = $arrText[2];
        DB::connection('oracle')->executeFunction('ZALOBGG.pkg_danhmuc.DangKy_MaTT', ['vIdZalo' => $userId, 'vMaTT' => $matt, 'vSoDT' => $sdt]);
        $message = sprintf("Quý khách đã đăng ký thành công mã thanh toán %s và số điện thoại %s", $matt, $sdt);
        $this->zaloAPI->SendMsgTuVan($userId, $message, null, null, null);
    }

    /*Sau khi ký hợp đồng nhân viên phát triển hợp đồng tư vấn mời khách hàng quan tâm OA Zalo  để nhận thông tin tương tác về tiến trình lắp đặt sau khi ký hợp đồng thành công*/
    public function CSKH_SauKhiKyHD($userId)
    {
        $message = sprintf("Cảm ơn Quý khách đã tin tưởng và lựa chọn sử dụng dịch vụ của VNPT Bắc Giang cung cấp. Ngày 07/05/2023, Quý khách có đăng kí mới dịch vụ: FiberVNN. tại địa chỉ lắp đặt: 34 Nguyễn Thị Lưu, P.Trần Phú, TP.Bắc Giang. Dịch vụ của Quý khách sẽ được nhân viên kỹ thuật VNPT Bắc Giang thi công trong khoảng thời gian từ 08h30 ngày 08/05 đến 11h00 ngày 08/05/2023. Quý khách có thể liên hệ với nhân viên kỹ thuật xử lý sự cố Nguyễn Ngọc Ân, 0916198226; Nhân viên kinh doanh Nguyễn Xuân Trường, 0917888999; Hotline: 18001166");
        $this->zaloAPI->SendMsgTuVan($userId, $message, null, null, null);
    }
    /*Sau khi khách hàng báo hỏng có phiếu trong hệ thống ĐHSXKD, hệ thống OA tự động gửi thông tin tương tác với khách hàng.*/
    public function CSKH_KhiBaoHong($userId)
    {
        $message = sprintf("Cảm ơn Quý khách đã luôn tin tưởng và sử dụng dịch vụ của VNPT Bắc Giang. Ngày 07/05/2023, Quý khách có báo hỏng dịch vụ: FiberVNN. tại địa chỉ lắp đặt: 34 Nguyễn Thị Lưu, P.Trần Phú, TP.Bắc Giang. Dịch vụ của quý khách sẽ được xử lý trong thời gian từ 08h30 ngày 08/05 đến 11h00 ngày 08/05/2023. Xin lỗi quý khách về sự bất tiện này. Quý khách có thể liên hệ với nhân viên kỹ thuật xử lý sự cố Nguyễn Ngọc Ân, 0916198226; Nhân viên kinh doanh Nguyễn Xuân Trường, 0917888999; Hotline: 18001166");
        $this->zaloAPI->SendMsgTuVan($userId, $message, null, null, null);
    }

    public function Noti_khiCoCuoc($userId)
    {
        $message = sprintf("Cảm ơn Quý khách đã luôn tin tưởng và sử dụng dịch vụ của VNPT Bắc Giang. Ngày 07/05/2023, Quý khách có báo hỏng dịch vụ: FiberVNN. tại địa chỉ lắp đặt: 34 Nguyễn Thị Lưu, P.Trần Phú, TP.Bắc Giang. Dịch vụ của quý khách sẽ được xử lý trong thời gian từ 08h30 ngày 08/05 đến 11h00 ngày 08/05/2023. Xin lỗi quý khách về sự bất tiện này. Quý khách có thể liên hệ với nhân viên kỹ thuật xử lý sự cố Nguyễn Ngọc Ân, 0916198226; Nhân viên kinh doanh Nguyễn Xuân Trường, 0917888999; Hotline: 18001166");
        $this->zaloAPI->SendMsgTuVan($userId, $message, null, null, null);
    }
    /*Nhắn tin thông báo tới khách hàng sau 10 ngày khách hàng nhận được tin nhắn thông báo cước mà chưa thanh toán (tránh ngày 01 âm lịch, ngày lễ,….)*/
    public function Noti_ChuaThanhToanCuoc($userId)
    {
        $message = sprintf("Cảm ơn Quý khách đã luôn tin tưởng và sử dụng dịch vụ của VNPT Bắc Giang. Ngày 07/05/2023, Quý khách có báo hỏng dịch vụ: FiberVNN. tại địa chỉ lắp đặt: 34 Nguyễn Thị Lưu, P.Trần Phú, TP.Bắc Giang. Dịch vụ của quý khách sẽ được xử lý trong thời gian từ 08h30 ngày 08/05 đến 11h00 ngày 08/05/2023. Xin lỗi quý khách về sự bất tiện này. Quý khách có thể liên hệ với nhân viên kỹ thuật xử lý sự cố Nguyễn Ngọc Ân, 0916198226; Nhân viên kinh doanh Nguyễn Xuân Trường, 0917888999; Hotline: 18001166");
        $this->zaloAPI->SendMsgTuVan($userId, $message, null, null, null);
    }
}
