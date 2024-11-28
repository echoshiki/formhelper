<?php

namespace plugin\formhelper\app\controller;

use support\Request;
use plugin\formhelper\app\model\Form;
use plugin\formhelper\app\model\FormSubmission;
use plugin\formhelper\app\model\FormField;
use plugin\formhelper\app\model\FormFieldValue;

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
}



