<?php

namespace App\Http\Controllers\Api\V1\ExamSystem;

use App\Http\Controllers\Api\AuthController;
use App\Imports\ExamSystem\RegisterImport;
use App\Models\ExamSystem\Category;
use App\Models\ExamSystem\Register;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class RegisterController extends AuthController
{
    protected $model = 'App\Models\ExamSystem\Register';

    function __construct()
    {
        parent::__construct();
    }

    // 列表
    public function index(Request $request)
    {
        $condition = $this->queryCondition($request, [
            ['name', 'like', '%T%']
        ]);
        $sortRaw = $this->querySort($request, ['id desc']);
        $model = $this->queryListStatus($request);
        $count = $model->with([
            'category' => function ($query) {
                $query->select('id', 'name');
            },
            'classes' => function ($query) {
                $query->select('id', 'name');
            },
            'enterprise' => function ($query) {
                $query->select('id', 'name');
            },
            'user' => function ($query) {
                $query->select('id', 'true_name', 'id_card');
            },
            'enterprise_manager' => function($query) {
                $query->select('id', 'true_name', 'id_card');
            }
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
        $data = Register::find($condition['id']);

        return $this->responseWithJson($data);
    }

    // 更新
    public function update(Request $request)
    {
        $condition = $request->validate(['id' => 'required|integer|min:1']);
        $data = $request->only([
            'class_id',
            'user_id',
            'category_id',
            'type',
            'enterprise_id',
            'enterprise_manager',
            'health_status',
            'education',
            'majors',
            'profession',
            'job_title',
            'job_title_majors',
        ]);

        $result = Register::where($condition)->update($data);
        $result_flag = $result === 1 ? OP_SUCCESS : OP_FAILURE;
        return $this->responseWithJson($result, __('common.update'.$result_flag), $result_flag);
    }

    // 添加
    public function create(Request $request)
    {
        $data = $request->only([
            'class_id',
            'user_id',
            'category_id',
            'type',
            'health_status',
            'education',
            'majors',
            'profession',
            'job_title',
            'job_title_majors',
            'enterprise_id',
            'enterprise_manager',
        ]);

        $data['admin_id'] = $this->user->id;

        $result = Register::create($data);
        $result_flag = $result && $result['id'] > 0 ? OP_SUCCESS : OP_FAILURE;
        return $this->responseWithJson($result, __('common.create'.$result_flag), $result_flag);
    }

    /**
     * 导入
     * @param Request $request
     */
    public function import(Request $request)
    {
        $data = $request->validate(['class_id' => [
            'required',
            'integer',
            'min:1',
            'exists:App\Models\ExamSystem\Classes,id'
        ]]);
        $import = new RegisterImport($this->user->id, $data['class_id']);
        $file = $request->file('file');
        $result = Excel::import($import, $file);
        $result_flag = $result ? OP_SUCCESS : OP_FAILURE;
        return $this->responseWithJson($result, __('common.import'.$result_flag), $result_flag);
    }
}
