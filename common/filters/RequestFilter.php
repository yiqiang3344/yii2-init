<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/6/8
 * Time: 1:55 PM
 */

namespace common\filters;

use common\helper\RequestHelper;
use yii\base\ActionFilter;
use Yii;
use yii\base\UserException;

/**
 * 请求过滤器
 * Class RequestFilter
 * @package common\filters
 */
class RequestFilter extends ActionFilter
{
    public function init()
    {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        parent::init();
    }

    /**
     * @param \yii\base\Action $action
     * @return bool
     * @throws \Exception
     * @throws \yii\base\Exception
     * @throws \yii\base\UserException
     */
    public function beforeAction($action)
    {
        if (!Yii::$app->request->isPost) {
            throw new UserException('请求必须为POST');
        }

        $requestHelper = RequestHelper::instance();
        $requestHelper->headersFilter();

        return true;
    }

    public function afterAction($action, $result)
    {
        return parent::afterAction($action, $result);
    }
}