<?php
$_exportInterval = $webEnv == 'prod' && $applicationEnv != 'console' ? 1000 : 1;
$config = yii\helpers\ArrayHelper::merge([
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'timeZone' => 'Asia/Shanghai',
    'components' => array_merge([
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'response' => [
            'class' => 'yiqiang3344\yii2_lib\helper\WebResponse',
        ],
        'errorHandler' => [
            'class' => 'common\error\ErrorHandler',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'common\logging\JsonFileTarget',
                    'levels' => ['error', 'warning'],
                    'logVars' => [],
                    'except' => [
                        'yii\httpclient\*',
                        'yiqiang3344\yii2_lib\helper\exception\ParamsInvalidException',
                        'yiqiang3344\yii2_lib\helper\exception\OptionsException',
                        'common\exception\CUserException',
                        'yiqiang3344\yii2_lib\helper\exception\UserException',
                        'yii\base\UserException',
                        'yii\base\InvalidRouteException',
                        'slowSql',
                    ],
                    'exportInterval' => $_exportInterval,
                    'logFileParams' => [
                        'base_path' => '@customLog/app',
                        'format' => 'Ymd', //日志文件格式
                    ],
                ],
                [
                    'class' => 'common\logging\JsonFileTarget',
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
                    'logVars' => [],
                    'exportInterval' => $_exportInterval,
                    'logFileParams' => [
                        'base_path' => '@customLog/error',
                        'format' => 'Ymd', //日志文件格式
                    ],
                ],
                [
                    'class' => 'common\logging\JsonFileTarget',
                    'levels' => ['info', 'error', 'warning'],
                    'categories' => ['debug'],
                    'logVars' => [],
                    'exportInterval' => $_exportInterval,
                    'logFileParams' => [
                        'base_path' => '@customLog/debug',
                        'format' => 'Ymd', //日志文件格式
                    ],
                ],
                [
                    'class' => 'common\logging\JsonFileTarget',
                    'levels' => ['error', 'warning'],
                    'categories' => ['yii\db\*', 'slowSql'],
                    'logVars' => [],
                    'exportInterval' => $_exportInterval,
                    'logFileParams' => [
                        'base_path' => '@customLog/db',
                        'format' => 'Ymd', //日志文件格式
                    ],
                ],
                [
                    'class' => 'common\logging\JsonFileTarget',
                    'levels' => ['info'],
                    'categories' => ['web_client'],
                    'logVars' => [],
                    'exportInterval' => $_exportInterval,
                    'logFileParams' => [
                        'base_path' => '@customLog/web_client',
                        'format' => 'Ymd', //日志文件格式
                    ],
                ],
                [
                    'class' => 'common\logging\JsonFileTarget',
                    'levels' => ['info'],
                    'categories' => ['web_client_timeout'],
                    'logVars' => [],
                    'exportInterval' => $_exportInterval,
                    'logFileParams' => [
                        'base_path' => '@customLog/web_client_timeout',
                        'format' => 'Ymd', //日志文件格式
                    ],
                ],
                [
                    'class' => 'common\logging\JsonFileTarget',
                    'levels' => ['info'],
                    'categories' => ['access'],
                    'logVars' => [],
                    'exportInterval' => $_exportInterval,
                    'logFileParams' => [
                        'base_path' => '@customLog/access',
                        'format' => 'Ymd', //日志文件格式
                    ],
                ],
                [
                    'class' => 'common\logging\JsonFileTarget',
                    'levels' => ['info'],
                    'categories' => ['console'],
                    'logVars' => [],
                    'exportInterval' => $_exportInterval,
                    'logFileParams' => [
                        'base_path' => '@customLog/console',
                        'format' => 'Ymd', //日志文件格式
                    ],
                ],
                [
                    'class' => 'common\logging\JsonFileTarget',
                    'levels' => ['info', 'warning', 'error'],
                    'categories' => ['biz'],
                    'logVars' => [],
                    'exportInterval' => $_exportInterval,
                    'logFileParams' => [
                        'base_path' => '@customLog/biz',
                        'format' => 'Ymd', //日志文件格式
                    ],
                ],
            ],
        ],
        'event' => [
            'class' => \yiqiang3344\yii2_lib\helper\event\Event::class,
            'listen' => [
                \yiqiang3344\yii2_lib\helper\event\events\SlowSqlEvent::class => [
                    \common\event\listeners\SlowSqlAlter::class,
                ],
            ],
        ],
    ],
        require __DIR__ . '/' . $webEnv . '/db.php',
        require __DIR__ . '/' . $webEnv . '/redis.php',
        require __DIR__ . '/' . $webEnv . '/mongodb.php'

    ),
],
    require __DIR__ . '/' . $webEnv . '/main.php'
);

if ($webEnv != 'prod') {
    // configuration adjustments for 'dev' environment
    if ($applicationEnv != 'console') {
        $config['bootstrap'][] = 'debug';
        $config['modules']['debug'] = [
            'class' => 'yii\debug\Module',
            'allowedIPs' => ['*']
        ];
    }

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*']
    ];
}

return $config;
