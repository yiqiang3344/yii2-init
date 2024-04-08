<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/12/12
 * Time: 8:12 PM
 */

namespace console\controllers;


use console\models\LongProcessTrait;
use yiqiang3344\yii2_lib\helper\Time;

/**
 *
 * User: sidney
 * Date: 2020/1/10
 */
class LpTestController extends BaseController
{
    use LongProcessTrait;

    public function actionTest()
    {
        $num = 0;
        $this->longProcess(function () use (&$num) {
            $this->output(Time::now());
            if ($num++ > 10) {
                $this->termHandle();
            }
        });
    }
}