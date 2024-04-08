<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/12/12
 * Time: 8:12 PM
 */

namespace console\controllers;


use common\helper\db\DB;
use yiqiang3344\yii2_lib\helper\Time;
use yiqiang3344\yii2_lib\helper\webClient\WebClientV2;
use yii\base\Exception;

/**
 *
 * User: sidney
 * Date: 2020/1/10
 */
class TestController extends BaseController
{
    /**
     * 标准测试用例，不要修改
     */
    public function actionTest()
    {
        echo 'success' . PHP_EOL;
    }

    /**
     * http客户端测试
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionWebclient()
    {
        $ret = WebClientV2::get('https://baidu.com', [], [], [
            CURLOPT_FOLLOWLOCATION => 1,
        ]);
        var_dump(WebClientV2::$httpStatusCode, $ret, WebClientV2::$result);
    }

    /**
     * 慢sql捕捉测试
     * @throws \yii\db\Exception
     */
    public function actionSlowSql()
    {
        $m = microtime();
        $ret = DB::default()->createCommand('select * from User where 1 order by id asc limit 1')->queryOne();
        var_dump(Time::getSubMicroTime(microtime(), $m), $ret);
    }
}