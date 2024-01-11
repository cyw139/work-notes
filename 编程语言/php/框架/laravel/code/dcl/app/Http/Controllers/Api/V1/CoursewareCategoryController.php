<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\AuthController;
use App\Http\Requests\Api\V1\CoursewareCategoryFormRequest;
use App\Models\Kspx\CoursewareCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class CoursewareCategoryController extends AuthController
{
    protected $model = 'App\Models\Kspx\CoursewareCategory';

    function __construct()
    {
        parent::__construct();
    }

    // 列表
    public function index(Request $request)
    {
        $condition = $this->queryCondition($request, [['title', 'like', '%T%']]);
        $sortRaw = $this->querySort($request, ['zy_currency_category_id desc']);
        $model = $this->queryListStatus($request);
        $count = $model->where($condition)->count();
        $limit = $this->queryPagination($request, $count);
        $items = CoursewareCategory::where($condition)
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
        $tree = CoursewareCategory::where($condition)
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
        $parent_depth = CoursewareCategory::nodeDepth($pid);
        // 展示到的深度
        $depth = $request->input('depth', 0);
        // 是否带上父节点数据
        $with_self = $request->input('with_self', 0);
        if ($with_self == 1) {
            if ($depth < $parent_depth) {
                return $this->responseWithJson([]);
            }
            $model = CoursewareCategory::withDepth()->having('depth', '<=', $depth)->descendantsAndSelf($pid);
        } else {
            if ($depth <= $parent_depth) {
                return $this->responseWithJson([]);
            }
            $model = CoursewareCategory::withDepth()->having('depth', '<=', $depth)->descendantsOf($pid);
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
            $model = $model->withDepth()->having('depth', '=', 0)->get();
        } else {
            $parent_depth = CoursewareCategory::nodeDepth($pid);
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
        $data = CoursewareCategory::find($condition['id']);
        return $this->responseWithJson($data, );
    }

    // 更新
    public function update(CoursewareCategoryFormRequest $request)
    {
        $condition = $request->validate([
            'zy_currency_category_id' => [
                'required',
                'integer',
                'min:1',
                'exists:App\Models\Kspx\CoursewareCategory,zy_currency_category_id'
            ],
        ]);
        $data = $request->only([
            'pid', // 父ID
            'title', // 考试分类标题
            'sibling', // 兄弟节点
            'position' // 位置
        ]);
        // 数据变更
        $zy_currency_category_id = $condition['zy_currency_category_id'];
        $current_node = CoursewareCategory::find($zy_currency_category_id);
        $current_node->pid = $data['pid'];
        $current_node->title = $data['title'];
        // 关系设置
        $parent_node = isset($data['pid']) ? CoursewareCategory::find($data['pid']) : null;
        $sibling_node = isset($data['sibling']) ? CoursewareCategory::find($data['sibling']) : null;
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
        if ($sibling_node && CoursewareCategory::isChild($sibling_node, $parent_node)) {
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
    public function create(CoursewareCategoryFormRequest $request)
    {
        $data = $request->only([
            'pid', // 父ID
            'title', // 考试分类标题
            'sibling', // 兄弟节点
            'position' // 位置
        ]);
        $parent_node = isset($data['pid']) ? CoursewareCategory::find($data['pid']) : null;
        $sibling_node = isset($data['sibling']) ? CoursewareCategory::find($data['sibling']) : null;
        // 父节点
        $result = CoursewareCategory::create($data, $parent_node);
        // 插入位置
        if ($sibling_node && CoursewareCategory::isChild($sibling_node, $parent_node)) {
            if ($data['position'] === 'before') {
                $result->insertBeforeNode($sibling_node);
            } else {
                $result->insertAfterNode($sibling_node);
            }
        }
        $result_flag = $result && $result['zy_currency_category_id'] > 0 ? OP_SUCCESS : OP_FAILURE;
        return $this->responseWithJson($result, __('common.create'.$result_flag), $result_flag);
    }
}
