<?php


namespace App\Http\Controllers;

use App\Models\DangKyCSKH;
use App\Models\DangKyLapDat;
use App\Models\DichVu;
use App\Models\Quan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use TCG\Voyager\Facades\Voyager;

class DichVuController extends Controller
{
    public function dangkydichvu(Request $request)
    {
        $lsDichVU = DichVu::all();
        $lsHuyen = Quan::all();
        $is_submited = false;
        return Voyager::view('voyager::dich-vu.dangky', compact([
            'is_submited',
            'lsDichVU',
            'lsHuyen'
        ]));
    }

    public function dangkydichvusubmit(Request $request)
    {
        $is_submited = true;
        $lsDichVU = DichVu::all();
        $lsHuyen = Quan::all();
        $validator = Validator::make($request->all(), [
            'dienthoai' => 'required|regex:/[0-9]{10}/|digits:10',
            'hoten' => 'required',
            'huyen_id' => 'required|not_in:-1',
            'dichvu_id' => 'required|not_in:-1',
            'diachi' => 'required',
        ], [
            'dienthoai.required' => 'Vui lòng nhập Số điện thoại',
            'dienthoai.regex' => 'Số điện thoại không đúng',
            'dienthoai.digits' => 'Số điện thoại không đúng',
            'huyen_id.required' => 'Vui lòng chọn khu vực',
            'huyen_id.not_in' => 'Vui lòng chọn khu vực',
            'dichvu_id.required' => 'Vui lòng chọn dịch vụ',
            'dichvu_id.not_in' => 'Vui lòng chọn dịch vụ',
            'diachi.required' => 'Vui lòng nhập Địa chỉ',
            'hoten.required' => 'Vui lòng nhập Họ tên',
        ]);

        if (!$validator->passes()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $request->merge([
            'trangthai' => 1
        ]);

        DangKyLapDat::create($request->except('_token'));
        return Voyager::view('voyager::dich-vu.dangky', compact([
            'is_submited',
            'lsDichVU',
            'lsHuyen'
        ]));
    }

    public function cskh(Request $request)
    {
        $is_submited = false;
        $lsDichVU = DichVu::all();
        $lsHuyen = Quan::all();
        return Voyager::view('voyager::dich-vu.cskh', compact([
            'is_submited',
            'lsDichVU',
            'lsHuyen'
        ]));
    }

    public function cskhsubmit(Request $request)
    {
        $is_submited = true;
        $lsDichVU = DichVu::all();
        $lsHuyen = Quan::all();
        $validator = Validator::make($request->all(), [
            'dienthoai' => 'required|regex:/[0-9]{10}/|digits:10',
            'hoten' => 'required',
            'huyen_id' => 'required|not_in:-1',
            'dichvu_id' => 'required|not_in:-1',
            'diachi' => 'required',
            'hientrang' => 'required',
        ], [
            'dienthoai.required' => 'Vui lòng nhập Số điện thoại',
            'dienthoai.regex' => 'Số điện thoại không đúng',
            'dienthoai.digits' => 'Số điện thoại không đúng',
            'huyen_id.required' => 'Vui lòng chọn khu vực',
            'huyen_id.not_in' => 'Vui lòng chọn khu vực',
            'dichvu_id.required' => 'Vui lòng chọn dịch vụ',
            'dichvu_id.not_in' => 'Vui lòng chọn dịch vụ',
            'diachi.required' => 'Vui lòng nhập Địa chỉ',
            'hoten.required' => 'Vui lòng nhập Họ tên',
            'hientrang.required' => 'Vui lòng nhập Hiện trạng dịch vụ',
        ]);

        if (!$validator->passes()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $request->merge([
            'trangthai' => 1
        ]);

        DangKyCSKH::create($request->except('_token'));

        return Voyager::view('voyager::dich-vu.cskh', compact([
            'is_submited',
            'lsDichVU',
            'lsHuyen'
        ]));
    }
}
