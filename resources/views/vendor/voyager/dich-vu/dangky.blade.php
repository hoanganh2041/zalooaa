<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Đăng ký dịch vụ - VNPT Bắc Giang</title>

    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.8/css/all.css">

</head>
<body class="antialiased">

<div class="container">
    <br>
    @if($is_submited)
        <div class="alert alert-success">
            <strong>Đăng ký thành công!</strong> Cảm ơn quý khách đã cung cấp thông tin. Bộ phận CSKH sẽ liên hệ với quý
            khách trong thời gian sớm nhất
        </div>
    @else
        <div class="card bg-light">
            <article class="card-body mx-auto" style="max-width: 400px;">
                <h4 class="card-title mt-3 text-center">Đăng ký dịch vụ</h4>
                <p class="text-center">Quý khách vui lòng nhập thông tin nhu cầu lắp đặt</p>
                <p class="divider-text">
                    <span class="bg-light">***</span>
                </p>

                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('dangky.dichvu.submit') }}" method="POST">
                    {{ csrf_field() }}
                    <div class="form-group input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"> <i class="fa fa-list"></i> <div
                                    class="red">&nbsp(*)</div></span>
                        </div>
                        <select class="form-control" name="dichvu_id">
                            <option value="-1"  @if (-1 == old('dichvu_id'))) selected="selected" @endif > Chọn dịch vụ</option>
                            @foreach ($lsDichVU as $obj)
                                <option value="{{ $obj->id }}"
                                    {{ ($obj->id == old('dichvu_id') ? 'selected' : '') }}>{{ $obj->ten }}</option>
                            @endforeach
                        </select>
                    </div> <!-- form-group end.// -->
                    <div class="form-group input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"> <i class="fa fa-book"></i> <div
                                    class="red">&nbsp(*)</div></span>
                        </div>
                        <select class="form-control" name="huyen_id">
                            <option value="-1" @if (-1 == old('huyen_id'))) selected="selected" @endif> Chọn khu vực</option>

                            @foreach ($lsHuyen as $obj)
                                <option value="{{ $obj->id }}"
                                    {{ ($obj->id == old('huyen_id') ? 'selected' : '') }}>{{ $obj->ten }}</option>
                            @endforeach
                        </select>
                    </div> <!-- form-group end.// -->
                    <div class="form-group input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"> <i class="fa fa-user"></i> <div
                                    class="red">&nbsp(*)</div></span>
                        </div>
                        <input name="hoten" class="form-control" placeholder="Họ tên" type="text"
                               value="{{ old('hoten') }}">
                    </div> <!-- form-group// -->
                    <div class="form-group input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"> <i class="fa fa-phone"></i> <div class="red">&nbsp(*)</div></span>
                        </div>
                        <input name="dienthoai" class="form-control" placeholder="Số điện thoại" type="text"
                               value="{{ old('dienthoai') }}">
                    </div> <!-- form-group// -->

                    <div class="form-group input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"> <i class="fa fa-address-book"></i> <div
                                    class="red">&nbsp(*)</div></span>
                        </div>
                        <input name="diachi" class="form-control" placeholder="Địa chỉ" type="text"
                               value="{{ old('diachi') }}">
                    </div> <!-- form-group// -->

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block"> Gửi đăng ký dịch vụ</button>
                    </div> <!-- form-group// -->
                    <p class="text-center">Quý khách cần hỗ trợ ngay bây giờ? Vui lòng gọi <a href="tel:18001166">18001166</a>
                    </p>
                </form>
            </article>

        </div> <!-- card.// -->
    @endif

</div>
<!--container end.//-->
<style>
    .divider-text {
        position: relative;
        text-align: center;
        margin-top: 15px;
        margin-bottom: 15px;
    }

    .divider-text span {
        padding: 7px;
        font-size: 12px;
        position: relative;
        z-index: 2;
    }

    .divider-text:after {
        content: "";
        position: absolute;
        width: 100%;
        border-bottom: 1px solid #ddd;
        top: 55%;
        left: 0;
        z-index: 1;
    }

    .btn-facebook {
        background-color: #405D9D;
        color: #fff;
    }

    .btn-twitter {
        background-color: #42AEEC;
        color: #fff;
    }

    .red {
        color: red;
    }

</style>
</body>
</html>
