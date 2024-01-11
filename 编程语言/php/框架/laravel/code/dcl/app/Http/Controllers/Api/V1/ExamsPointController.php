<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\AuthController;
use App\Http\Requests\Api\V1\ExamsPointFormRequest;
use App\Models\Kspx\ExamsPoint;
use Illuminate\Http\Request;

class ExamsPointController extends AuthController
{
    function __construct()
    {
        parent::__construct();
    }

    // 列表
    public function index(Request $request)
    {
        $condition = $this->queryCondition($request, [['title', 'like', '%T%'], 'exams_subject_id']);
        $sortRaw = $this->querySort($request, ['exams_point_id desc']);
        $count = ExamsPoint::where($condition)->count();
        $limit = $this->queryPagination($request, $count);
        $items = ExamsPoint::with(['ExamsSubject' => function ($query){
            $query->select();
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

    // 全部
    public function allBySubjectId(Request $request)
    {
        if ($condition = $request->validate([
            'exams_subject_id' => 'required|integer|min:1'
        ])) {
            $items = ExamsPoint::where($condition)->get();
            return $this->responseWithJson($items);
        }
    }

    // 详情
    public function detail(Request $request)
    {
        if (!$request->filled('exams_point_id')) {
            return $this->responseWithJson([], __('exams_point.id_not_exists'));
        }
        $data = ExamsPoint::find($request->input('exams_point_id'));

        return $this->responseWithJson($data);
    }

    // 更新
    public function update(ExamsPointFormRequest $request)
    {
        if ($request->filled('exams_point_id')) {
            $condition = $this->queryCondition($request, ['exams_point_id']);
            $data = $request->only([
                'title',
                'exams_subject_id',
            ]);
            $result = ExamsPoint::where($condition)->update($data);
            $msg = $result === 1 ? '' : __('common.update_fail');
            return $this->responseWithJson($result, $msg);
        }
    }

    // 添加
    public function create(ExamsPointFormRequest $request)
    {
        $data = $request->only([
            'title',
            'exams_subject_id',
        ]);
        $result = ExamsPoint::create($data);
        $msg = $result === 1 ? '' : __('common.insert_fail');
        return $this->responseWithJson($result, $msg);
    }

    /**
     * 批量软删除
     * @param Request $request
     */
    public function remove(Request $request)
    {
        $ids = $request->input('exams_point_id');
        $result = ExamsPoint::destroy($ids);
        $msgKey = $result === 1 ? 'common.remove_success' : 'common.remove_failure';
        return $this->responseWithJson($result, __($msgKey));
    }

    /**
     * 永久删除
     * @param Request $request
     */
    public function forceRemove(Request $request) {
        $ids = $request->input('exams_point_id');
        $result = ExamsPoint::find($ids)->forceDelete();
        $msgKey = $result === 1 ? 'common.delete_success' : 'common.delete_failure';
        return $this->responseWithJson($result, __($msgKey));
    }
}
