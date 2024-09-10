<?php

namespace plugin\formhelper\app\model;
use support\Model;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * @property integer $id (主键)
 * @property string $title 表单标题
 * @property integer $user_id 创建表单的用户id
 * @property string $description 表单描述
 * @property mixed $created_at 
 * @property mixed $updated_at
 */
class Form extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'formhelper_forms';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    public function submissions() {
        return $this->hasMany(FormSubmission::class, 'form_id');
    }

    public function fields() {
        return $this->hasMany(FormField::class, 'form_id');
    }

    /**
     * 级联删除
     * 一对多关联了 form_submissions & form_fields
     * 而 form_submissions 一对多关联了 form_field_values
     * @return boolean
     */
    public function deleteWithAssociations() {
        // 启用事务，确保删除操作的原子性
        DB::beginTransaction();
        try {
            // 删除所有的提交以及对应的字段值
            foreach ($this->submissions as $submission) {
                $submission->field_values()->delete();
                $submission->delete();
            }
            // 删除该表单下的所有定义的字段
            $this->fields()->delete();
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
