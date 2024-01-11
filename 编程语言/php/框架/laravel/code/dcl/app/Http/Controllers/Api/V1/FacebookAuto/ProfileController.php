<?php

namespace App\Http\Controllers\Api\V1\FacebookAuto;

use App\Http\Controllers\Api\AuthController;
use App\Http\Requests\Api\V1\ExamSystem\EnterpriseCreateFormRequest;
use App\Http\Requests\Api\V1\ExamSystem\EnterpriseUpdateFormRequest;
use App\Http\Requests\Api\V1\FacebookAuto\ProfileCreateFormRequest;
use App\Models\ExamSystem\Category;
use App\Models\ExamSystem\Enterprise;
use App\Models\FacebookAuto\ProfileModel;
use Illuminate\Http\Request;

class ProfileController extends AuthController
{
    protected $model = 'App\Models\FacebookAuto\Profile';

    function __construct()
    {
        parent::__construct();
    }

    // 列表
//    public function index(Request $request)
//    {
//        $condition = $this->queryCondition($request, [['name', 'like', '%T%']]);
//        $sortRaw = $this->querySort($request, ['id desc']);
//        $model = $this->queryListStatus($request);
//        $count = $model->with([
//            'Category' => function ($query) {
//                $query->select('id', 'name');
//            },
//            'legal_representative'
//        ])->where($condition)->count();
//        $limit = $this->queryPagination($request, $count);
//        $items = $model->where($condition)
//            ->skip($limit['start'])
//            ->take($limit['size'])
//            ->orderByRaw($sortRaw)
//            ->get();
//        Category::ancestorsFormatter($items);
//        return $this->responseWithJson([
//            'total' => $count,
//            'items' => $items,
//            'size' => $limit['size'],
//        ]);
//    }
//
//    // 详情
//    public function detail(Request $request)
//    {
//        $condition = $request->validate(['id' => 'required|integer|min:1']);
//        $data = Enterprise::find($condition['id']);
//
//        return $this->responseWithJson($data);
//    }
//
//    // 更新
//    public function update(EnterpriseUpdateFormRequest $request)
//    {
//        $data = $request->validated();
//        Enterprise::formatter($data);
//
//        $result = Enterprise::where(['id' => $data['id']])->update($data);
//        $result_flag = $result === 1 ? OP_SUCCESS : OP_FAILURE;
//        return $this->responseWithJson($result, __('common.update' . $result_flag), $result_flag);
//    }

    // 添加
    public function create(ProfileCreateFormRequest $request)
    {
        $data = $request->validated();

        $result = ProfileModel::create($data);
        $result_flag = $result && $result['id'] > 0 ? OP_SUCCESS : OP_FAILURE;
        return $this->responseWithJson($result, __('common.create' . $result_flag), $result_flag);
    }
}
