<?php

namespace common\helper\oss;



use yiqiang3344\yii2_lib\helper\config\Config;
use common\helper\Env;
use yiqiang3344\yii2_lib\helper\oss\Oss;

class XyfOss
{
    /**
     * 单例
     * @param null $app
     * @return Oss
     * @throws \yii\base\Exception
     */
    public static function getInstance($app = null)
    {
        $defaultConfig = Config::getArray('oss');
        $app = $app ?: Env::getApp();
        $config = Config::getArray('oss.' . $app); //不同app可以自定义配置，没有则使用默认配置
        $config['accessKeyId'] = $config['accessKeyId'] ?? $defaultConfig['accessKeyId'] ?? null;
        $config['accessKeySecret'] = $config['accessKeySecret'] ?? $defaultConfig['accessKeySecret'] ?? null;
        $config['endpoint'] = $config['endpoint'] ?? $defaultConfig['endpoint'] ?? null;
        $config['domain'] = $config['domain'] ?? $defaultConfig['domain'] ?? null;
        $config['securityToken'] = $config['securityToken'] ?? $defaultConfig['securityToken'] ?? null;
        $config['timeout'] = $config['timeout'] ?? $defaultConfig['timeout'] ?? null;
        $config['bucket'] = $config['bucket'] ?? $defaultConfig['bucket'] ?? null;
        return Oss::getInstance($config['bucket'], $config);
    }
}