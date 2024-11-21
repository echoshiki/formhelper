<?php
namespace plugin\formhelper\app\validate;

use think\Validate;

class Form extends Validate
{
    protected $rule = [
        // 表单基本信息验证
        'title' => 'require|chsAlphaNum|max:50',
        'description' => 'max:300',
        'started_at' => 'date',
        'expired_at' => 'date|afterWith:started_at',
        'limited' => 'integer|>=:0',
        'single' => 'boolean',
        'logged' => 'boolean',
        'disabled' => 'boolean',
    ];
    
    protected $message = [
        'title.require' => '表单标题不能为空。',
        'title.chsAlphaNum' => '表单标题只能是数字、字母以及汉字。',
        'title.max' => '表单标题必须小于50个字。',
        'description.max' => '表单标题必须小于300个字。',
        'started_at.date' => '开始时间必须是有效日期。',
        'expired_at.date' => '截止时间必须是有效日期。',
        'expired_at.afterWith' => '截止时间必须在开始日期之后。',
        'limited.integer' => '限制人数必须是数字。',
        'limited' => '限制人数必须大于等于0。',
    ];
    
}