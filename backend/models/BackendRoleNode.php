<?php

namespace backend\models;

use common\models\MActiveRecord;

class BackendRoleNode extends \backend\tables\BackendRoleNode
{
    use MActiveRecord;

    const TYPE_MENU = 1;
    const TYPE_INTERFACE = 2;
}
