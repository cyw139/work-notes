<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\AuthController;
use App\Http\Requests\Api\V1\ExamsPaperFormRequest;
use App\Models\Kspx\ExamsPaper;
use Illuminate\Http\Request;

class ExamsPaperController extends AuthController
{
    protected $model = 'App\Models\Kspx\ExamsPaper';

    function __construct()
    {
        parent::__construct();
    }

    // 列表
    public function index(Request $request)
    {
        $condition = $this->queryCondition($request, [
            ['exams_paper_title', 'like', '%T%'],
            'exams_module_id',
        ]);
        $sortRaw = $this->querySort($request, ['exams_paper_id desc']);
        $model = $this->queryListStatus($request);
        $count = $model->where($condition)->count();
        $limit = $this->queryPagination($request, $count);
        $items = $model->with([
            'ExamsSubject' => function ($query) {
                $query->select('exams_subject_id', 'exams_module_id', 'title');
            },
            'ExamsModule' => function ($query) {
                $query->select('exams_module_id', 'title');
            },
            'User' => function ($query) {
                $query->select('uid', 'login', 'uname', 'true_name', 'email');
            },
            'School' => function ($query) {
                $query->select('id', 'title');
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
        $data = ExamsPaper::with([
            'ExamsSubject' => function ($query) {
                $query->select('exams_subject_id', 'exams_module_id', 'title');
            },
            'ExamsModule' => function ($query) {
                $query->select('exams_module_id', 'title');
            },
            'User' => function ($query) {
                $query->select('uid', 'login', 'uname', 'true_name', 'email');
            },
            'School' => function ($query) {
                $query->select('id', 'title');
            },
        ])->find($condition['id']);

        return $this->responseWithJson($data);
    }

    // 更新
    public function update(ExamsPaperFormRequest $request)
    {
        $condition = $request->validate(['exams_paper_id' => 'required|integer|min:1']);
        $data = $request->only([
            'exams_paper_title', // 试卷标题*
            'exams_subject_id', // 分类ID*
            'exams_module_id', // 板块ID*
            'description', // 试卷描述*
            'reply_time', // 答题时间*
            'level', // 试卷难度*
            'is_rand', // 是否随机试题*
            'start_time', // 考试开始时间*
            'end_time', // 考试结束时间*
            'sort', // 排序
            'exams_limit', // 考试次数限制*
            'assembly_type', // 组卷类型*
            'paper_options', // 试卷试题类型数据*
            'price', // 销售价格
        ]);
        $data['paper_options'] = serialize($data['paper_options']);
        $data['start_time'] = strtotime($data['start_time']);
        $data['end_time'] = strtotime($data['end_time']);
        $result = ExamsPaper::where($condition)->update($data);
        $result_flag = $result === 1 ? OP_SUCCESS : OP_FAILURE;
        return $this->responseWithJson($result, __('common.update'.$result_flag), $result_flag);
    }

    // 添加
    public function create(ExamsPaperFormRequest $request)
    {
        $data = $request->only([
            'exams_paper_title', // 试卷标题*
            'exams_subject_id', // 分类ID*
            'exams_module_id', // 板块ID*
            'description', // 试卷描述*
            'reply_time', // 答题时间*
            'level', // 试卷难度*
            'is_rand', // 是否随机试题*
            'start_time', // 考试开始时间*
            'end_time', // 考试结束时间*
            'sort', // 排序
            'exams_limit', // 考试次数限制*
            'assembly_type', // 组卷类型*
            'paper_options', // 试卷试题类型数据*
            'price', // 销售价格
        ]);
        $data['admin_id'] = $this->user->id;
        $data['paper_options'] = serialize($data['paper_options']);
        $data['start_time'] = strtotime($data['start_time']);
        $data['end_time'] = strtotime($data['end_time']);
        $result = ExamsPaper::create($data);
        $result_flag = $result && $result['exams_paper_id'] > 0 ? OP_SUCCESS : OP_FAILURE;
        return $this->responseWithJson($result, __('common.create'.$result_flag), $result_flag);
    }
}
