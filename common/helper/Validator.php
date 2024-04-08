<?php
/**
 * Created by PhpStorm.
 * User: sidney
 * Date: 2019/9/5
 * Time: 5:24 PM
 */

namespace common\helper;


use yiqiang3344\yii2_lib\helper\exception\ParamsInvalidException;

class Validator extends \yiqiang3344\yii2_lib\helper\validator\Validator
{
    /**
     * @inheritDoc
     */
    public static function checkParams(&$params, $needParams)
    {
        try {
            parent::checkParams($params, $needParams);
        } catch (ParamsInvalidException $e) {
            throw new ParamsInvalidException($e->getMessage(), -3);
        }
    }
}