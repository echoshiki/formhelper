<?php

namespace plugin\formhelper\app\controller;
use support\Request;
use plugin\formhelper\api\User;
use plugin\user\app\model\User as Users;

class ApiController {
    
    public function profile() {
        $userObject = User::getSessionUser();
        $user = [
            'id' => $userObject->id,
            'username' => $userObject->username,
            'avatar' => $userObject->avatar ?? "",
            'nickname' => $userObject->nickname ?? "",
            'email' => $userObject->email ?? "",
            'mobile' => $userObject->mobile ?? "",
            'sex' => $userObject->sex ?? "",
            'birthday' => $userObject->birthday ?? "",
            'money' => $userObject->money ?? 0,
            'score' => $userObject->score ?? 0,
            'last_time' => $userObject->last_time,
            'join_time' => $userObject->join_time,
        ];
        return json(['code' => 0, 'msg' => 'ok', 'data' => $user]);
    }

    public function updateProfile(Request $request) {
        $availableFields = [
            'nickname',
            'sex',
            'birthday',
            'mobile'
        ];
        $post = $request->post();
        $update = [];
        foreach ($availableFields as $field) {
            if (isset($post[$field])) {
                $update[$field] = $post[$field];
            }
        }
 
        if ($update) {
            $user = Users::find(session('user.id'));
            foreach ($update as $key => $value) {
                $user->$key = $value;
            }
            $user->save();
        }
        return json(['code' => 0, 'msg' => '修改成功']);
    }

}
