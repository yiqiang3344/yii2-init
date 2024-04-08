<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/6/20
 * Time: 3:08 PM
 */

namespace common\filters;

use common\helper\JwtHelper;
use yiqiang3344\yii2_lib\helper\Env;
use yii\base\ActionFilter;
use Yii;
use yii\base\UserException;

class UserFilter extends ActionFilter
{
    //允许放行的 url
    const ALLOW_URLs = [
        'user/login',
    ];

    /**
     * @param \yii\base\Action $action
     * @return bool
     * @throws UserException
     * @throws \Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function beforeAction($action)
    {
        $pathInfo = Yii::$app->request->getPathInfo();

        if (in_array($pathInfo, self::ALLOW_URLs)) return true;

        if (empty(Env::getToken())) throw new UserException('用户登录失败');

        JwtHelper::validateToken(Env::getToken());

        return true;
    }
}