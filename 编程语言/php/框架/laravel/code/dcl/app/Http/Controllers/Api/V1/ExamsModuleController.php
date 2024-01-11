<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\AuthController;
use App\Http\Requests\Api\V1\ExamsModuleFormRequest;
use App\Models\Kspx\ExamsModule;
use Illuminate\Http\Request;

class ExamsModuleController extends AuthController
{
    function __construct()
    {
        parent::__construct();
    }

    // 列表
    public function index(Request $request)
    {
        $condition = $this->queryCondition($request, [['title', 'like', '%T%']]);
        $sortRaw = $this->querySort($request, ['exams_module_id desc']);
        $count = ExamsModule::where($condition)->count();
        $limit = $this->queryPagination($request, $count);
        $items = ExamsModule::where($condition)
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
    public function all()
    {
        $count = ExamsModule::count();
        $items = ExamsModule::get();
        return $this->responseWithJson([
            'total' => $count,
            'items' => $items,
        ]);
    }

    // 详情
    public function detail(Request $request)
    {
        if (!$request->filled('id')) {
            return $this->responseWithJson([], __('exams_module.id_not_exists'));
        }
        $data = ExamsModule::find($request->input('id'));

        return $this->responseWithJson($data);
    }

    // 更新
    public function update(Request $request)
    {
        if ($request->filled('exams_module_id')) {
            $condition = $this->queryCondition($request, ['exams_module_id']);
            $data = $request->only([
                'title',
                'icon',
                'description',
                'btn_text',
                'is_practice',
                'sort',
                'display',
            ]);
            $result = ExamsModule::where($condition)->update($data);
            $msg = $result === 1 ? '' : __('common.update_fail');
            return $this->responseWithJson($result, $msg);
        }
    }

    // 添加
    public function create(Request $request)
    {
        $data = $request->only([
            'title',
            'icon',
            'description',
            'btn_text',
            'is_practice',
            'sort',
            'display',
        ]);
        $result = ExamsModule::create($data);
        $msg = $result === 1 ? '' : __('common.insert_fail');
        return $this->responseWithJson($result, $msg);
    }

    /**
     * 批量软删除
     * @param Request $request
     */
    public function remove(Request $request)
    {
        $ids = $request->input('exams_module_id');
        $result = ExamsModule::destroy($ids);
        $msgKey = $result === 1 ? 'common.remove_success' : 'common.remove_failure';
        return $this->responseWithJson($result, __($msgKey));
    }

    /**
     * 永久删除
     * @param Request $request
     */
    public function forceRemove(Request $request) {
        $ids = $request->input('ids');
        $result = ExamsModule::find($ids)->forceDelete();
        $msgKey = $result === 1 ? 'common.delete_success' : 'common.delete_failure';
        return $this->responseWithJson($result, __($msgKey));
    }
}
