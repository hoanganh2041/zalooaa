<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class DangKyLapDat extends Model
{
    protected $table = 'dangkylapdat';

    protected $fillable = [
        'dichvu_id',
        'huyen_id',
        'hoten',
        'diachi',
        'dienthoai',
        'trangthai'
    ];
}
