<?php

namespace plugin\formhelper\app\admin\controller;

use support\Request;
use support\Response;
use plugin\formhelper\app\admin\model\Form;
use plugin\admin\app\controller\Crud;
use support\exception\BusinessException;

/**
 * 表单列表 
 */
class FormController extends Crud
{
    
    /**
     * @var Form
     */
    protected $model = null;

    /**
     * 构造函数
     * @return void
     */
    public function __construct()
    {
        $this->model = new Form;
    }
    
    /**
     * 浏览
     * @return Response
     */
    public function index(): Response
    {
        return view('form/index');
    }

    /**
     * 插入
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function insert(Request $request): Response
    {
        if ($request->method() === 'POST') {
            return parent::insert($request);
        }
        return view('form/insert');
    }

    /**
     * 更新
     * @param Request $request
     * @return Response
     * @throws BusinessException
    */
    public function update(Request $request): Response
    {
        if ($request->method() === 'POST') {
            return parent::update($request);
        }
        return view('form/update');
    }

}
