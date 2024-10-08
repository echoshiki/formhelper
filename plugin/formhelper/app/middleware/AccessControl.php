<?php
namespace plugin\formhelper\app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class AccessControl implements MiddlewareInterface
{
    public function process(Request $request, callable $next) : Response
    {
        // 如果是options请求则返回一个空响应，否则继续向洋葱芯穿越，并得到一个响应
        $response = $request->method() == 'OPTIONS' ? response('') : $next($request);
        // 给响应添加跨域相关的http头
        $response->withHeaders([
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Origin' => $request->header('origin', 'http://form.webman.lc:5173'),
            'Access-Control-Allow-Methods' => $request->header('access-control-request-method', 'GET, POST, PUT, DELETE, OPTIONS'),
            'Access-Control-Allow-Headers' => $request->header('access-control-request-headers', 'Content-Type, Authorization'),
        ]);
        return $response;
    }
}