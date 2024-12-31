<?php

use Webman\Route;

Route::any('/formhelper[{path:.+}]', function ($path = '') {
    $pluginPublicPath = base_path('plugin/formhelper/public');
    // 检查是否存在静态资源文件
    $filePath = "$pluginPublicPath/$path";
    if (is_file($filePath)) {
        return response()->file($filePath);
    }
    // 对于 SPA 路由，返回 index.html
    return response()->file("$pluginPublicPath/index.html");
});