<?php

namespace plugin\formhelper\app\model;
use support\Model;

/**
 * @property integer $id (主键)
 * @property integer $form_id 表单id
 * @property string $label 字段标签
 * @property string $field_type 字段类型
 * @property string $option 字段选项
 * @property integer $required 是否必须
 * @property mixed $created_at 
 * @property mixed $updated_at
 */
class FormField extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'formhelper_form_fields';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * 允许批量赋值的字段列表
     */ 
    protected $fillable = [
        'label',
        'field_type',
        'options',
        'required',
        'sort',
    ];

    // 反向关联到 Form 模型
    public function form() {
        return $this->belongsTo(Form::class, 'form_id');
    } 

    // 正向关联到 FormFieldValue 模型
    public function field_values() {
        return $this->hasMany(FormFieldValue::class, 'form_field_id');
    }
     
}
