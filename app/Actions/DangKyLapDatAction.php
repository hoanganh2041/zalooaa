<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class DangKyLapDatAction extends AbstractAction
{
    public function getTitle()
    {
        return 'Xử lý';
    }

    public function getIcon()
    {
        return 'voyager-check';
    }

    public function getPolicy()
    {
        return 'browse';
    }

    public function getAttributes()
    {
        return [
            'class' => "btn btn-sm btn-success pull-right margin-right-5 xuly",
            'data-id' => $this->data->id
        ];
    }

    public function getDefaultRoute()
    {
        return 'javascript:;';
    }

    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug == 'dangkylapdat';
    }
}
