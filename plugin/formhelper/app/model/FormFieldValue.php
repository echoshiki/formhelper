<?php

namespace plugin\formhelper\app\model;
use support\Model;

/**
 * @property integer $id (主键)
 * @property integer $form_submission_id 表单提交ID
 * @property integer $form_field_id 表单字段ID
 * @property string $value 字段值
 */
class FormFieldValue extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'formhelper_form_field_values';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    public function submission() {
        return $this->belongsTo(FormSubmission::class, 'form_submission_id');
    }
    
    
    
}
