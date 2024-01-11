<?php

namespace App\Http\Controllers\Api\V1\ExamSystem;

use App\Http\Controllers\Api\AuthController;
use App\Imports\ExamSystem\RegisterImport;
use App\Imports\ExamSystem\UserImport;
use App\Models\ExamSystem\User;
use App\Models\ExamSystem\UserContact;
use App\Models\ExamSystem\UserIdentity;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends AuthController
{
    protected $model = 'App\Models\ExamSystem\User';

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
            ->with([
                'userIdentity',
                'userContact',
            ])
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

    // 密码重置 √
    public function resetPassword(Request $request) {
        $condition = $request->validate(['id' => 'required|integer|min:1']);
        $user = User::find($condition['id']);
        if (!$user) {
            return $this->responseWithJson([], __('common.user'. NOT_EXISTS), NOT_EXISTS);
        }
        $data = [
            'password' =>  bcrypt(substr($user['id_card'], -6))
        ];
        $result = User::where($condition)->update($data);
        $result_flag = $result === 1 ? OP_SUCCESS : OP_FAILURE;
        return $this->responseWithJson($result, __('common.password_reset'.$result_flag), $result_flag);
    }

    // 详情 √
    public function detail(Request $request)
    {
        $condition = $request->validate(['id' => 'required|integer|min:1']);
        $data = User::with([
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

        $result = User::where($condition)->update($data);
        $result_flag = $result === 1 ? OP_SUCCESS : OP_FAILURE;
        return $this->responseWithJson($result, __('common.update'.$result_flag), $result_flag);
    }

    // 资源格式化
    private function resourceFormatter(&$data) {
        // 图片资源保存到正式路径，去掉tmp
        $keys = ['id_card_front', 'id_card_back', 'photo'];
        $client = Storage::disk('upyun_exam_system_picture');
        foreach ($keys as $key) {
            if (isset($data[$key]) && $data[$key]) {
                // 临时路径
                $isTempPath = strpos($data[$key], 'tmp/') !== false;
                if ($isTempPath && $client->has($data[$key])) {
                    $savePath = str_replace('tmp/', '' ,$data[$key]);
                    if ($client->has($savePath)) {
                        $data[$key] = $savePath;
                    } else {
                        if ($client->move($data[$key], $savePath)) {
                            $data[$key] = $savePath;
                        }
                    }
                    usleep(200000);
                }
            }
        }
        // 密码生成
        if(isset($data['password']) && $data['password']) {
            $data['password'] = bcrypt($data['password']);
        } else {
            if (isset($data['id_card']) && $data['id_card']) {
                $data['password'] = bcrypt(substr($data['id_card'], -6));
            }
        }
    }

    // 添加
    public function create(Request $request)
    {
        // 身份证已存在
        $user = $request->only([
            'id_card',
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
        if (User::isExistsIdCard($user['id_card'])) {
            return $this->responseWithJson([], __('common.user_id_card'. EXISTS), EXISTS);
        }
        // 手机号已存在
        $user_contact = $request->only([
            'mobile',
        ]);
        if (UserContact::isExistsMobile($user_contact['mobile'])) {
            return $this->responseWithJson([], __('common.mobile'. EXISTS), EXISTS);
        }
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
        UserIdentity::formatter($user_identity);
        $this->resourceFormatter($user);
        $user['admin_id'] = $this->user->id;

        DB::connection('mysql_exam_system')->beginTransaction();
        try {
            // 用户创建
            UserIdentity::formatter($user);
            $result_user = User::create($user);
            if (!$result_user) {
                DB::connection('mysql_exam_system')->rollBack();
                return $this->responseWithJson($result_user, __('common.user_create'.OP_FAILURE), OP_FAILURE);
            }
            // 身份证创建
            $user_identity['user_id'] = $result_user['id'];
            $user_identity['admin_id'] = $this->user->id;
            $result_user_identity = UserIdentity::create($user_identity);
            if (!$result_user_identity) {
                DB::connection('mysql_exam_system')->rollBack();
                return $this->responseWithJson($result_user, __('common.user_identity_create'.OP_FAILURE), OP_FAILURE);
            }
            // 手机创建
            $user_contact['user_id'] = $result_user['id'];
            $user_contact['type'] = 'mobile';
            $user_contact['name'] = $user_contact['mobile'];
            $user_contact['admin_id'] = $this->user->id;
            $result_user_contact = UserContact::create($user_contact);
            if (!$result_user_contact) {
                DB::connection('mysql_exam_system')->rollBack();
                return $this->responseWithJson($result_user, __('common.user_mobile_create'.OP_FAILURE), OP_FAILURE);
            }

            DB::connection('mysql_exam_system')->commit();
            $result_user['user_identity'] = $result_user_identity;
            $result_user['user_contact'] = $user_contact;
            return $this->responseWithJson($result_user, __('common.create'.OP_SUCCESS), OP_SUCCESS);
        } catch (QueryException $ex) {
            DB::connection('mysql_exam_system')->rollback();
            return $this->responseWithJson($ex->getMessage(), __('common.sql'.SQL_ERROR), SQL_ERROR);
        }
    }

    /**
     * 导入
     * @param Request $request
     */
    public function importPhotos(Request $request)
    {
        $ds = DIRECTORY_SEPARATOR;
        $file = $request->file('file');
        $path = storage_path('app') . $ds . 'user'. $ds . 'photos' . $ds . time();
        $result_unzip = $this->unzip($file, $path);
        $result_upload = false;
        if ($result_unzip) {
            $files = $this->getAllPhotoAndExcelFiles($path);
            $import = new UserImport($this->user->id);
            $file = $this->getExcelFile($files);
            if ($file) {
                $result = Excel::import($import, $file);
                if ($result) {
                    $result_upload = $this->uploadUpYun($files);
                }
            } else {
                $result_upload = $this->uploadUpYun($files);
            }

            $this->deldir($path);
        }

        $result_flag = $result_upload ? OP_SUCCESS : OP_FAILURE;
        return $this->responseWithJson($result_upload, __('common.import'.$result_flag), $result_flag);
    }

    private function getExcelFile($files) {
        if (!$files) {
            return '';
        }
        foreach ($files as $file) {
            if (preg_match('/\.xlsx$/', $file)) {
                return $file;
            }
        }
        return '';
    }

    private function deldir($dir) {
        //先删除目录下的文件：
        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if($file != "." && $file!="..") {
                $fullpath = $dir."/".$file;
                if(!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    $this->deldir($fullpath);
                }
            }
        }
        closedir($dh);

        //删除当前文件夹：
        if(rmdir($dir)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 格式：
     * 身份证正面（ID%3 === 1）：001_身份证.jpg
     * 身份证反面（ID%3 === 2）：002_身份证.jpg
     * 头像（ID%3 === 0）：003_身份证.jpg
     *
     * 例如：
     * 001_350423199105250522.jpg 身份证正面
     * 002_350423199105250522.jpg 身份证反面
     * 003_350423199105250522.jpg 头像
     * 006_350425198807222911.jpg 头像
     *
     * @param $path
     * @param $files
     * @return bool
     */
    private function uploadUpYun($files) {
        foreach ($files as $file) {
            $pathNames = explode(DIRECTORY_SEPARATOR , $file);
            $count = count($pathNames);
            if (!preg_match('/^\d{3}_/', $pathNames[$count-1])) {
                continue;
            }
            $data = explode('.', $pathNames[$count-1]);
            $ext = $data[count($data) - 1]; // 图片扩展名
            $fields = explode('_', $data[0]);
            $order = $fields[0]; // 序号
            $id_card = $fields[1]; // 身份证
            if (intval($order) % 3 === 1) {
                $field = 'id_card_front';
            } else if (intval($order) % 3 === 2) {
                $field = 'id_card_back';
            } else {
                $field = 'photo';
            }
            if ($id_card) {
                $user = User::firstWhere(['id_card' => $id_card]);
                if ($user) {
                    usleep(500000);
                    // 保存云盘
                    $path = $this->saveUpYun($file, $ext);
                    // 字段保存
                    if (false !== $path) {
                        $user->update([ $field => $path]);
                        $userIdentity = UserIdentity::withoutTrashed()->firstWhere(['user_id' => $user->id]);
                        $userIdentity->update([$field => $path]);
                    }
                }

            }
        }
        return true;
    }

    private function getAllPhotoAndExcelFiles($path) {
        if(!file_exists($path)) {
            return [];
        }
        $files = [];
        // 切换到当前目录
        chdir($path);
        $paths = glob('*');
        foreach($paths as $fileName) {
            $newPath = $path . DIRECTORY_SEPARATOR . $fileName;
            // 可递归目录
            if (is_dir($newPath) && substr($fileName, 0, 2) !== '__') {
                $files = array_merge($files, $this->getAllPhotoAndExcelFiles($newPath));
            } else if(is_file($newPath)) {
                if (preg_match('/^\d{3}_/', $fileName) || preg_match('/\.xlsx$/', $fileName)) {
                    $files[] = $newPath;
                }
            }

        }
        return $files;
    }

    private function saveUpYun($file, $ext) {
        $resource = fopen($file, 'r');
        $client = Storage::disk('upyun_exam_system_picture');
        $path = $this->getRelativePath($ext);
        $result = $client->put($path, $resource);
        return $result ? $path : $result;
    }

    private function getRelativePath($ext, $type = ''): string
    {
        $ds = DIRECTORY_SEPARATOR;
        $type = $type ? $ds . $type : '';
        $date = date("{$ds}y{$ds}m{$ds}d");
        return env('UPYUN_RELATIVE_PATH') . $type . $date . $ds . md5(microtime()) . '.' . $ext;
    }

    private function unzip($file, $path) {
        $zip = new \ZipArchive();
        $flag = $zip->open($file);
        if($flag !== true){
            echo "open error code: {$flag}\n";
            exit();
        }
        $result = $zip->extractTo($path);
        $zip->close();
        return $result;
    }
}
