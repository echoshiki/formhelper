<?php

use plugin\formhelper\app\admin\controller\FormController;

return [
    [
        'title' => '自定义表单',
        'key' => 'formhelper',
        'icon' => 'layui-icon-align-left',
        'weight' => 0,
        'type' => 0,
        'children' => [
            [
                'title' => '表单列表',
                'key' => FormController::class,
                'href' => '/app/formhelper/admin/form/index',
                'type' => 1,
                'weight' => 0,
            ],
        ]
    ]
];
