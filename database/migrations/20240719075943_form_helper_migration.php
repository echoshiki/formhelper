<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class FormHelperMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $this->table('formhelper_forms')
            ->addColumn('title', 'string', ['limit' => 255, 'comment' => '表单标题'])
            ->addColumn('user_id', 'integer', ['signed' => false])
            ->addColumn('description', 'text', ['null' => true, 'comment' => '表单描述'])
            ->addColumn('started_at', 'timestamp', ['null' => true, 'comment' => '开始时间'])
            ->addColumn('expired_at', 'timestamp', ['null' => true, 'comment' => '过期时间'])
            ->addColumn('limited', 'integer', ['default' => 0, 'comment' => '限制人数'])
            ->addColumn('single', 'boolean', ['default' => false, 'comment' => '是否限制单次'])
            ->addColumn('disabled', 'boolean', ['default' => false, 'comment' => '是否禁用'])
            ->addColumn('logged', 'boolean', ['default' => false, 'comment' => '是否需要登录'])
            ->addTimestamps()
            ->create();

        $this->table('formhelper_form_fields')
            ->addColumn('form_id', 'integer', ['signed' => false])
            ->addColumn('label', 'string', ['limit' => 255, 'comment' => '字段标签'])
            ->addColumn('field_type', 'enum', ['values' => ['text', 'textarea', 'select', 'checkbox', 'switch', 'file', 'date', 'number'], 'comment' => '字段类型'])
            ->addColumn('options', 'text', ['null' => true, 'comment' => '字段选项'])
            ->addColumn('required', 'boolean', ['default' => false, 'comment' => '是否必填'])
            ->addColumn('sort', 'integer', ['signed' => false, 'default' => 0, 'comment' => '排序'])
            ->addTimestamps()
            ->create();

        $this->table('formhelper_form_submissions')
            ->addColumn('form_id', 'integer', ['signed' => false])
            ->addColumn('user_id', 'integer', ['signed' => false])
            ->addColumn('submitted_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '提交时间'])   
            ->create();

        $this->table('formhelper_form_field_values')
            ->addColumn('form_submission_id', 'integer', ['signed' => false, 'comment' => '表单提交ID'])
            ->addColumn('label', 'string', ['limit' => 255, 'comment' => '字段标签'])
            ->addColumn('field_type', 'enum', ['values' => ['text', 'textarea', 'select', 'checkbox', 'switch', 'file', 'date', 'number'], 'comment' => '字段类型'])
            ->addColumn('sort', 'integer', ['signed' => false, 'default' => 0, 'comment' => '排序'])
            ->addColumn('value', 'text', ['comment' => '字段值'])
            ->create();
    }
}
