<?php

namespace common\logging;


use common\facade\Config;

class EmailTarget extends \yiqiang3344\yii2_lib\helper\log\EmailTarget
{
    public function getMonitorAddressBook(): array
    {
        return Config::instance()->getMonitorAddressBook();
    }

    public function getNotifyBizTypes(): array
    {
        return ['common_error_notify'];
    }
}