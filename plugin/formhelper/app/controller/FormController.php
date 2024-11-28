<?php

namespace plugin\formhelper\app\controller;

use support\Request;
use plugin\formhelper\app\model\Form;
use plugin\formhelper\app\model\FormSubmission;
use plugin\formhelper\app\model\FormField;
use plugin\formhelper\app\model\FormFieldValue;
use plugin\formhelper\app\validate\Form as FormValidate;
use plugin\formhelper\app\validate\FormFields as FormFieldsValidate;

use think\facade\Validate;


class FormController {
	/**
     * 获取表单列表
     * @param Request $request
     * @return \support\Response
     */
	public function list(Request $request) {
		$page = (int) $request->get('page', 1);
		$pageSize = (int) $request->get('page_size', 10);
		$sortField = $request->get('sort_field', 'id');
		$sortOrder = $request->get('sort_order', 'desc');
		$search = $request->get('search', null);
		$user_id = session('user.id');

		try {
			// 建立数据库查询构造
			$query = Form::query();
			// 查询登录用户的数据
			$query->where("user_id", $user_id);
			// 搜索关键词，模糊查询标题和描述字段
			if (!empty($search)) {
				// 传入闭包函数
				// use ($search) 将外部搜索关键词变量传入函数内部
				$query->where(function($q) use ($search) {
	                	$q->where('title', 'like', "%$search%")
		                  ->orWhere('description', 'like', "%$search%");
	            });
			}
			// 排序
			$query->orderBy($sortField, $sortOrder);
			// 分页
			// 计算出 submissions_count
			$forms = $query->withCount('submissions')
						->paginate($pageSize, ['*'], 'page', $page);
			return json([
				'code' => 0,
				'msg' => 'ok',
				'data' => [
					'items' => $forms->items(),
					'user' => session('user.id'),
					'pagination' => [
						'total' => $forms->total(),
						'page' => $forms->currentPage(),
						'page_size' => $forms->perPage(),
						'total_pages' => $forms->lastPage(),
					]
				]
			]);
		} catch (\Exception $e) {
			return json([
				'code' => 500,
				'msg' => $e->getMessage()
			], 500);
		} 
	}

	/**
     * 查询表单详情
     * @param Request $request
     * @return \support\Response
     */
	public function view(Request $request) {
		$id = $request->get('id');

		if (!$id) return json([
			'code' => 500,
			'msg' => '没有找到有效的参数'
		], 500); 

		$view = Form::with([
			'fields',
		])->where([
			'id' => $id,
			'user_id' => session('user.id')
		])->first();

		if (!$view) return json([
			'code' => 401,
			'msg' => '未找到该表单或者没有权限查看'
		], 500);

		// Eloquent 模型内的结构关系需要手动更新改变
		$view->setRelation('fields', $view->fields->sortBy(function ($field) {
		    // 根据 $field 内的 sort 值进行排序
		    return $field->sort;
		})->values());

		$data['base'] = [
				'id' => $view->id,
				'title' => $view->title,
    			'description' => $view->description,
    			'started_at' => $view->started_at,
    			'expired_at' => $view->expired_at,
    			'limited' => $view->limited,
    			'single' => $view->single,
    			'logged' => $view->logged,
    			'disabled' => $view->disabled,
		];

	    $data['fields'] = $view->fields->map(function ($field) {
	        return [
	            'label' => $field->label, // 仅保留需要的字段
	            'field_type' => $field->field_type,   
	            'options' => json_decode($field->options),
	            'required' => $field->required,
	            'sort' => $field->sort, 
	        ];
	    });
	
		return json([
			'code' => 0,
			'msg' => 'ok',
			'data' => $data
		], 200);
	}

	/**
     * 创建表单
     * @param Request $request
     * @return \support\Response
     */
	public function create(Request $request) {

		$formBase = $request->post('formBase');
		$formFields = $request->post('formFields');

		// 验证表单基础字段
		$formValidator = new FormValidate;
		if (!$formValidator->check($formBase)) {
			return json([
				'code' => 550,
				'msg' => $formValidator->getError()
			], 200);
		}

		$formBase['started_at'] = $formBase['started_at'] ? $formBase['started_at'] : date('Y-m-d H:i:s');
		$formBase['expired_at'] = $formBase['expired_at'] ? $formBase['expired_at'] : null;

		// 验证表单自定义字段
		if (!empty($formFields)) {
			$formFieldsValidator = new FormFieldsValidate;
			foreach ($formFields as $field) {
				if (!$formFieldsValidator->check($field)) {
					return json([
						'code' => 550,
						'msg' => $formFieldsValidator->getError()
					], 200);
				}
			}
		} else {
			return json([
				'code' => 550,
				'msg' => '至少添加一个自定义填写项。'
			], 200);
		}

		try {
			$formBase['user_id'] = session('user.id');
			$form = new Form();
			$result = $form->createWithFields($formBase, $formFields);
			return json([
				'code' => 0,
				'msg' => '表单添加成功'
			], 200);
		} catch (\Exception $e) {
			return json([
				'code' => 500,
				'msg' => $e->getMessage()
			], 500);
		}
	}

	/**
     * 编辑表单
     * @param Request $request
     * @return \support\Response
     */
	public function edit(Request $request) {

		$formBase = $request->post('formBase');
		$formFields = $request->post('formFields');

		$id = $formBase['id'];

		if (!$id) return json([
			'code' => 500,
			'msg' => '没有找到有效的参数'
		], 500); 

		// 验证表单基础字段
		$formValidator = new FormValidate;
		if (!$formValidator->check($formBase)) {
			return json([
				'code' => 550,
				'msg' => $formValidator->getError()
			], 200);
		}

		$formBase['started_at'] = $formBase['started_at'] ? $formBase['started_at'] : date('Y-m-d H:i:s');
		$formBase['expired_at'] = $formBase['expired_at'] ? $formBase['expired_at'] : null;

		// 验证表单自定义字段
		if (!empty($formFields)) {
			$formFieldsValidator = new FormFieldsValidate;
			foreach ($formFields as $field) {
				if (!$formFieldsValidator->check($field)) {
					return json([
						'code' => 550,
						'msg' => $formFieldsValidator->getError()
					], 200);
				}
			}
		} else {
			return json([
				'code' => 550,
				'msg' => '至少添加一个自定义填写项。'
			], 200);
		}

		try {
			$formBase['user_id'] = session('user.id');
			$form = new Form();
			$result = $form->updateWithFields($formBase, $formFields);
			return json([
				'code' => 0,
				'msg' => '表单更新成功'
			], 200);
		} catch (\Exception $e) {
			return json([
				'code' => 500,
				'msg' => $e->getMessage()
			], 500);
		}
	}
	
	/**
     * 删除表单
     * @param Request $request
     * @return \support\Response
     */
	public function deleteSelected(Request $request) {
		$ids = $request->post('ids');

		if (!$ids || empty($ids)) return json([
			'code' => 500,
			'msg' => '没有找到有效的参数'
		], 500); 

		$user_id = session('user.id');

		$forms = Form::whereIn('id', $ids)->where('user_id', $user_id)->get();

		if ($forms->isEmpty()) return json([
			'code' => 401,
			'msg' => '未找到该表单或者没有权限删除'
		], 500);

		try {
			foreach ($forms as $form) {
				// 删除该表单下的所有提交
				$form->deleteWithAssociations();
			}
			return json([
				'code' => 0,
				'msg' => '表单删除成功'
			], 200);
		} catch (\Exception $e) {
			return json([
				'code' => 500,
				'msg' => $e->getMessage()
			], 500);
		}
	}
}



