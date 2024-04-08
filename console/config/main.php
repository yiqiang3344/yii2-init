<?php
$params = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/params.php'
);
$ret = [
    'id' => 'console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => null,
        'response' => [
            'class' => '\yii\console\Response',
        ],
        'user' => null,
        'log' => [
            'flushInterval' => 1,
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
