<?php
namespace plugin\formhelper\app\validate;

use think\Validate;

class FormFields extends Validate
{
    protected $rule = [
        // // 表单字段信息验证
        'label' => 'require|max:50',
        'field_type' => 'require|in:text,select,switch,textarea,checkbox,file,number,date',
        'options' => 'array|checkOption',
        'required' => 'boolean',
        'sort' => 'integer|min:1|max:25'
    ];
    
    protected $message = [
        'label.require' => "自定义字段名称不能为空。",
        'label.max' => "自定义字段名称最大不超过50个字符。",
        'field_type.require' => "自定义字段类型不能为空",
        'field_type.in' => "自定义字段类型出错",
        'options.array' => "自定义字段的选项类型必须为数组",
        'sort.integer' => "自定义字段排序类型必须为整型",
        'sort.min' => "自定义字段排序长度最小为1个字符",
        'sort.max' => "自定义字段排序长度最小为25个字符",  
    ];

    protected function checkOption($value, $rule, $data = []) {
        // 判断字母、数字以及汉字类型的正则
        $pattern = '/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]+$/u';
        foreach ($value as $option) {
            if (strlen($option) > 50) {
                return '自定义字段的选项内容不能超过50个字符。';
            }
            if (!preg_match($pattern, $option)) {
                return '自定义字段的选项内容只能包含中文、字母或数字。';
            }
        }
        return true;
    }
    
}