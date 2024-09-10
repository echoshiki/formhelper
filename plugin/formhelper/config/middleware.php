<?php

use plugin\formhelper\app\middleware\AccessControl;

return [
    '' => [
        AccessControl::class,  
    ],
    'plugin.user' => [
        AccessControl::class, 
    ]
];
