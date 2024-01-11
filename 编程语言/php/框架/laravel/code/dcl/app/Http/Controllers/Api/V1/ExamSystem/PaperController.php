<?php

namespace App\Http\Controllers\Api\V1\ExamSystem;

use App\Http\Controllers\Api\AuthController;
use App\Http\Requests\Api\V1\ExamSystem\PaperFormRequest;
use App\Models\ExamSystem\Category;
use App\Models\ExamSystem\Paper;
use Illuminate\Http\Request;

class PaperController extends AuthController
{
    protected $model = 'App\Models\ExamSystem\Paper';

    function __construct()
    {
        parent::__construct();
    }

    // 列表
    public function index(Request $request)
    {
        $condition = $this->queryCondition($request, [
            ['name', 'like', '%T%'],
            'category_id'
        ]);
        $sortRaw = $this->querySort($request, ['id desc']);
        $model = $this->queryListStatus($request);
        $count = $model->where($condition)->count();
        $limit = $this->queryPagination($request, $count);
        $items = $model->with([
            'category' => function ($query) {
                $query->select('id', 'name');
            },
        ])->where($condition)
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
        $data = Paper::with([
            'category' => function ($query) {
                $query->select('id', 'name');
            },
        ])->find($condition['id']);

        return $this->responseWithJson($data);
    }

    // 更新
    public function update(PaperFormRequest $request)
    {
        $condition = $request->validate(['id' => 'required|integer|min:1']);
        $data = $request->only([
            'name', // 试卷标题*
            'category_id', // 分类ID*
            'description', // 试卷描述*
            'reply_time', // 答题时间*
            'start_time', // 考试开始时间*
            'end_time', // 考试结束时间*
            'exams_limit', // 考试次数限制*
            'paper_options', // 试卷试题类型数据*
        ]);
        $data['paper_options'] = json_encode($data['paper_options']);
        $data['start_time'] = strtotime($data['start_time']);
        $data['end_time'] = strtotime($data['end_time']);
        $result = Paper::where($condition)->update($data);
        $result_flag = $result === 1 ? OP_SUCCESS : OP_FAILURE;
        return $this->responseWithJson($result, __('common.update'.$result_flag), $result_flag);
    }

    // 添加
    public function create(PaperFormRequest $request)
    {
        $data = $request->only([
            'name', // 试卷标题*
            'category_id', // 分类ID*
            'description', // 试卷描述*
            'reply_time', // 答题时间*
            'start_time', // 考试开始时间*
            'end_time', // 考试结束时间*
            'exams_limit', // 考试次数限制*
            'paper_options', // 试卷试题类型数据*
        ]);
        $data['admin_id'] = $this->user->id;
        $data['paper_options'] = json_encode($data['paper_options']);
        $data['start_time'] = strtotime($data['start_time']);
        $data['end_time'] = strtotime($data['end_time']);
        $result = Paper::create($data);
        $result_flag = $result && $result['id'] > 0 ? OP_SUCCESS : OP_FAILURE;
        return $this->responseWithJson($result, __('common.create'.$result_flag), $result_flag);
    }
}
