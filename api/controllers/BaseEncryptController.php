<?php

namespace api\controllers;

use api\helper\CodeMessageEncrypt;
use common\filters\ApiRequestV3Filter;
use common\filters\Cors;
use common\helper\config\Config;
use yiqiang3344\yii2_lib\helper\ArrayHelper;
use yiqiang3344\yii2_lib\helper\log\TEncryptArgs;

/**
 * 支持请求加密及响应参数加密
 * User: xinfei
 * Date: 2021/11/24
 */
class BaseEncryptController extends \common\controllers\BaseController
{
    use TEncryptArgs;

    public function init()
    {
        parent::init();
        $this->codeMessage = new CodeMessageEncrypt();
    }

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class' => Cors::class, //跨域
                'cors' => [
                    'Origin' => Config::getArray('corsOrigin', ['*']),
                    'Access-Control-Request-Method' => ['GET', 'POST', 'OPTIONS'],
                    'Access-Control-Allow-Headers' => [
                        'Accept',
                        'Accept-Encoding',
                        'Accept-Language',
                        'Cache-Control',
                        'Connection',
                        'Content-Length',
                        'Content-Type',
                        'Host',
                        'Origin',
                        'Pragma',
                        'Referer',
                        'Qa-Env',
                        'From',
                        'Utm-Source',
                        'Version-Code',
                        'User-Agent',
                        'App',
                        'App-Name',
                        'Source-Type',
                        'Inner-App',
                        'Device-Id',
                        'Referer',
                        'Token',
                        'Jwt-Token',
                        'Channel',
                        'OS',
                        'Request-Float-Number',
                        'Mobile',
                        'Trace-Id',
                        'App-Id',
                        'Sdk-Version',
                        'App-Version',
                        'Os-Version',
                        'Imei',
                        'Oaid',
                        'Idfv',
                        'Idfa',
                        'Encrypt-Key',
                    ],
                ],
            ],
            [
                'class' => ApiRequestV3Filter::class, //请求头过滤
            ],
        ]);
    }
}