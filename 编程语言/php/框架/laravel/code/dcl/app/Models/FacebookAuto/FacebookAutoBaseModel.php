<?php

namespace App\Models\FacebookAuto;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacebookAutoBaseModel extends Model
{
    use HasFactory;

    const STATUS = [RS_NORMAL=>'正常', RS_REMOVE=>'已回收', RS_ALL=>'全部'];
    protected $connection = 'mysql_dcl_fb_auto';

    /**
     * 为数组 / JSON 序列化准备日期。
     *
     * @param \DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(\DateTimeInterface $date)
    {
        return Carbon::instance($date)->toDateTimeString();
    }

    public function getPrimaryKey() {
        return $this->primaryKey;
    }

    // 设置中间表记录状态过滤
    protected function setListStatus($model, $status) {
        $status = intval($status);
        if ($status === RS_NORMAL) {
            $model->wherePivotNull('deleted_at', 'and', false);
        } else if ($status === RS_REMOVE) {
            $model->wherePivotNull('deleted_at', 'and', true);
        }
    }
}
