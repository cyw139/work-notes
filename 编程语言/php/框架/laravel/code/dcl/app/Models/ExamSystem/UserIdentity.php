<?php

namespace App\Models\ExamSystem;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class UserIdentity extends ExamSystemBaseModel
{
    use HasFactory, SoftDeletes;

    const SEX = [
        SEX_UNKNOWN => '未知',
        SEX_MALE => '男',
        SEX_FEMALE => '女',
    ];

    protected $table = 'user_identity';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'true_name',
        'sex',
        'nationality',
        'id_card_address',
        'id_card_front',
        'id_card_back',
        'photo',
        'issuing_authority',
        'valid_period_start',
        'valid_period_end',
        'admin_id',
    ];

    protected $hidden = [
    ];

    // 资源格式化
    public static function formatter(&$data) {
        // 图片资源保存到正式路径，去掉tmp
        $keys = ['id_card_front', 'id_card_back', 'photo', 'diploma_image_front', 'diploma_image_back'];
        upYunMoveFile($data, $keys);

        // 时间格式化
        $time_keys = ['valid_period', 'graduation', 'entry_date'];
        foreach ($time_keys as $key) {
            if (isset($data[$key]) && $data[$key]) {
                if ($key === 'valid_period') {
                    $data['valid_period_start'] = $data[$key][0] / 1000;
                    $data['valid_period_end'] = $data[$key][1] / 1000;
                    unset($data[$key]);
                } else {
                    $data[$key] = strtotime($data[$key]);
                }
            }
        }
    }

}
