<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/6/20
 * Time: 2:55 PM
 */

namespace api\modules\user\controllers;

use api\controllers\BaseController as Base;

class BaseController extends Base
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            //用户登录过滤
            [
                'class' => \common\filters\UserFilter::className(),
            ],
        ]);
    }
}