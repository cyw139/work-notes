<?php

namespace App\Models\Kspx;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KspxBaseModel extends Model
{
    use HasFactory;

    const STATUS = [RS_NORMAL=>'正常', RS_REMOVE=>'已回收', RS_ALL=>'全部'];
    protected $connection = 'mysql_kspx';

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
}
