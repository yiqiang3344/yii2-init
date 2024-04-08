<?php

namespace backend\controllers;

use yiqiang3344\yii2_lib\helper\ArrayHelper;

/**
 * 公共接口的基础控制器，不验证权限
 * @baseController
 */
class PublicBaseController extends BaseController
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), []);
    }
}