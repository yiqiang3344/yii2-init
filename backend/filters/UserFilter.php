<?php

namespace backend\filters;

use backend\facade\Login;
use backend\models\User;
use common\helper\Env;
use yii\base\ActionFilter;
use Yii;
use yii\base\UserException;

class UserFilter extends ActionFilter
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

        if (empty(Env::getToken())) {
            throw new UserException('用户登录失败, 没有token', 200000);
        }
        $user = User::findIdentityByAccessToken(Env::getToken(), User::TYPE_JWT);
        if (!$user) {
            throw new UserException('用户登录失败, token失效', 200000);
        }

        //检查无操作的过期时间
        \backend\facade\User::checkNoActionLoginExpire($user);
        \backend\facade\User::checkNoActionUserExpire($user);

        //更新无操作过期时间
        \backend\facade\User::setNoActionLoginExpire($user);
        \backend\facade\User::setNoActionUserExpire($user);

        //检查密码是否过期
        if ($user->isPasswordExpire()) {
            throw new UserException('', 200020);
        }

        \Yii::$app->user->login($user);

        return true;
    }
}