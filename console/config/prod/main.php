<?php
return [
    'components' => yii\helpers\ArrayHelper::merge([],
        require __DIR__ . '/db.php',
        require __DIR__ . '/redis.php'
    ),
    'params' => require __DIR__ . '/params.php',
];
