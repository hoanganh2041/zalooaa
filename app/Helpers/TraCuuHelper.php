<?php


namespace App\Helpers;


use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use PDO;

class TraCuuHelper
{
    protected $zaloAPI;

    public function __construct(ZaloAPIHelper $zaloAPI)
    {
        $this->zaloAPI = $zaloAPI;
    }

    public function TraCuuNo($userId)
    {
        $procedureName = 'ZALOBGG.pkg_danhmuc.TraCuu_No';
        $bindings = [
            ['name' => 'vIdZalo', 'value' => $userId, 'type' => PDO::PARAM_STR],
            ['name' => 'vOut', 'type' => PDO::PARAM_STMT],
        ];
        $message = '';
        $tongtien = 0;
        $isSuccess = 1;
        $values = $this->executeProcedureWithCursor($procedureName, $bindings);
        foreach ($values as $items) {
            foreach ($items as $item) {
                $isSuccess = $item['CODE'];
                if ($item['CODE'] == 0) {
                    $MA_TT = $item['MA_TT'];
                    $chukyno = $item['CHUKYNO'];
                    $tien = $item['TIEN'];
                    $ten_tt = $item['TEN_TT'];
                    $DIACHI_TT = $item['DIACHI_TT'];

                    $message = $message . sprintf("- Mã thanh toán %s. \n    + Số tiền %s VNĐ. \n    + Tên thanh toán %s. \n    + Địa chỉ thanh toán %s \n", $MA_TT, number_format($tien, 0, '.', ','), $ten_tt, $DIACHI_TT);
                    $tongtien = $tongtien + $tien;
                } else {
                    $message = $item['MESSAGE'] . "\n";
                }
            }
        }

        if ($isSuccess == 0) {
            $message = sprintf("* Tổng số tiền nợ cước của quý khách là: %s VNĐ. \n%s", number_format($tongtien, 0, '.', ','), $message);
        }else if ($message == ''){
            $message = "Quý khách không còn nợ cước VT-CNTT. \n";
        }

        $message = $message . '⏰ Ngày tra cứu ' . Carbon::now()->format('d/m/Y') . "\n";
        $message = $message . 'VNPT Bắc Giang trân trọng thông báo!';

        $this->zaloAPI->SendMsgTuVan($userId, $message, null, null, null);
    }

    public function TraCuuCuoc($userId)
    {
        $procedureName = 'ZALOBGG.pkg_danhmuc.TraCuu_Cuoc';
        $bindings = [
            ['name' => 'vIdZalo', 'value' => $userId, 'type' => PDO::PARAM_STR],
            ['name' => 'vOut', 'type' => PDO::PARAM_STMT],
        ];
        $message = '';
        $tongtien = 0;
        $isSuccess = 0;
        $values = $this->executeProcedureWithCursor($procedureName, $bindings);

        foreach ($values as $items) {
            foreach ($items as $item) {
                $isSuccess = $item['CODE'];
                if ($item['CODE'] == 0) {
                    $MA_TT = $item['MA_TT'];
                    $COQUANTT = $item['COQUANTT'];
                    $DIACHITT = $item['DIACHITT'];
                    $TIEN = $item['TIEN'];
                    $SLTHANG = $item['SLTHANG'];
                    $thangTT = Carbon::createFromFormat('Ym', $SLTHANG);
                    $message = $message . sprintf("- Mã thanh toán %s. \n    + Tên khách hàng %s. \n    + Cước tháng %s. \n    + Số tiền %s VNĐ.", $MA_TT, $COQUANTT, $thangTT->format('m/Y'), number_format($TIEN, 0, '.', ','));
                    $tongtien = $tongtien + $TIEN;
                } else {
                    $message = $item['MESSAGE'] . "\n";
                }
            }
        }
        if ($isSuccess == 0) {
            $message = sprintf("* Tổng số tiền cước của quý khách là: %s VNĐ. \n%s", number_format($tongtien, 0, '.', ','), $message);
        }else if ($message == ''){
            $message = "Quý khách chưa phát sinh cước. \n";
        }

        $message = $message . '⏰ Ngày tra cứu ' . Carbon::now()->format('d/m/Y') . "\n";
        $message = $message . 'VNPT Bắc Giang trân trọng thông báo!';

        $this->zaloAPI->SendMsgTuVan($userId, $message, null, null, null);
    }

    public function executeProcedureWithCursor($procedureName, $bindings)
    {
        $cursors = [];
        $result = [];
        $pdo = DB::connection('oracle')->getPdo();
        $command = sprintf('begin %s(:%s); end;', $procedureName, implode(', :', Arr::pluck($bindings, 'name')));
        $stmt = $pdo->prepare($command);

        foreach ($bindings as $key => $row) {
            if (isset($row['value']))
                $stmt->bindParam(":" . $row['name'], $row['value'], $row['type']);
            else
                $stmt->bindParam(":" . $row['name'], $result[$row['name']], $row['type']);

            if ($row['type'] === PDO::PARAM_STMT)
                $cursors[$row['name']] = $result[$row['name']];
        }
        $stmt->execute();
        $stmt->closeCursor();

        foreach ($cursors as $key => $cursor) {
            oci_execute($cursor, OCI_DEFAULT);
            oci_fetch_all($cursor, $result[$key], 0, -1, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC);
            oci_free_cursor($cursor);
        }

        return $result;
    }
}
