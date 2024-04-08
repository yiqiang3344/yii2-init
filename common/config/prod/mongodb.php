<?php

return [
    '业务架构组配置' => [
        'class' => '\yii\mongodb\Connection',
        'dsn' => 'mongodb://mongodbHost:27017/admin',
        'options' => [
            'username' => 'mongodbUser',
            'password' => 'mongodbPwd',
        ],
    ],
];