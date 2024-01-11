<?php


namespace App\Http\Controllers\Api\V1\ExamSystem;

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends AuthController
{
    function __construct()
    {
        parent::__construct();
    }

    public function picture(Request $request)
    {
        $protocol = env('UPYUN_EXAM_SYSTEM_PICTURE_PROTOCOL');
        $domain = env('UPYUN_EXAM_SYSTEM_PICTURE_DOMAIN');
        $resource = $request->file('file');
        $client = Storage::disk('upyun_exam_system_picture');
        $path = $client->put($this->getRelativePath(), $resource);
        $flag = $path ? OP_SUCCESS : OP_FAILURE;
        $data = [
            'path' => $path,
            'prefix' => $protocol . '://' . $domain . DIRECTORY_SEPARATOR,
        ];
        return $this->responseWithJson($data, __('common.upload' . $flag), $flag);
    }

    protected function getRelativePath($type = 'tmp'): string
    {
        $ds = DIRECTORY_SEPARATOR;
        $type = $ds . $type;
        $date = date("{$ds}y{$ds}m{$ds}d");
        return env('UPYUN_RELATIVE_PATH') . $type . $date;
    }
}
