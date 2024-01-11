<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use Helpers;

    // 批量删除规则
    protected $idsRule = [
        'ids' => [
            'required',
            'array',
        ],
        'ids.*' => [
            'required',
            'integer',
            'min:1'
        ]
    ];

    public function __construct()
    {
        App::setLocale('zh_cn');
        app()->configPath("constant");
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function login()
    {
        $credentials = request(['email', 'password']);
        if (!$token = auth('api')->attempt($credentials)) {
            return $this->responseWithJson([], __('auth.unauthorized'), 401);
        }

        return $this->respondWithToken($token);
    }

    public function me()
    {
//        return response()->json(auth()->user());
        $user = auth('api')->user()->toArray();
        return $this->responseWithJson($user, __('user.info.get_success'));
//        return $this->user();
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token)
    {
        return $this->responseWithJson([
            'token' => $token
        ], __('auth.login_success'));
    }

    /**
     * @param mixed $data
     * @param string $msg
     * @param int $code
     * @return string
     */
    protected function responseWithJson($data = [], string $msg = '', int $code = OP_SUCCESS)
    {
        if ($data instanceof Collection) {
            $data = $data->count() === 0 ? [] : $data->toArray();
        }

        return $this->response->array([
            'data' => $data,
            'code' => $code,
            'msg' => $msg
        ]);
    }

    /**
     * 分页查询
     * @param Request $request 请求
     * @param int $totalRecords 总记录
     * @param string[] $queryKeys 查询条件
     * @return array
     */
    protected function queryPagination(Request $request, int $totalRecords = 0, array $queryKeys = ['page', 'size']): array
    {
        $limit = $request->only($queryKeys);
        $size = isset($limit['size']) ? intval($limit['size']) : 10;
        $totalPages = ceil($totalRecords / $size);
        $page = isset($limit['page']) ? intval($limit['page']) : 1;
        $page = $page <= 0 ? 1 : ($page >= $totalPages ? $totalPages : $page);
        $start = ($page - 1) * $size;
        return [
            'start' => $start,
            'size' => $size
        ];
    }

    /**
     * 获取查询条件
     * @param Request $request 请求对象
     * @param array $keys 查询条件key
     * @return array
     */
    protected function queryCondition(Request $request, $keys = []): array
    {
        $query = [];
        if (!is_array($keys) || count($keys) === 0) {
            return $query;
        }

        foreach ($keys as $index => $key) {
            if (is_string($key)) {
                $name = $key;
                if ($request->filled($name)) {
                    $query[] = [$name, '=', $request->input($name)];
                }
            } else if (is_array($key)) {
                $name = $key[0];
                $symbol = $key[1];
                $value = $key[2];
                if ($request->filled($name)) {
                    if ($symbol === 'like') {
                        $query[] = [$name, 'like', str_replace('T', $request->input($name), $value)];
                    } else {
                        $query[] = [$name, 'like', $value];
                    }
                }
            }
        }

        return $query;
    }

    /**
     * 查询排序
     * 使用：-name,-id
     * @param Request $request
     * @return string
     */
    protected function querySort(Request $request, $defaultOrder = []): string
    {
        if (!$request->filled('sort')) {
            return implode(',', $defaultOrder);
        }

        $sorts = explode(',', $request->input('sort'));
        $order = [];

        foreach ($sorts as $sort) {
            if (preg_match('/^[+-]/', $sort)) {
                $symbol = str_split($sort, 1);
                $order[] = $sort . ' ' . ($symbol === '-' ? 'desc' : 'asc');
            } else {
                $order[] = $sort . ' asc';
            }
        }

        return implode(',', $order);
    }

    // 获取记录集合
    protected function queryListStatus(Request $request)
    {
        $status = intval($request->input('list_status'));
        if ($status === RS_ALL) {
            return $this->model::withTrashed();
//            return $this->model::withoutTrashed();
        } else if ($status === RS_REMOVE) {
            return $this->model::onlyTrashed();
        } else {
            return $this->model::withoutTrashed();
        }

    }

    /**
     * 批量软删除
     * @param Request $request
     */
    public function remove(Request $request)
    {
        $condition = $request->validate($this->idsRule);
        $ids = $condition['ids'];
        $result = $this->model::destroy($ids);
        $result_flag = $result > 0 ? OP_SUCCESS : OP_FAILURE;
        return $this->responseWithJson($result, __('common.remove' . $result_flag), $result_flag);
    }

    /**
     * 批量恢复
     * @param Request $request
     */
    protected function recover(Request $request)
    {
        $condition = $request->validate($this->idsRule);
        $ids = $condition['ids'];
        $result = $this->model::onlyTrashed()->find($ids)->map(function ($item) {
            return $item->restore() ? $item->{$item->getPrimaryKey()} : 0;
        })->filter(function ($item) {
            return $item > 0;
        })->count();
        $result_flag = $result > 0 ? OP_SUCCESS : OP_FAILURE;
        return $this->responseWithJson($result, __('common.recover' . $result_flag), $result_flag);
    }

    /**
     * 批量永久删除
     * @param Request $request
     * @param Model $model
     * @return string
     */
    protected function forceRemove(Request $request)
    {
        $condition = $request->validate($this->idsRule);
        $ids = $condition['ids'];
        $result = $this->model::onlyTrashed()->find($ids)->map(function ($item) {
            return $item->forceDelete() ? $item->{$item->getPrimaryKey()} : 0;
        })->filter(function ($item) {
            return $item > 0;
        })->count();
        $result_flag = $result > 0 ? OP_SUCCESS : OP_FAILURE;
        return $this->responseWithJson($result, __('common.delete' . $result_flag), $result_flag);
    }

    protected function treeFormatter(Collection $data)
    {
        if ($data->count() === 0) {
            return $data;
        }
        return $data->map(function($item) {
            if (($item->_rgt - $item->_lft) === 1) {
                unset($item->children);
                $item->hasChildren = 0;
            } else {
                $item->hasChildren = 1;
                $item->children = $this->treeFormatter($item->children);
            }
            return $item;
        });
    }
}
