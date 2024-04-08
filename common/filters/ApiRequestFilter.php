<?php

namespace common\filters;

use common\exception\CUserException;
use common\facade\Config;
use common\helper\encrypt\Encrypt;
use common\helper\Env;
use common\helper\StringHelper;
use common\logging\AccessLog;
use yii\base\ActionFilter;
use Yii;
use yii\base\Exception;
use yii\base\UserException;

/**
 * api请求过滤器
 */
class ApiRequestFilter extends ActionFilter
{
    //不需要验签的路由
    const WHITE_ROUTES = [
    ];

    //不需要加密的路由
    const ENCRYPT_WHITE_ROUTES = [
    ];

    /**
     * @param \yii\base\Action $action
     * @return bool
     * @throws \Exception
     * @throws \yii\base\Exception
     * @throws \yii\base\UserException
     */
    public function beforeAction($action)
    {
        $uri = Yii::$app->request->getPathInfo();
        if (in_array($uri, self::WHITE_ROUTES)) {
            return true;
        }

        //header检查
        $this->checkHeader();

        //参数检查
        $params = $this->checkParams();

        //解析args参数
        $args = $params['args'];
        Env::setAttr('args', $args);
        Env::setAttr('ua', $params['ua']);

        return true;
    }

    /**
     * @throws CUserException
     * @throws Exception
     */
    private function checkHeader()
    {
        $rule = [
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

        $header = \Yii::$app->request->getHeaders();

        foreach ($rule as $k => $row) {
            if ($row['must'] && empty($header->get($k))) {
                throw new UserException($k . '不能为空', -2);
            }
            Env::setAttr($row['alias'] ?? StringHelper::lineToUnder($k), $header->get($k, ''));
        }

        return $header;
    }

    /**
     * @throws CUserException
     * @throws Exception
     */
    private function checkParams()
    {
        $params = \Yii::$app->request->getBodyParams();

        if (empty($params['ua'])) {
            throw new CUserException('参数不能为空：ua', -3);
        }
        if (empty($params['sign'])) {
            throw new CUserException('参数不能为空：sign', -3);
        }
        if (!isset($params['args'])) {
            throw new CUserException('参数缺失：args', -3);
        }

        //验签
        if (!$this->sign($params)) {
            throw new CUserException('验签失败', -3);
        }

        return $params;
    }


    /**
     * 验签
     * @param $params
     * @return bool
     * @throws CUserException
     * @throws Exception
     */
    protected function sign($params)
    {
        //验签开关关闭则不用验签
        if (!Config::instance()->getApiSignSwitch()) {
            return true;
        }

        $key = Config::instance()->getSignKeyByUa($params['ua']);
        if (!$key) {
            throw new CUserException('不合法的机构');
        }

        $sign = Encrypt::getSignByUa($params['ua'], $key, $params['args'], Yii::$app->controller->id . '/' . Yii::$app->controller->action->id);
        if ($sign != $params['sign']) {
            return false;
        }
        return true;
    }
}