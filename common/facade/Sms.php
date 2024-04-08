<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/6/12
 * Time: 3:51 PM
 */

namespace common\facade;

use common\helper\Env;
use Yii;
use yii\base\Model;

class Sms extends Model
{
    public static function send($mobile, $templateId, $data, $app, $innerApp, $channel = '', $notifyUrl = '')
    {
        $args = [
            'mobile' => $mobile,
            'template_id' => $templateId,
            'data' => $data,
            'app' => $app,
            'inner_app' => $innerApp,
            'ip' => Env::getIp(),
            'channel' => $channel,
            'notify_url' => $notifyUrl,
        ];
        return CommonSms::instance()->sendSms('sms/send', $args);
    }
}