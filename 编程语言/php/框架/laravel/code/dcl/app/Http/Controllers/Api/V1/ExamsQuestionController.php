<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\AuthController;
use App\Http\Requests\Api\V1\ExamsQuestionFormRequest;
use App\Imports\Kspx\ExamsQuestionImport;
use App\Models\Kspx\ExamsQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ExamsQuestionController extends AuthController
{
    function __construct()
    {
        parent::__construct();
    }

    // 列表
    public function index(Request $request)
    {
        $condition = $this->queryCondition($request, [
            ['content', 'like', '%T%'],
            'exams_subject_id',
            'exams_module_id',
            'exams_point_id',
            'exams_question_type_id',
            'exams_question_difficult',
        ]);
        $sortRaw = $this->querySort($request, ['exams_question_id desc']);
        $count = ExamsQuestion::where($condition)->count();
        $limit = $this->queryPagination($request, $count);
        $items = ExamsQuestion::with([
            'ExamsSubject' => function ($query) {
                $query->select('exams_subject_id', 'exams_module_id', 'title');
            },
            'ExamsModule' => function ($query) {
                $query->select('exams_module_id', 'title');
            },
            'ExamsPoint' => function ($query) {
                $query->select('exams_point_id', 'exams_subject_id', 'title');
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
        if (!$request->filled('id')) {
            return $this->responseWithJson([], __('exams_question.id_not_exists'));
        }
        $data = ExamsQuestion::with([
            'ExamsSubject' => function ($query) {
                $query->select('exams_subject_id', 'exams_module_id', 'title');
            },
            'ExamsModule' => function ($query) {
                $query->select('exams_module_id', 'title');
            },
            'ExamsPoint' => function ($query) {
                $query->select('exams_point_id', 'exams_subject_id', 'title');
            },
            'User' => function ($query) {
                $query->select('uid', 'login', 'uname', 'true_name', 'email');
            },
            'School' => function ($query) {
                $query->select('id', 'title');
            },
        ])->find($request->input('id'));

        return $this->responseWithJson($data);
    }

    // 更新
    public function update(ExamsQuestionFormRequest $request)
    {
        if ($request->validate(['exams_question_id' => 'required|integer|min:1'])) {
            $condition = $this->queryCondition($request, ['exams_question_id']);
            $data = $request->only([
                'exams_question_type_id', // 试题分类ID
                'exams_subject_id', // 分类ID
                'exams_point_id', // 考点ID
                'exams_module_id', // 板块ID
                'level', // 难度
                'content', // 试题内容
                'answer_options', // 答题选项内容
                'answer_true_option', // 正确选项标识
                'analyze', // 试题解析
            ]);
            $data['answer_options'] = serialize($data['answer_options']);
            $data['answer_true_option'] = serialize($data['answer_true_option']);
            $result = ExamsQuestion::where($condition)->update($data);
            $msg = $result === 1 ? '' : __('common.update_fail');
            return $this->responseWithJson($result, $msg);
        }
    }

    // 添加
    public function create(ExamsQuestionFormRequest $request)
    {
        $data = $request->only([
            'exams_question_type_id', // 试题分类ID
            'exams_subject_id', // 分类ID
            'exams_point_id', // 考点ID
            'exams_module_id', // 板块ID
            'level', // 难度
            'content', // 试题内容
            'answer_options', // 答题选项内容
            'answer_true_option', // 正确选项标识
            'analyze', // 试题解析
        ]);
        $data['admin_id'] = $this->user->id;
        $data['answer_options'] = serialize($data['answer_options']);
        $data['answer_true_option'] = serialize($data['answer_true_option']);
        $result = ExamsQuestion::create($data);
        $msg = $result === 1 ? '' : __('common.insert_fail');
        return $this->responseWithJson($result, $msg);
    }

    /**
     * 批量软删除
     * @param Request $request
     */
    public function remove(Request $request)
    {
        $ids = $request->input('ids');
        $result = ExamsQuestion::destroy($ids);
        $msgKey = $result === 1 ? 'common.remove_success' : 'common.remove_failure';
        return $this->responseWithJson($result, __($msgKey));
    }

    /**
     * 永久删除
     * @param Request $request
     */
    public function forceRemove(Request $request)
    {
        $ids = $request->input('ids');
        $result = ExamsQuestion::find($ids)->forceDelete();
        $msgKey = $result === 1 ? 'common.delete_success' : 'common.delete_failure';
        return $this->responseWithJson($result, __($msgKey));
    }

    /**
     * 导入
     * @param Request $request
     */
    public function import(Request $request)
    {
        $import = new ExamsQuestionImport($this->user->id);
        Excel::import($import, $request->file('file'));
        dd($import->error);
    }

    /**
     * 试题类型数量统计
     * @param Request $request
     */
    public function typeAmountStat(Request $request)
    {
        $condition = $request->validate([
            'exams_subject_id' => [
                'required',
                'integer',
                'min:1',
                'exists:App\Models\Kspx\ExamsSubject,exams_subject_id'
            ],
            'exams_module_id' => [
                'required',
                'integer',
                'min:-1',
                'exists:App\Models\Kspx\ExamsModule,exams_module_id'
            ],
        ]);
        $condition['is_del'] = 0;
        $items = ExamsQuestion::withoutTrashed()
            ->select('exams_question_type_id', DB::raw('count(*) as total'))
            ->where($condition)
            ->groupBy('exams_question_type_id')
            ->get();
        return $this->responseWithJson([
            'items' => $items,
        ]);
    }
}
