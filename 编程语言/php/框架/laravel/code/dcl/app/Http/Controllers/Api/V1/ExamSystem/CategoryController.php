<?php

namespace App\Http\Controllers\Api\V1\ExamSystem;

use App\Http\Controllers\Api\AuthController;
use App\Http\Requests\Api\V1\ExamSystem\CategoryFormRequest;
use App\Imports\ExamSystem\CategoryImport;
use App\Models\ExamSystem\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class CategoryController extends AuthController
{
    protected $model = 'App\Models\ExamSystem\Category';

    function __construct()
    {
        parent::__construct();
    }

    // 列表
    public function index(Request $request)
    {
        $condition = $this->queryCondition($request, [
            ['name', 'like', '%T%'],
            'type',
        ]);
        $sortRaw = $this->querySort($request, ['id desc']);
        $model = $this->queryListStatus($request);
        $count = $model->where($condition)->count();
        $limit = $this->queryPagination($request, $count);
        $items = Category::where($condition)
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
        $condition = $this->queryCondition($request, [
            'type',
        ]);
        $model = $this->queryListStatus($request);
        $tree = $model->where($condition)
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
        $parent_depth = Category::nodeDepth($pid);
        // 展示到的深度
        $depth = $request->input('depth', 0);
        // 是否带上父节点数据
        $with_self = $request->input('with_self', 0);
        if ($with_self == 1) {
            if ($depth < $parent_depth) {
                return $this->responseWithJson([]);
            }
            $model = Category::withDepth()->having('depth', '<=', $depth)->descendantsAndSelf($pid);
        } else {
            if ($depth <= $parent_depth) {
                return $this->responseWithJson([]);
            }
            $model = Category::withDepth()->having('depth', '<=', $depth)->descendantsOf($pid);
        }
        $tree = $model->toTree();

        return $this->responseWithJson($this->treeFormatter($tree));
    }
    // 直接孩子
    public function children(Request $request)
    {
        $pid = $request->input('pid', 0);
        $condition = $this->queryCondition($request, [
            'type',
        ]);
        $model = $this->queryListStatus($request);
        $model->where($condition);
        $model->orderByRaw('_lft asc');
        // 获取父节点深度
        if ($pid === null || $pid == 0) {
            $model = $model->withDepth()->having('depth', '=', 0)->get();
        } else {
            $parent_depth = Category::nodeDepth($pid);
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
        $data = Category::find($condition['id']);
        return $this->responseWithJson($data, );
    }

    // 更新
    public function update(CategoryFormRequest $request)
    {
        $condition = $request->validate([
            'id' => [
                'required',
                'integer',
                'min:1',
                'exists:App\Models\ExamSystem\Category,id'
            ],
        ]);
        $data = $request->only([
            'pid', // 父ID
            'name', // 类别名称
            'type', // 类别种类：企业类别、工种类别
            'sibling', // 兄弟节点
            'position' // 位置
        ]);
        // 数据变更
        $id = $condition['id'];
        $current_node = Category::find($id);
//        $current_node->pid = $data['pid'] ?? null;
        $current_node->name = $data['name'];
        $current_node->type = $data['type'];
        // $current_node->id = $id;
        // 关系设置
        $parent_node = isset($data['pid']) ? Category::find($data['pid']) : null;
        $sibling_node = isset($data['sibling']) ? Category::find($data['sibling']) : null;

        // 插入位置操作
        $current_node->parent()->associate($parent_node)->save();
        // 插入位置
        if ($sibling_node && Category::isChild($sibling_node, $parent_node)) {
            if ($data['position'] === 'before') {
                $current_node->insertBeforeNode($sibling_node);
            } else {
                $current_node->insertAfterNode($sibling_node);
            }
        }
        Category::fixTree();
        $result_flag = $current_node ? OP_SUCCESS : OP_FAILURE;
        return $this->responseWithJson($current_node, __('common.update'.$result_flag), $result_flag);
    }

    // 添加
    public function create(CategoryFormRequest $request)
    {
        $data = $request->only([
            'pid', // 父ID
            'name', // 类别名称
            'type', // 类别种类：企业类别、工种类别
            'sibling', // 兄弟节点
            'position' // 位置
        ]);
        $data['admin_id'] = $this->user->id;
        $parent_node = isset($data['pid']) ? Category::find($data['pid']) : null;
        $sibling_node = isset($data['sibling']) ? Category::find($data['sibling']) : null;

        // 父节点
        $result = Category::create($data, $parent_node);
        // 插入位置
        if ($sibling_node && Category::isChild($sibling_node, $parent_node)) {
            if ($data['position'] === 'before') {
                $result->insertBeforeNode($sibling_node);
            } else {
                $result->insertAfterNode($sibling_node);
            }
        }
        Category::fixTree();
        $result_flag = $result && $result['id'] > 0 ? OP_SUCCESS : OP_FAILURE;
        return $this->responseWithJson($result, __('common.create'.$result_flag), $result_flag);
    }

    /**
     * 导入
     * @param Request $request
     */
    public function import(Request $request)
    {
        $condition = $request->validate([
            'type' => [
                Rule::in(array_keys(Category::TYPES))
            ],
        ]);
        $import = new CategoryImport($this->user->id, $condition['type']);
        $file = $request->file('file');
        Excel::import($import, $file);
        $importRows = $import->getImportRows();
        $result_flag = $importRows ? OP_SUCCESS : OP_FAILURE;
        return $this->responseWithJson($importRows, __('common.import'.$result_flag), $result_flag);
    }

    /**
     * 某父节点下的直接子节点名称是否唯一
     * @param Request $request
     */
    public function nameUnique(Request $request) {
        $condition = $request->validate([
            'type' => [
                'required',
                'integer',
                Rule::in(array_keys(Category::TYPES)),
            ],
            'pid' => [
                'nullable',
                'min: 1',
                'exists:App\Models\ExamSystem\Category,id',
            ],
            'name' => [
                'required',
                'string',
                'max: 100',
            ],
        ]);
        if (!isset($condition['pid'])) {
            $condition['pid'] = null;
        }
//        dd($condition);
        $result = Category::withoutTrashed()->firstWhere($condition);
        $result_flag = $result ? EXISTS : NOT_EXISTS;
        return $this->responseWithJson([
            'code' => $result_flag,
            'node' => $result
        ], __('common.category_name'.$result_flag));

    }
}
