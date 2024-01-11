<?php

namespace App\Http\Controllers\Api\V1\ExamSystem;

use App\Http\Controllers\Api\AuthController;
use App\Http\Requests\Api\V1\ExamSystem\UserEnterprisePositionCreateFormRequest;
use App\Http\Requests\Api\V1\ExamSystem\UserEnterprisePositionUpdateFormRequest;
use App\Models\ExamSystem\Category;
use App\Models\ExamSystem\UserEnterprisePosition;
use App\Models\ExamSystem\UserIdentity;
use Illuminate\Http\Request;

class UserEnterprisePositionController extends AuthController
{
    protected $model = 'App\Models\ExamSystem\UserEnterprisePosition';

    function __construct()
    {
        parent::__construct();
    }

    // 列表
    public function index(Request $request)
    {
        $condition = $this->queryCondition($request, ['user_id']);
        $sortRaw = $this->querySort($request, ['id desc']);
        $model = $this->queryListStatus($request);
        $count = $model->where($condition)->count();
        $limit = $this->queryPagination($request, $count);
        $items = $model
            ->with([
                'user',
                'enterprise:id,category_id,name',
                'enterprise.category'
            ])
            ->where($condition)
            ->skip($limit['start'])
            ->take($limit['size'])
            ->orderByRaw($sortRaw)
            ->get();
        Category::ancestorsFormatter($items, 'enterprise');
        return $this->responseWithJson([
            'total' => $count,
            'items' => $items,
            'size' => $limit['size'],
        ]);
    }

    // 详情
    public function detail(Request $request)
    {
        $condition = $request->validate(['id' => 'required|integer|min:1|exists:App\Models\ExamSystem\UserEnterprisePosition,id']);
        $data = UserEnterprisePosition::with([
            'user',
        ])->find($condition['id']);

        return $this->responseWithJson($data);
    }

    // 更新
    public function update(UserEnterprisePositionUpdateFormRequest $request)
    {
        $data = $request->validated();
        UserIdentity::formatter($data);
        $data['admin_id'] = $this->user->id;
        $result = UserEnterprisePosition::where(['id' => $data['id']])->update($data);
        $result_flag = $result === 1 ? OP_SUCCESS : OP_FAILURE;
        return $this->responseWithJson($result, __('common.update'.$result_flag), $result_flag);
    }

    // 添加
    public function create(UserEnterprisePositionCreateFormRequest $request)
    {
        $data = $request->validated();
        UserIdentity::formatter($data);
        $data['admin_id'] = $this->user->id;
        $result = UserEnterprisePosition::create($data);
        $result_flag = $result ? OP_SUCCESS : OP_FAILURE;
        return $this->responseWithJson($result, __('common.create'.$result_flag), $result_flag);
    }
}
