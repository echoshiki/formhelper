<?php

namespace plugin\formhelper\app\model;
use support\Model;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * @property integer $id (主键)
 * @property integer $form_id 提交的表单id
 * @property string $user_id 提交数据的用户id
 * @property mixed $submitted_at 提交时间
 */
class FormSubmission extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'formhelper_form_submissions';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    // 反向关联到 Form 模型
    public function user() {
        return $this->belongsTo(\plugin\user\app\model\User::class, 'user_id');
    } 

    // 反向关联到 Form 模型
    public function form() {
        return $this->belongsTo(Form::class, 'form_id');
    } 

    // 正向关联到 FormFieldValue 模型
    public function field_values() {
        return $this->hasMany(FormFieldValue::class, 'form_submission_id');
    }

    /**
     * 级联删除
     * form_submissions 一对多关联了 form_field_values
     * @return boolean
     */
    public function deleteWithAssociations() {
        // 启用事务，确保删除操作的原子性
        DB::beginTransaction();
        try {
            // 删除所有的提交以及对应的字段值
            $this->field_values()->delete();
            $this->delete();
            // 提交事务
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception('删除失败: ' . $e->getMessage());
        }
    }
}
