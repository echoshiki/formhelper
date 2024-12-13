<?php

namespace plugin\formhelper\app\controller;

use support\Request;
use plugin\formhelper\app\model\Form;
use plugin\formhelper\app\model\FormSubmission;
use plugin\formhelper\app\model\FormField;
use plugin\formhelper\app\model\FormFieldValue;
use plugin\formhelper\app\validate\FormFieldValues as FormFieldValuesValidate;


class SubmissionController {
	/**
     * 查询指定表单的所有提交数据
     * @param Request $request
     * @return \support\Response
     */
	public function list(Request $request) {
		$page = (int) $request->get('page', 1);
		$pageSize = (int) $request->get('page_size', 10);
		$sortField = $request->get('sort_field', 'id');
		$sortOrder = $request->get('sort_order', 'desc');
		$search = $request->get('search', null);
		$form_id = (int) $request->get('form_id');

		if (!$form_id) return json([
			'code' => 500,
			'msg' => '没有找到有效的参数'
		], 500); 


		$user_id = session('user.id');
		$forms = Form::where('id', $form_id)->where('user_id', $user_id)->get();

		if ($forms->isEmpty()) return json([
			'code' => 401,
			'msg' => '未找到数据或者没有权限查看'
		], 500);

		try {
			// 建立数据库查询构造
			$query = FormSubmission::query();
			// 查询登录用户的数据
			$query->where("form_id", $form_id);
			// 排序
			$query->orderBy($sortField, $sortOrder);
			// 分页
			$submissions = $query->with([
				'form:id,title',
				'user:id,username'
			])->paginate($pageSize, ['*'], 'page', $page);	

			return json([
				'code' => 0,
				'msg' => 'ok',
				'data' => [
					'items' => $submissions->items(),
					'pagination' => [
						'total' => $submissions->total(),
						'page' => $submissions->currentPage(),
						'page_size' => $submissions->perPage(),
						'total_pages' => $submissions->lastPage(),
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
     * 删除数据
     * @param Request $request
     * @return \support\Response
     */
	public function deleteSelected(Request $request) {
		$ids = $request->post('ids');

		if (!$ids || empty($ids)) return json([
			'code' => 500,
			'msg' => '没有找到有效的参数'
		], 500); 

		// 筛选出提交数据对应的各个表单id
		$form_ids = FormSubmission::whereIn('id', $ids)->distinct()->pluck('form_id');

		if ($form_ids->count() > 1) return json([
			'code' => 500,
			'msg' => '提交数据不应归属不同的表单'
		], 500);

		// 验证用户是否有删除的权限
		$user_id = session('user.id');
		$form_id = $form_ids->first();	
		$forms = Form::where('id', $form_id)->where('user_id', $user_id)->get();

		if ($forms->isEmpty()) return json([
			'code' => 401,
			'msg' => '未找到该表单或者没有权限删除'
		], 500);

		$submissions = FormSubmission::whereIn('id', $ids)->get();

		try {
			foreach ($submissions as $submission) {
				// 删除该表单下的所有提交
				$submission->deleteWithAssociations();
			}
			return json([
				'code' => 0,
				'msg' => '提交数据删除成功'
			], 200);
		} catch (\Exception $e) {
			return json([
				'code' => 500,
				'msg' => $e->getMessage()
			], 500);
		}
	}

	/**
     * 查询提交数据详情
     * @param Request $request
     * @return \support\Response
     */
	public function view(Request $request) {
		$id = $request->get('id');

		if (!$id) return json([
			'code' => 500,
			'msg' => '没有找到有效的参数'
		], 500); 

		// 修改后不关联 fields 表获取字段信息
		$response = FormSubmission::with([
			'user',
			'form',
			'field_values'
		])->where('id', $id)->first();

		// 对查询出的结果进行排序
		$response->field_values = $response->field_values->sortBy(function ($field_value) {
		    // 根据 $field_value 内的 sort 值进行排序
		    return $field_value->sort;
		})->values();

		if (!$response || session('user.id') !== $response->form->user_id) return json([
			'code' => 401,
			'msg' => '未找到该数据或者没有权限查看'
		], 500);

		$data = [
			'fields' => $response->field_values,
			'username' => optional($response->user)->username ? $response->user->username : '匿名',
			'submitted_at' => $response->submitted_at,
		];

		return json([
			'code' => 0,
			'msg' => 'ok',
			'data' => $data
		], 200);
	}

	public function create(Request $request) {
		$form_id = $request->post('form_id');
		$fields = $request->post('fields');

		if (!$form_id) return json([
			'code' => 500,
			'msg' => '参数错误，非法提交'
		], 500);

		$formInfo = Form::find($form_id);

		// 检测是否被禁止
		if ($formInfo['disabled']) return json([
			'code' => 500,
			'msg' => '该表单已经被禁止。'
		], 500); 

		// 检测是否到期
		if (strtotime($formInfo['expired_at']) < time()) return json([
			'code' => 500,
			'msg' => '该表单已经过期了。'
		], 500); 

		// 检测用户限填一次
		if ($formInfo['logged'] && $formInfo['single']) {
			$user_id = session('user.id');
			if (!$user_id) return json([
				'code' => 401,
				'msg' => '缺少用户id，非法提交'
			], 500);

			$submitted_count = FormSubmission::where([
				'form_id' => $formInfo['id'],
				'user_id' => $user_id
			])->count();

			if ($submitted_count) return json([
				'code' => 500,
				'msg' => '该表单限制提交一次。'
			], 500); 
		}

		// 检测填写人数
		if ($formInfo['limited']) {
			$submitted_total_count = FormSubmission::where([
				'form_id' => $formInfo['id']
			])->count();

			// 如果填写次数大于限制次数
			if ($submitted_total_count >= $formInfo['limited']) return json([
				'code' => 500,
				'msg' => '已经超出该表单设定的最大填写数。'
			], 500); 
		}

		// 验证表单字段值
		if (!empty($fields)) {
			$formFieldValuesValidator = new FormFieldValuesValidate;
			foreach ($fields as $field) {
				if (!$formFieldValuesValidator->check($field)) {
					return json([
						'code' => 550,
						'msg' => $formFieldValuesValidator->getError()
					], 200);
				}
			}
			if (isset($field['value']) && ($field['field_type'] === 'text' || $field['field_type'] == 'textarea')) {
                $field['value'] = htmlspecialchars($field['value'], ENT_QUOTES, 'UTF-8');
            }
		} else {
			return json([
				'code' => 550,
				'msg' => '至少添加一个自定义填写项。'
			], 200);
		}

		// 需要插入 form_submissions & form_field_values 表
		$insertData = [
			'formSubmission' => [
				'form_id' => $form_id,
				'user_id' => session('user.id'),
				'submitted_at' => date('Y-m-d H:i:s'),
			],
			'formFieldValues' => $fields
		];

		try {
			$submission = new FormSubmission();
			$result = $submission->createWithFieldValues($insertData);
			return json([
				'code' => 0,
				'msg' => '数据提交成功'
			], 200);
		} catch (\Exception $e) {
			return json([
				'code' => 500,
				'msg' => $e->getMessage()
			], 500);
		}
	}
}



