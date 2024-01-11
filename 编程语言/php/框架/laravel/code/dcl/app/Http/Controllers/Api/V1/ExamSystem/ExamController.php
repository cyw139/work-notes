<?php

namespace App\Http\Controllers\Api\V1\ExamSystem;

use App\Http\Controllers\Api\AuthController;
use App\Models\ExamSystem\Category;
use App\Models\ExamSystem\Exam;
use Illuminate\Http\Request;

class ExamController extends AuthController
{
    protected $model = 'App\Models\ExamSystem\Exam';

    function __construct()
    {
        parent::__construct();
    }

    // 列表
    public function index(Request $request)
    {
        $condition = $this->queryCondition($request, [
            'register_id',
            'class_id',
            'user_id',
        ]);
        $sortRaw = $this->querySort($request, ['id desc']);
        $model = $this->queryListStatus($request);
        $count = $model->with([
            'classes' => function ($query) {
                $query->select('id', 'name');
            },
            'register' => function ($query) {
                $query->select();
            },
            'user' => function ($query) {
                $query->select('id', 'true_name', 'id_card');
            },
        ])
            ->where($condition)->count();
        $limit = $this->queryPagination($request, $count);
        $items = $model->where($condition)
            ->skip($limit['start'])
            ->take($limit['size'])
            ->orderByRaw($sortRaw)
            ->get();
//        Category::ancestorsFormatter($items);
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
        $data = Exam::find($condition['id']);

        return $this->responseWithJson($data);
    }
}
