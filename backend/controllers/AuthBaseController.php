<?php

namespace backend\controllers;

use backend\facade\Operation;
use backend\filters\ApiRoleFilter;
use backend\filters\UserFilter;
use common\logging\DebugLog;
use yiqiang3344\yii2_lib\helper\ArrayHelper;

/**
 * 需要验证权限的基础控制器
 *
 * @baseController
 */
class AuthBaseController extends BaseController
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            //用户登录过滤
            [
                'class' => UserFilter::class,
            ],
            // api 权限验证
            [
                'class' => ApiRoleFilter::class,
            ],
        ]);
    }

    public function afterAction($action, $result)
    {
        $ret = parent::afterAction($action, $result);

        //记录操作日志
        Operation::log($result);

        //发送操作通知
        if ($this->checkSendOperationNotify()) {
            Operation::sendNotify($result);
        }

        return $ret;
    }

    /**
     * 判断是否发送操作通知
     * @return bool
     */
    public function checkSendOperationNotify()
    {
        try {
            $reflector = new \ReflectionClass($this);
            $doc = $reflector->getMethod($this->action->actionMethod)->getDocComment();
            preg_match('/@sendNotify/', $doc, $match);
            if (!empty($match[0])) {
                return true;
            }
            return false;
        } catch (\Throwable $e) {
            DebugLog::instance()->log((string)$e, 'send_operation_notify_error');
            return false;
        }
    }
}