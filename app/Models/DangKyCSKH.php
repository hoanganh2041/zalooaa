<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class DangKyCSKH extends Model
{
    protected $table = 'dangkycskh';
    protected $fillable = [
        'dichvu_id',
        'huyen_id',
        'hoten',
        'diachi',
        'dienthoai',
        'hientrang',
        'trangthai'
    ];
}
