<?php
return yii\helpers\ArrayHelper::merge([
    'switch' => [
        'response' => [
            'httpStatus' => false,
        ],
    ],

    //开发人员通讯录
    'developerAddressBook' => [
        '易君强' => [
            'name' => '易君强',
            'email' => 'junqiang.yi2@xinfei.cn',
            'mobile' => '18621927050',
        ],
        '魏建华' => [
            'name' => '魏建华',
            'email' => 'jianhua.wei@xinfei.cn',
            'mobile' => '13122315219',
        ],
    ]
],
    require __DIR__ . '/' . $webEnv . '/params.php'
);
