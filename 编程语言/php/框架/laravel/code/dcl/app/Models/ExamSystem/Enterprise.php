<?php

namespace App\Models\ExamSystem;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Enterprise extends ExamSystemBaseModel
{
    use HasFactory, SoftDeletes;

    const ENTERPRISE_STATUS = [
        ENTERPRISE_STATUS_UNKNOWN,
        ENTERPRISE_STATUS_RESERVE,
        ENTERPRISE_STATUS_DESTROY,
    ];

    protected $table = 'enterprise';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'credit_code',
        'business_license_residence',
        'category_id',
        'legal_representative',
        'status',
        'logo',
        'established_date',
        'registered_capital',
        'registration_number',
        'paid_in_capital',
        'taxpayer_identification_number',
        'organization_code',
        'business_license',
        'operating_period_start',
        'operating_period_end',
        'taxpayer_qualification',
        'registration_date',
        'type',
        'trade',
        'staff_size',
        'insurance_amount',
        'registration_authority',
        'former_name',
        'former_name_period_start',
        'former_name_period_end',
        'english_name',
        'business_scope',
        'phone',
        'email',
        'url',
        'admin_id',
    ];

    protected $hidden = [
    ];

    // 资源格式化
    public static function formatter(&$data) {
        // 图片资源保存到正式路径，去掉tmp
        $keys = ['logo', 'business_license'];
        upYunMoveFile($data, $keys);
        // 时间格式化
        $time_keys = ['established_date', 'operating_period', 'registration_date', 'former_name_period'];
        foreach ($time_keys as $key) {
            if (in_array($key,['operating_period', 'former_name_period'])) {
                if (isset($data[$key]) && $data[$key]) {
                    $data[$key . '_start'] = $data[$key][0] / 1000;
                    $data[$key . '_end'] = $data[$key][1] / 1000;
                } else {
                    $data[$key . '_start'] = 0;
                    $data[$key . '_end'] = 0;
                }
                unset($data[$key]);
            } else {
                $data[$key] = strtotime($data[$key]);
            }
        }
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
    public function managers($listStatus)
    {
        $model = $this->belongsToMany(User::class, 'enterprise_manager', 'enterprise_id', 'user_id')
            ->withPivot('id', 'deleted_at')
            ->withTimestamps()
            ->with([ 'userContact'])
//            ->select('true_name', 'mobile')
            ->orderBy('enterprise_manager.id', 'desc');
        $this->setListStatus($model, $listStatus);
        return $model;
    }
    // 法定代表人
    public function legal_representative() {
        return $this->belongsTo(User::class, 'legal_representative', 'id');
    }

}
