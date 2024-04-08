<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/12/11
 * Time: 11:33 AM
 */

namespace common\helper\db;


/**
 * 数据库连接类
 * Class BizWebClient
 * @package common\unit
 */
class DB extends \yiqiang3344\yii2_lib\helper\db\DB
{
    /**
     * 后台
     * @return \yii\db\Connection
     * @throws \yii\base\InvalidConfigException
     */
    public static function backend()
    {
        /** @var \yii\db\Connection $db */
        $db = \Yii::$app->get('backend');
        return $db;
    }
}