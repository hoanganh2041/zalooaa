<?php

use App\Http\Controllers\DangKyCSKHController;
use App\Http\Controllers\DangKyLapDatController;
use App\Http\Controllers\DichVuController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/admin');
});


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();

    Route::group(['prefix' => 'dangkycskh'], function () {
        Route::post('/xuly', [DangKyCSKHController::class, 'xuly'])->name('dangkycskh.xuly');
    });

    Route::group(['prefix' => 'dangkylapdat'], function () {
        Route::post('/xuly', [DangKyLapDatController::class, 'xuly'])->name('dangkylapdat.xuly');
    });
});

Route::group(['prefix' => 'dich-vu'], function () {
    Route::get('/dang-ky-dich-vu', [DichVuController::class, 'dangkydichvu']);
    Route::post('/dang-ky-dich-vu', [DichVuController::class, 'dangkydichvusubmit'])->name('dangky.dichvu.submit');

    Route::get('/dang-ky-cskh', [DichVuController::class, 'cskh']);
    Route::post('/dang-ky-cskh', [DichVuController::class, 'cskhsubmit'])->name('dangky.cskh.submit');;
});
