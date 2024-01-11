<?php

namespace App\Http\Controllers\Api\V1\ExamSystem;

use App\Http\Controllers\Api\AuthController;
use App\Models\ExamSystem\Category;
use App\Models\ExamSystem\Classes;
use Illuminate\Http\Request;

class ClassesController extends AuthController
{
    protected $model = 'App\Models\ExamSystem\Classes';

    function __construct()
    {
        parent::__construct();
    }

    // 列表
    public function index(Request $request)
    {
        $condition = $this->queryCondition($request, [['name', 'like', '%T%']]);
        $sortRaw = $this->querySort($request, ['id desc']);
        $model = $this->queryListStatus($request);
        $count = $model->with([
            'category' => function ($query) {
                $query->select('id', 'name');
            },
            'paper' => function ($query) {
                $query->select('id', 'name');
            },
        ])
            ->where($condition)->count();
        $limit = $this->queryPagination($request, $count);
        $items = $model->where($condition)
            ->skip($limit['start'])
            ->take($limit['size'])
            ->orderByRaw($sortRaw)
            ->get();
        Category::ancestorsFormatter($items);
        return $this->responseWithJson([
            'total' => $count,
            'items' => $items,
            'size' => $limit['size'],
        ]);
    }

    // 详情
    public function detail(Request $request)
    {
        $condition = $request->validate(['id' => 'required|integer|min:1']);
        $data = Classes::find($condition['id']);

        return $this->responseWithJson($data);
    }

    // 更新
    public function update(Request $request)
    {
        $condition = $request->validate(['id' => 'required|integer|min:1']);
        $data = $request->only([
            'name',
            'category_id',
            'paper_id',
        ]);

        $result = Classes::where($condition)->update($data);
        $result_flag = $result === 1 ? OP_SUCCESS : OP_FAILURE;
        return $this->responseWithJson($result, __('common.update'.$result_flag), $result_flag);
    }

    // 添加
    public function create(Request $request)
    {
        $data = $request->only([
            'name',
            'category_id',
            'paper_id',
        ]);

        $data['admin_id'] = $this->user->id;

        $result = Classes::create($data);
        $result_flag = $result && $result['id'] > 0 ? OP_SUCCESS : OP_FAILURE;
        return $this->responseWithJson($result, __('common.create'.$result_flag), $result_flag);
    }
}
