<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\AuthController;
use App\Http\Requests\Api\V1\CoursewareFormRequest;
use App\Models\Kspx\Courseware;
use Illuminate\Http\Request;

class CoursewareController extends AuthController
{
    protected $model = 'App\Models\Kspx\Courseware';

    function __construct()
    {
        parent::__construct();
    }

    // 列表
    public function index(Request $request)
    {
        $condition = $this->queryCondition($request, [
            ['title', 'like', '%T%'],
        ]);
        $sortRaw = $this->querySort($request, ['id desc']);
        $model = $this->queryListStatus($request);
        $count = $model->where($condition)->count();
        $limit = $this->queryPagination($request, $count);
        $items = $model->with([
            'CoursewareCategory' => function ($query) {
                $query->select('zy_currency_category_id', 'title');
            },
            'User' => function ($query) {
                $query->select('uid', 'login', 'uname', 'true_name', 'email');
            },
        ])->where($condition)
            ->skip($limit['start'])
            ->take($limit['size'])
            ->orderByRaw($sortRaw)
            ->get();
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
        $data = Courseware::with([
            'User' => function ($query) {
                $query->select('uid', 'login', 'uname', 'true_name', 'email');
            },
            'Attaches' => function ($query) {
                $query->select('attach.attach_id', 'name', 'save_path', 'save_name', 'resource_type', 'storage_type',
                    'transcoding_status', 'duration', 'extension');
            }
        ])->find($condition['id']);

        return $this->responseWithJson($data);
    }

    // 更新
    public function update(CoursewareFormRequest $request)
    {
        $condition = $request->validate(['exams_paper_id' => 'required|integer|min:1']);
        $data = $request->only([
        ]);
        $result = Courseware::where($condition)->update($data);
        $result_flag = $result === 1 ? OP_SUCCESS : OP_FAILURE;
        return $this->responseWithJson($result, __('common.update' . $result_flag), $result_flag);
    }

    // 添加
    public function create(CoursewareFormRequest $request)
    {
        $data = $request->only([
            'title', // 课件标题
            'sort', // 排序
        ]);
        $data['admin_id'] = $this->user->id;
        $result = Courseware::create($data);
        $result_flag = $result && $result['id'] > 0 ? OP_SUCCESS : OP_FAILURE;
        return $this->responseWithJson($result, __('common.create' . $result_flag), $result_flag);
    }
}
