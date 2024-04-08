<?php

namespace backend\filters;

use backend\helper\RequestHelper;
use yii\base\ActionFilter;
use Yii;
use yii\base\UserException;
use yii\web\Response;

/**
 * 请求过滤器
 * Class RequestFilter
 * @package common\filters
 */
class RequestFilter extends ActionFilter
{
    //不限制的请求
    const ALLOW_URLs = [
    ];

    //允许GET的请求
    const ALLOW_GET_URLs = [
    ];

    public function init()
    {
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        parent::init();
    }

    public function beforeAction($action)
    {
        $pathInfo = Yii::$app->request->getPathInfo();
        if (in_array($pathInfo, self::ALLOW_URLs)) {
            return true;
        }

        $requestHelper = RequestHelper::getInstance();
        if (in_array($pathInfo, self::ALLOW_GET_URLs)) {
            $requestHelper->getFilter();
            return true;
        }

        $requestHelper->headersFilter();

        if (!Yii::$app->request->isPost) {
            throw new UserException('请求必须为POST', -2);
        }
        return true;
    }
}