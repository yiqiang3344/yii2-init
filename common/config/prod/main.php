<?php
return [
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'common\logging\EmailTarget',
                    'levels' => ['error'],
                    'except' => [
                        'yii\web\HttpException:404',
                        'yii\web\HttpException:400',
                        'yii\web\HttpException:403',
                        'yiqiang3344\yii2_lib\helper\exception\ParamsInvalidException',
                        'yiqiang3344\yii2_lib\helper\exception\OptionsException',
                        'common\exception\CUserException',
                        'yiqiang3344\yii2_lib\helper\exception\UserException',
                        'yii\base\UserException',
                        'yii\base\InvalidRouteException',
                    ],
                    'message' => [
                        'from' => ['sms-api'],
                        'subject' => '',
                    ],
                ],
            ],
        ],
    ],
];