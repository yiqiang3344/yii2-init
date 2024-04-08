<?php

namespace backend\filters;

use backend\facade\User;
use yii\base\ActionFilter;
use Yii;
use yii\base\UserException;

/**
 * 权限过滤器
 */
class ApiRoleFilter extends ActionFilter
{
    //允许放行的 url
    const ALLOW_URLs = [
    ];

    public function beforeAction($action)
    {
        $pathInfo = Yii::$app->request->getPathInfo();
        if (in_array($pathInfo, self::ALLOW_URLs)) {
            return true;
        }

        $nodeMap = User::getUserInterfaceNodes(User::getUser());
        if (!isset($nodeMap[$pathInfo])) {
            throw new UserException('', 200010);
        }

        return true;
    }
}