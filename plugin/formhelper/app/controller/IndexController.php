<?php

namespace plugin\formhelper\app\controller;

use support\Request;

use plugin\user\app\model\User;

class IndexController
{

    public function index() {
        return json(['code' => 9, 'message' => '测试']);
    }

}
