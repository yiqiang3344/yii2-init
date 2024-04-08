<?php
$params = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/params.php'
);
$ret = [
    'id' => 'backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'charset' => 'utf-8',
    'language' => 'zh-CN',
    'timeZone' => 'Asia/Shanghai',
    'components' => [
        'user' => [
            'identityClass' => 'backend\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-api', 'httpOnly' => true],
        ],
        'request' => [
            'cookieValidationKey' => PROJECT_NAME . '-backend.cookie',
        ],
        'formatter' => [
            'dateFormat' => 'yyyy-MM-dd',
            'timeFormat' => 'HH:mm:ss',
            'datetimeFormat' => 'yyyy-MM-dd HH:mm:ss'
        ]
    ],
    'params' => $params,
];
if (file_exists(__DIR__ . '/' . $webEnv . '/main.php')) {
    $ret = yii\helpers\ArrayHelper::merge($ret,
        require __DIR__ . '/' . $webEnv . '/main.php'
    );
}
return $ret;