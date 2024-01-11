<?php

namespace App\Http\Controllers\Api\V1\ExamSystem;

use App\Http\Controllers\Api\AuthController;
use App\Models\ExamSystem\User;
use App\Models\ExamSystem\UserIdentity;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserIdentityController extends AuthController
{
    protected $model = 'App\Models\ExamSystem\UserIdentity';

    function __construct()
    {
        parent::__construct();
    }

    // 列表 √
    public function index(Request $request)
    {
        $condition = $this->queryCondition($request, [['true_name', 'like', '%T%']]);
        $sortRaw = $this->querySort($request, ['id desc']);
        $model = $this->queryListStatus($request);
        $count = $model->where($condition)->count();
        $limit = $this->queryPagination($request, $count);
        $items = $model
//            ->with([
//                'userIdentity',
//                'userContact',
//            ])
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

    // 详情 √
    public function detail(Request $request)
    {
        $condition = $request->validate(['id' => 'required|integer|min:1']);
        $data = UserIdentity::with([
            'userIdentity',
            'userContact',
        ])->find($condition['id']);

        return $this->responseWithJson($data);
    }

    // 更新
    public function update(Request $request)
    {
        $condition = $request->validate(['id' => 'required|integer|min:1']);
        $data = $request->only([
            'true_name',
            'sex',
            'mobile',
            'password',
            'nationality',
            'id_card',
            'id_card_address',
            'id_card_front',
            'id_card_back',
            'photo',
        ]);
        $this->resourceFormatter($data);

        $result = UserIdentity::where($condition)->update($data);
        $result_flag = $result === 1 ? OP_SUCCESS : OP_FAILURE;
        return $this->responseWithJson($result, __('common.update'.$result_flag), $result_flag);
    }

    // 添加
    public function change(Request $request)
    {
        $condition = $request->validate(['user_id' => 'required|integer|min:1']);
        // 身份证信息
        $user_identity = $request->only([
            'true_name',
            'sex',
            'nationality',
            'id_card_address',
            'id_card_front',
            'id_card_back',
            'photo',
            'issuing_authority',
            'valid_period',
        ]);
        $user = $request->only([
            'true_name',
            'sex',
            'nationality',
            'id_card_address',
            'id_card_front',
            'id_card_back',
            'photo',
            'issuing_authority',
            'valid_period',
        ]);
        // 用户不存在
        if (!User::isExists($condition['user_id'])) {
            return $this->responseWithJson([], __('common.user'. NOT_EXISTS), NOT_EXISTS);
        }
//        $this->formatter($user_identity);
        UserIdentity::formatter($user_identity);
        $user_identity['user_id'] = $condition['user_id'];
        $user_identity['admin_id'] = $this->user->id;

        DB::connection('mysql_exam_system')->beginTransaction();
        try {
            // 更新用户表
            UserIdentity::formatter($user);
            $result_user = User::where(['id' => $condition['user_id']])->update($user);
            if (!$result_user) {
                DB::connection('mysql_exam_system')->rollBack();
                return $this->responseWithJson($result_user, __('common.user_create'.OP_FAILURE), OP_FAILURE);
            }
            // 更新用户证件表
            $user_identity_rs = UserIdentity::withoutTrashed()->where(['user_id' => $condition['user_id']])->select('id')->get();
            $ids = $user_identity_rs->pluck('id')->all();
            if ($ids) {
                $result = UserIdentity::destroy($ids);
                if (!$result) {
                    DB::connection('mysql_exam_system')->rollBack();
                    return $this->responseWithJson($result, __('common.user_identity_remove'.OP_FAILURE), OP_FAILURE);
                }
            }
            $result_user_identity = UserIdentity::create($user_identity);
            if (!$result_user_identity) {
                DB::connection('mysql_exam_system')->rollBack();
                return $this->responseWithJson($result_user_identity, __('common.user_identity_change'.OP_FAILURE), OP_FAILURE);
            }
            DB::connection('mysql_exam_system')->commit();
            return $this->responseWithJson($result_user_identity, __('common.user_identity_change'.OP_SUCCESS), OP_SUCCESS);
        } catch (QueryException $ex) {
            DB::connection('mysql_exam_system')->rollback();
            return $this->responseWithJson($ex->getMessage(), __('common.sql'.SQL_ERROR), SQL_ERROR);
        }
    }
}
