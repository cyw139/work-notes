<?php

namespace App\Http\Controllers\Api\V1\ExamSystem;

use App\Http\Controllers\Api\AuthController;
use App\Imports\ExamSystem\QuestionImport;
use App\Models\ExamSystem\Category;
use App\Models\ExamSystem\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class QuestionController extends AuthController
{
    protected $model = 'App\Models\ExamSystem\Question';
    function __construct()
    {
        parent::__construct();
    }

    // 列表
    public function index(Request $request)
    {
        $condition = $this->queryCondition($request, [
            ['content', 'like', '%T%'],
        ]);
        $sortRaw = $this->querySort($request, ['id desc']);
        $model = $this->queryListStatus($request);
        $count = $model->where($condition)->count();
        $limit = $this->queryPagination($request, $count);
        $items = $model->with([
            'category' => function ($query) {
               $query->select('id', 'name');
            }
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
        $data = Question::find($condition['id']);

        return $this->responseWithJson($data);
    }

    // 更新
    public function update(Request $request)
    {
        $condition = $request->validate(['id' => 'required|integer|min:1']);
        $data = $request->only([
            'category_id', // 工种类别
            'type', // 试题分类ID
            'content', // 试题内容
            'answer_options', // 答题选项内容
            'answer_true_option', // 正确选项标识
            'analyze', // 试题解析
        ]);
        $data['answer_options'] = json_encode($data['answer_options']);
        $data['answer_true_option'] = json_encode($data['answer_true_option']);
        $result = Question::where($condition)->update($data);
        $result_flag = $result === 1 ? OP_SUCCESS : OP_FAILURE;
        return $this->responseWithJson($result, __('common.update'.$result_flag), $result_flag);
    }

    // 添加
    public function create(Request $request)
    {
        $data = $request->only([
            'category_id', // 工种类别
            'type', // 试题分类ID
            'content', // 试题内容
            'answer_options', // 答题选项内容
            'answer_true_option', // 正确选项标识
            'analyze', // 试题解析
        ]);
        $data['admin_id'] = $this->user->id;
        $data['answer_options'] = json_encode($data['answer_options']);
        $data['answer_true_option'] = json_encode($data['answer_true_option']);
        $result = Question::create($data);
        $result_flag = $result && $result['id'] > 0 ? OP_SUCCESS : OP_FAILURE;
        return $this->responseWithJson($result, __('common.create'.$result_flag), $result_flag);
    }

    /**
     * 导入
     * @param Request $request
     */
    public function import(Request $request)
    {
        $import = new QuestionImport($this->user->id);
        $file = $request->file('file');
        $result = Excel::import($import, $file);
        $importCount = $import->getImportCount();
        $result_flag = $result ? OP_SUCCESS : OP_FAILURE;
        return $this->responseWithJson(['importCount' => $importCount], __('common.import'.$result_flag), $result_flag);
    }

    /**
     * 试题类型数量统计
     * @param Request $request
     */
    public function typeAmountStat(Request $request)
    {
        $condition = $request->validate([
            'category_id' => [
                'required',
                'integer',
                'min:1',
                'exists:App\Models\ExamSystem\Category,id'
            ],
        ]);
        $items = Question::withoutTrashed()
            ->select('type', DB::raw('count(*) as total'))
            ->where($condition)
            ->groupBy('type')
            ->get();
        return $this->responseWithJson([
            'items' => $items,
        ]);
    }
}
