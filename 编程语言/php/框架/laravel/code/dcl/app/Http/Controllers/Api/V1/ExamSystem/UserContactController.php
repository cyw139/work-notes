<?php

namespace App\Http\Controllers\Api\V1\ExamSystem;

use App\Http\Controllers\Api\AuthController;
use App\Models\ExamSystem\User;
use App\Models\ExamSystem\UserContact;
use Illuminate\Http\Request;

class UserContactController extends AuthController
{
    protected $model = 'App\Models\ExamSystem\UserContact';

    function __construct()
    {
        parent::__construct();
    }

    // 列表
    public function index(Request $request)
    {
        $condition = $this->queryCondition($request, [['type', 'like', '%T%']]);
        $sortRaw = $this->querySort($request, ['id desc']);
        $model = $this->queryListStatus($request);
        $count = $model->where($condition)->count();
        $limit = $this->queryPagination($request, $count);
        $items = $model
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

    // 详情
    public function detail(Request $request)
    {
        $condition = $request->validate(['id' => 'required|integer|min:1']);
        $data = UserContact::find($condition['id']);

        return $this->responseWithJson($data);
    }

    // 添加手机 √
    public function createMobile(Request $request)
    {
        $data = $request->only([
            'user_id',
            'mobile',
        ]);
        // 用户是否存在
        $user = User::find($data['user_id']);
        if (!$user) {
            return $this->responseWithJson([], __('common.user'. NOT_EXISTS), NOT_EXISTS);
        }
        $condition = [
            'type' => 'mobile',
            'name' => $data['mobile'],
        ];
        // 手机号是否存在
        if ($mobile = UserContact::isExistsMobile($data['mobile'])) {
            return $this->responseWithJson([], __('common.mobile'. EXISTS), EXISTS);
        }
        $data['name'] = $data['mobile'];
        $data['type'] = 'mobile';
        $data['admin_id'] = $this->user->id;

        $result = UserContact::create($data);
        $result_flag = $result && $result['id'] > 0 ? OP_SUCCESS : OP_FAILURE;
        return $this->responseWithJson($result, __('common.mobile_create'.$result_flag), $result_flag);
    }
}
