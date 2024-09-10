<?php
	
namespace plugin\formhelper\api;

use Exception;
use support\exception\BusinessException;
use plugin\user\app\model\User as UserPlugin;

/**
 * 用户相关
 */
class User {
	/**
     * 获取登录用户基本信息
     * @return object
     * @throws Exception
     */
	public static function getSessionUser(): object
    {
    	if (!session('user.id')) {
    		throw new BusinessException('会话已经过期，请重新登录。', 401);
    	}
    	$sessionUser = UserPlugin::find(session('user.id'));
    	if (!$sessionUser) {
    		throw new BusinessException('未能找到对应用户！');
    	}
        if (!$sessionUser->avatar || $sessionUser->avatar == "/app/user/default-avatar.png")
            $sessionUser->avatar = static::gravatar($sessionUser->username);
    	return $sessionUser;
    }

    public static function gravatar($param = 'admin@admin.com', $size = '100') {
        $hash = md5(strtolower(trim($param)));
        return "https://cdn.v2ex.com/gravatar/$hash?d=retro&s=$size";
    }

}