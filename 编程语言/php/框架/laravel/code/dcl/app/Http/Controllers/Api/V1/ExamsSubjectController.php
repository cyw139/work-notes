<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\AuthController;
use App\Http\Requests\Api\V1\ExamsSubjectFormRequest;
use App\Models\Kspx\ExamsSubject;
use Illuminate\Http\Request;

class ExamsSubjectController extends AuthController
{
    protected $model = 'App\Models\Kspx\ExamsSubject';

    function __construct()
    {
        parent::__construct();
    }

    // 列表
    public function index(Request $request)
    {
        $condition = $this->queryCondition($request, [['title', 'like', '%T%']]);
        $sortRaw = $this->querySort($request, ['exams_subject_id desc']);
        $model = $this->queryListStatus($request);
        $count = $model->where($condition)->count();
        $limit = $this->queryPagination($request, $count);
        $items = ExamsSubject::with(['ExamsModule' => function ($query){
            $query->select('exams_module_id', 'title');
        }])
            ->where($condition)
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

    // 树
    public function tree(Request $request)
    {
        $condition = [];
        $tree = ExamsSubject::with(['ExamsModule' => function($query) {
            $query->select('exams_module_id', 'title');
        }])
            ->where($condition)
            ->withDepth()
            ->orderByRaw('_lft asc')
            ->get()
            ->toTree();
        return $this->responseWithJson($this->treeFormatter($tree));
    }

    // 子树
    public function subtree(Request $request)
    {
        // 获取父节点深度
        $pid = $request->input('pid', null);
        $parent_depth = ExamsSubject::with(['ExamsModule' => function($query) {
            $query->select('exams_module_id', 'title');
        }])->nodeDepth($pid);
        // 展示到的深度
        $depth = $request->input('depth', 0);
        // 是否带上父节点数据
        $with_self = $request->input('with_self', 0);
        if ($with_self == 1) {
            if ($depth < $parent_depth) {
                return $this->responseWithJson([]);
            }
            $model = ExamsSubject::withDepth()->having('depth', '<=', $depth)->descendantsAndSelf($pid);
        } else {
            if ($depth <= $parent_depth) {
                return $this->responseWithJson([]);
            }
            $model = ExamsSubject::withDepth()->having('depth', '<=', $depth)->descendantsOf($pid);
        }
        $tree = $model->toTree();

        return $this->responseWithJson($this->treeFormatter($tree));
    }
    // 直接孩子
    public function children(Request $request)
    {
        $pid = $request->input('pid', 0);
        $model = $this->queryListStatus($request);
        $model->orderByRaw('_lft asc');
        // 获取父节点深度
        if ($pid === null || $pid == 0) {
            $model = $model->with(['ExamsModule' => function($query) {
                $query->select('exams_module_id', 'title');
            }])->withDepth()->having('depth', '=', 0)->get();
        } else {
            $parent_depth = ExamsSubject::nodeDepth($pid);
            $children_depth = $parent_depth + 1;
            // 是否带上父节点数据
            $with_self = $request->input('with_self', 0);
            if ($with_self == 1) {
                $model = $model->withDepth()
                    ->having('depth', '=', $children_depth)
                    ->descendantsAndSelf($pid);
            } else {
                $model = $model->withDepth()
                    ->having('depth', '=', $children_depth)
                    ->descendantsOf($pid);
            }
        }

        $tree = $model->map(function($item) {
            $item->hasChildren = ($item->_rgt - $item->_lft) > 1 ? 1 : 0;
            return $item;
        });

        return $this->responseWithJson($tree);
    }

    // 详情
    public function detail(Request $request)
    {
        $condition = $request->validate(['id' => 'required|integer|min:1']);
        $data = ExamsSubject::with([
            'ExamsModule' => function ($query) {
                $query->select('exams_module_id', 'title');
            },
        ])->find($condition['id']);
        return $this->responseWithJson($data, );
    }

    // 更新
    public function update(ExamsSubjectFormRequest $request)
    {
        $condition = $request->validate([
            'exams_subject_id' => [
                'required',
                'integer',
                'min:1',
                'exists:App\Models\Kspx\ExamsSubject,exams_subject_id'
            ],
        ]);
        $data = $request->only([
            'pid', // 父ID
            'title', // 考试分类标题
            'exams_module_id', // 关联模板ID
            'sibling', // 兄弟节点
            'position' // 位置
        ]);
        // 数据变更
        $exams_subject_id = $condition['exams_subject_id'];
        $current_node = ExamsSubject::find($exams_subject_id);
        $current_node->pid = $data['pid'];
        $current_node->title = $data['title'];
        $current_node->exams_module_id = $data['exams_module_id'];
        // 关系设置
        $parent_node = isset($data['pid']) ? ExamsSubject::find($data['pid']) : null;
        $sibling_node = isset($data['sibling']) ? ExamsSubject::find($data['sibling']) : null;
        if ($current_node->pid != $data['pid']) {
            if (!$parent_node) {
                $current_node->saveAsRoot();
            } else {
                $current_node->parent()->associate($parent_node)->save();
            }
        }

        // 插入位置操作
        $current_node->parent()->associate($parent_node)->save();
        // 插入位置
        if ($sibling_node && ExamsSubject::isChild($sibling_node, $parent_node)) {
            if ($data['position'] === 'before') {
                $current_node->insertBeforeNode($sibling_node);
            } else {
                $current_node->insertAfterNode($sibling_node);
            }
        }
        $result_flag = $current_node ? OP_SUCCESS : OP_FAILURE;
        return $this->responseWithJson($current_node, __('common.update'.$result_flag), $result_flag);
    }

    // 添加
    public function create(ExamsSubjectFormRequest $request)
    {
        $data = $request->only([
            'pid', // 父ID
            'title', // 考试分类标题
            'exams_module_id', // 关联模板ID
            'sibling', // 兄弟节点
            'position' // 位置
        ]);
        $parent_node = isset($data['pid']) ? ExamsSubject::find($data['pid']) : null;
        $sibling_node = isset($data['sibling']) ? ExamsSubject::find($data['sibling']) : null;
        // 父节点
        $result = ExamsSubject::create($data, $parent_node);
        // 插入位置
        if ($sibling_node && ExamsSubject::isChild($sibling_node, $parent_node)) {
            if ($data['position'] === 'before') {
                $result->insertBeforeNode($sibling_node);
            } else {
                $result->insertAfterNode($sibling_node);
            }
        }
        $result_flag = $result && $result['exams_subject_id'] > 0 ? OP_SUCCESS : OP_FAILURE;
        return $this->responseWithJson($result, __('common.create'.$result_flag), $result_flag);
    }
}
