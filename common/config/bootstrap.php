<?php
const PROJECT_NAME = 'yii2-init';
const PROJECT_NAME_ZH = 'yii2基础框架';

Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@api', dirname(dirname(__DIR__)) . '/api');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('@customLog', '/data/logs/' . PROJECT_NAME);

if (file_exists(__DIR__ . '/' . $webEnv . '/bootstrap.php')) {
    require __DIR__ . '/' . $webEnv . '/bootstrap.php';
}
