<?php

namespace backend\helper;

use common\helper\Env;
use Yii;

class RequestHelper
{
    public $token;
    public $trace_id;

    private static $_instance = null;

    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct()
    {

    }

    public function headersFilter()
    {
        $headers = Yii::$app->request->getHeaders();
        $this->token = $headers->get('token');
        $this->trace_id = $headers->get('trace-id');

        Env::setAttr('token', $this->token);
        Env::setAttr('request_float_number', $this->trace_id);
        return true;
    }

    public function getFilter()
    {
        $this->token = Yii::$app->request->get("token");
        $this->trace_id = Yii::$app->request->get("trace_id");
        Env::setAttr('token', $this->token);
        Env::setAttr('request_float_number', $this->trace_id);
        return true;
    }
}