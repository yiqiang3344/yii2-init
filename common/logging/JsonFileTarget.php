<?php

namespace common\logging;

use common\helper\Env;
use common\models\User;
use Yii;
use yii\helpers\VarDumper;
use yii\log\Logger;
use yii\web\Request;

/**
 * Class FileTarget
 * @package common\logging
 */
class JsonFileTarget extends \yiqiang3344\yii2_lib\helper\log\JsonFileTarget
{
    /**
     * @param array $message
     * @return array
     * @throws \Exception
     * @throws \Throwable
     */
    public function getMessagePrefix($message)
    {
        if ($this->prefix !== null) {
            return call_user_func($this->prefix, $message);
        }

        if (Yii::$app === null) {
            return [];
        }

        $request = Yii::$app->getRequest();
        $ip = $request instanceof Request ? $request->getUserIP() : '';
        $mobile = $userID = '';
        /* @var $user \yii\web\User */
        $user = Yii::$app->has('user', true) ? Yii::$app->get('user') : null;
        if ($user && ($identity = $user->getIdentity(false))) {
            /** @var $identity User */
            $userID = $identity->id;
            $mobile = $identity->mobile;
        }

        return [
            'biz_type' => Env::getBizType(),
            'mobile' => $mobile,
            'app' => Env::getApp(),
            'inner_app' => Env::getInnerApp(),
            'user_id' => $userID,
            'request_float_number' => Env::getRequestFloatNumber(),
            'ip' => $ip,
        ];
    }
}
