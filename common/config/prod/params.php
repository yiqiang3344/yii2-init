<?php
return [
    'domain' => [
        'inner_sms_center' => '找短信中心负责人要', //短信中心
    ],

    'secret' => [
        'sms_center_ua' => PROJECT_NAME,
        'sms_center_sign_key' => '找短信中心负责人要',
    ],

    'switch' => [
        'apiSign' => 'off', //api验签开关
        'apiEncrypt' => 'off', //api加密开关
        'ApiResponseEncrypt' => 'off', //api响应加密开关
    ],

    //允许跨域的域名
    'corsOrigin' => [
        '*',
    ],

    'userSdk' => [
        'url' => '', //用户SDK自定义域名，不自定义时不要改
        'ua' => PROJECT_NAME,
    ],

    'ua_encrypt_key' => [ //api加密秘钥，参考文档 https://www.tapd.cn/20090981/markdown_wikis/show/#1120090981001008661
        'request_ua' => [
            'private' => '开发人员自行生成并配置',
            'public' => '开发人员自行生成并配置',
        ],
    ],
];
