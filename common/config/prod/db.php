<?php
return [
    'db' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=dbHost;dbname=dbDatabase',
        'username' => 'dbUser',
        'password' => 'dbPwd',
        'charset' => 'utf8',
        'tablePrefix' => '',
    ],
    'backend' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=dbBackendHost;dbname=dbBackendDatabase',
        'username' => 'dbBackendUser',
        'password' => 'dbBackendPwd',
        'charset' => 'utf8',
        'tablePrefix' => '',
    ],
];