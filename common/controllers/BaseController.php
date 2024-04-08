<?php

namespace common\controllers;


use common\helper\CodeMessage;
use yiqiang3344\yii2_lib\helper\log\TAccessLog;
use common\models\User;
use yii\web\Controller;
use yii\web\Request;

/**
 * Class BaseController
 * @package common\controllers
 * @property User $user
 * @property Request $request
 */
class BaseController extends Controller
{
    use TAccessLog;

    public $enableCsrfValidation = false;

    public $request = 'request'; //框架底层有用到，初始化值必须为request
    public $user;
    /** @var \yiqiang3344\yii2_lib\helper\CodeMessage */
    public $codeMessage; //需要再控制器中初始化

    public function init()
    {
        parent::init();
        $this->codeMessage = new CodeMessage();
    }

    /**
     * 成功的响应
     * @param array $data
     * @return \yii\web\Response
     */
    public function success($data = null)
    {
        return $this->codeMessage::success($data);
    }

    /**
     * 失败的响应
     * @param $code
     * @param string $subMessage
     * @param array $data
     * @return \yii\web\Response
     */
    public function failed($code, $subMessage = '', $data = null)
    {
        return $this->codeMessage::failed($code, $subMessage, $data);
    }
}