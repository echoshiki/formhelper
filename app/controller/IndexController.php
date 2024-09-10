<?php

namespace app\controller;

use support\Request;
use support\Db;

class IndexController
{
    public function index(Request $request)
    {
        // static $readme;
        // if (!$readme) {
        //     $readme = file_get_contents(base_path('README.md'));
        // }
        // return $readme;
        $default_uid = 2;
        $uid = $request->get('uid', $default_uid);
        $name = Db::table('wa_rules')->where('id', $uid)->value('title');
        return response("hello $name");
    }

    public function view(Request $request)
    {
        return view('index/view', ['name' => 'webman']);
    }

    public function json(Request $request)
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }

    public function test(Request $request) {
        if ($request->method() === 'POST') {
            return json(['code' => 1, 'method' => 'POST', 'data' => [
                'session_value' => session('test-value'),
                'info' => $request->post()
            ]]);
        } elseif ($request->method() === 'GET') {
            $value = $request->get('value'); 
            $request->session()->set('test-value', $value);
            return json(['code' => 1, 'method' => 'GET', 'data' => [
                'sessions' => session('test-value'),
                'value' => $value
            ]]);
        }
    }

}
