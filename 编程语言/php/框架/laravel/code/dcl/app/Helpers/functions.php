<?php

use Illuminate\Support\Facades\Storage;

/**
 * 又拍云临时路径移动至正式路径
 * @param array $data 待迁移数据
 * @param array $fields 待迁移字段
 */
function upYunMoveFile(&$data, $fields) {
    $client = Storage::disk('upyun_exam_system_picture');
    foreach ($fields as $field) {
        if (isset($data[$field]) && $data[$field]) {
            // 临时路径
            $isTempPath = strpos($data[$field], 'tmp/') !== false;
            $savePath = str_replace('tmp/', '' ,$data[$field]);
            if ($isTempPath && $client->has($data[$field])) {
                if ($client->has($savePath)) {
                    $data[$field] = $savePath;
                } else {
                    if ($client->move($data[$field], $savePath)) {
                        $data[$field] = $savePath;
                    }
                }
                usleep(200000);
            } else {
                if ($client->has($savePath)) {
                    $data[$field] = $savePath;
                }
            }
        }
    }
}
