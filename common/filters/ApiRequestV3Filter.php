<?php

namespace common\filters;

use common\facade\Config;
use common\helper\Env;
use yiqiang3344\yii2_lib\helper\filters\RequestV3Filter;
use yii\base\Exception;

/**
 * api请求过滤器
 */
class ApiRequestV3Filter extends RequestV3Filter
{
    //header字段规范
    public static $headerRule = [
        'encrypt-key' => ['must' => false],
        'source-type' => ['must' => false],
        'app' => ['must' => false],
        'inner-app' => ['must' => false],
        'token' => ['must' => false],
        'trace-id' => ['must' => true, 'alias' => 'request_float_number'],
        'device-id' => ['must' => false],
        'app-id' => ['must' => false],
        'sdk-version' => ['must' => false],
        'app-version' => ['must' => false],
        'os' => ['must' => false],
        'os-version' => ['must' => false],
        'channel' => ['must' => false],
        'imei' => ['must' => false],
        'oaid' => ['must' => false],
        'idfv' => ['must' => false],
        'idfa' => ['must' => false],
        'user-agent' => ['must' => false],
        'utm-source' => ['must' => false],
    ];

    /**
     * 不需要验签的路由
     */
    public function getNoSignWhiteRoutes(): array
    {
        return [];
    }

    /**
     * 参数不需要加密的路由
     */
    public function getArgsNoEncryptWhiteRoutes(): array
    {
        return [];
    }

    /**
     * 根据ua获取对应RSA私钥
     * @param string $ua
     * @return string
     * @throws Exception
     */
    public function getPriKey(string $ua): string
    {
        return \common\helper\config\Config::getString('ua_encrypt_key.' . $ua . '.private', '');
    }

    /**
     * 获取参数加密开关
     */
    public function getArgsEncryptSwitch(): bool
    {
        return Config::instance()->getApiEncryptSwitch();
    }

    /**
     * 获取验签开关
     */
    public function getSignSwitch(): bool
    {
        return Config::instance()->getApiSignSwitch();
    }

    /**
     * 根据自身项目初始化Env变量
     * @param array $envs
     * @throws Exception
     */
    public function initEnv(array $envs): void
    {
        foreach ($envs as $k => $env) {
            Env::setAttr($k, $env);
        }
    }
}