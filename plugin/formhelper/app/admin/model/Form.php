<?php

namespace plugin\formhelper\app\admin\model;

use plugin\admin\app\model\Base;

/**
 * @property integer $id (主键)
 * @property string $title 表单标题
 * @property integer $user_id 创建表单的用户id
 * @property string $description 表单描述
 * @property mixed $created_at 
 * @property mixed $updated_at
 */
class Form extends Base
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
    
    
    
}
