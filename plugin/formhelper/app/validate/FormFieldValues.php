<?php
namespace plugin\formhelper\app\validate;

use think\Validate;

class FormFieldValues extends Validate
{
    protected $rule = [
        // // 表单字段信息验证
        'label' => 'require|max:50',
        'field_type' => 'require|in:text,select,switch,textarea,checkbox,file,number,date',
        'options' => 'array|checkOption',
        'required' => 'boolean',
        'sort' => 'integer|min:1|max:25',
        // 怎么验证比较好？
        'value' => 'checkedValue'
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

    protected function checkedValue($value, $rule, $data = []) {
        if ($data['required'] && $value == '') {
            return '必填项不能为空。';
        }
        switch ($data['field_type']) {
            case "text":
            case "textarea":
            case "select":
                return $this->checkText($value);
            case "checkbox":
                return $this->checkMultiple($value);
            case "date":
                return $this->checkDate($value);
            case "switch":
                return $this->checkSwitch($value);
            case "number":
                return $this->checkNumber($value);
            default:
                return "未知字段类型。";
        }
    }

    protected function checkText($value) {
        // 正则匹配允许的字符范围：中文、英文、数字、标点符号、换行符和空格
        $pattern = '/^[\x{4e00}-\x{9fa5}a-zA-Z0-9\s.。,，…!！?？;:"\'()\-]+$/u';
        if (!preg_match($pattern, $value)) {
            return '文本内容包含非法字符。';
        }
        // 限制文本长度
        if (mb_strlen($value) > 1000) {
            return '文本内容不能超过1000个字符。';
        }
        return true;
    }

    protected function checkMultiple($value) {
        if (!$this->checkRule($value, "array")) {
            return "字段值只能是数组";
        }
        return true;
    }

    protected function checkDate($value) {
        if (!$this->checkRule($value, "date")) {
            return "字段值只能是日期";
        }
        return true;
    }

    protected function checkSwitch($value) {
        if (!$this->checkRule($value, "boolean")) {
            return "字段值只能是布尔类型";
        }
        return true;
    }

    protected function checkNumber($value) {
        if (!$this->checkRule($value, "number")) {
            return "字段值只能是数字类型";
        }
        return true;
    }
    
}