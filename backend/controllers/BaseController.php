<?php

namespace backend\controllers;

use backend\filters\RequestFilter;
use common\filters\Cors;
use common\helper\config\Config;

/**
 * 基础控制器，支持跨域，需要有统一的header头参数
 *
 * @baseController
 */
class BaseController extends \common\controllers\BaseController
{
    public function behaviors()
    {
        return [
            //跨域过滤
            [
                'class' => Cors::class,
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
                        'User-Agent',
                        'Qa-Env',
                        'App-Name',
                        'App-Version',
                        'Channel',
                        'Content-Type',
                        'From',
                        'OS',
                        'Referer',
                        'Source-Type',
                        'Token',
                        'User-Agent',
                        'Version-Code',
                    ],
                ],
            ],
            //请求头过滤
            [
                'class' => RequestFilter::class,
            ],
        ];
    }
}