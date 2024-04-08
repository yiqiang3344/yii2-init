<?php

namespace common\error;

use common\exception\DBException;
use common\helper\CodeMessage;

class ErrorHandler extends \yii\base\ErrorHandler
{
    /**
     * @param \Exception $exception
     * @throws \Exception
     */
    public function renderException($exception)
    {
        if (\Yii::$app->id == 'console') {
            CodeMessage::showExceptionMessage($exception);
        }
        $showException = true;
        if (in_array(\Yii::$app->id, ['backend'])) {
            //跨域的应用即使是测试环境也不显示错误信息，避免前端无法处理
            $showException = false;
        }
        if ($exception instanceof DBException) {
            $arr = json_decode($exception->getMessage(), true);
            $code = -30;
            $message = $exception->getMessage();
            if (is_array($arr)) {
                $message = array_pop($arr)[0];
            }
            $response = CodeMessage::failed($code, YII_DEBUG ? $message : '');
        } else {
            $response = CodeMessage::getResponseFromException($exception, $showException);
        }
        $response->send();
    }
}