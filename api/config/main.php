<?php
$params = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/params.php'
);
$ret = [
    'id' => 'api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
            'cookieValidationKey' => PROJECT_NAME . '-api.cookie',
        ],
    ],
    'params' => $params,
];
if (file_exists(__DIR__ . '/' . $webEnv . '/main.php')) {
    $ret = yii\helpers\ArrayHelper::merge($ret,
        require __DIR__ . '/' . $webEnv . '/main.php'
    );
}
return $ret;