<?php


namespace App\Http\Controllers\Api;

use App\Models\Kspx\Attach;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends AuthController
{
    function __construct()
    {
        parent::__construct();
    }

    public function video(Request $request)
    {
        $resource = $request->file('file');
        $client = Storage::disk('upyun_kspx_video');
        $path = $client->put($this->getRelativePath(), $resource);
        $avMeta = $client->avMeta($path);
        $flag = $path ? OP_SUCCESS : OP_FAILURE;
        $attach = $this->saveAttach('video', $path, $avMeta);
        return $this->responseWithJson($attach, __('common.upload' . $flag), $flag);
    }

    protected function saveAttach($type, $path, $avMeta)
    {
        if ($type === 'video') {
            return $this->saveVideoAttach($path, $avMeta);
        }
    }

    protected function saveVideoAttach($path, $avMeta)
    {
        $width = $height = $duration = $filesize = 0;
        if ($avMeta) {
            if ($avMeta['streams']) {
                $width = $avMeta['streams'][0]['video_width'] ?? 0;
                $height = $avMeta['streams'][0]['video_height'] ?? 0;
            }
            if ($avMeta['format']) {
                $duration = $avMeta['format']['duration'] ?? 0;
                $filesize = $avMeta['format']['filesize'] ?? 0;

            }
        }
        $extension = $save_path = $save_name = '';
        $prefix = env('UPYUN_RELATIVE_PATH');
        $pattern = '/^('.$prefix.'\/tmp\/\d{2}\/\d{2}\/\d{2}\/)+([a-zA-Z0-9]+\.(.+))$/';
        $flag = preg_match_all($pattern, $path, $matches);
        if ($flag) {
            $extension = $flag ? $matches[3][0] : '';
            $save_path = $flag ? $matches[1][0] : '';
            $save_name = $flag ? $matches[2][0] : '';
        }
        $data = [
            'attach_type' => 'courseware', // @todo 分类需要上传
            'name' => $save_name,
            'type' => '',
            'size' => $filesize,
            'extension' => $extension,
            'transcoding_status' => -1,
            'admin_id' => $this->user()->id,
            'created_at' => time(),
            'updated_at' => time(),
            'save_path' => $save_path,
            'save_name' => $save_name,
            'from' => 0,
            'width' => $width,
            'height' => $height,
            'meta' => $avMeta ? json_encode($avMeta) : '',
            'duration' => $duration,
            'resource_type' => 1, // 视频
            'storage_type' => 7, // 又拍云
        ];
        $result = Attach::create($data);
        return $result && $result['attach_id'] > 0 ? $result : false;
    }

    protected function getRelativePath($type = 'tmp'): string
    {
        $ds = DIRECTORY_SEPARATOR;
        $type = $ds . $type;
        $date = date("{$ds}y{$ds}m{$ds}d");
        return env('UPYUN_RELATIVE_PATH') . $type . $date;
    }

    public function picture()
    {

    }
}
