<?php

namespace App\Models\ExamSystem;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserEnterprisePosition extends ExamSystemBaseModel
{
    use HasFactory, SoftDeletes;

    const JOB_STATUS = [
      JOB_STATUS_HOLD, // 在职
      JOB_STATUS_RESIGN, // 离职
      JOB_STATUS_DECIDING, // 待定
    ];

    protected $table = 'user_enterprise_position';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'enterprise_id',
        'entry_date',
        'job_title',
        'work_type',
        'job',
        'section',
        'category_id',
        'job_situation',
        'admin_id',
    ];

    protected $appends = [
        'job_situation_name'
    ];

    protected $hidden = [
    ];

    public function getJobSituationNameAttribute() {
        return __('user_enterprise_position.job_status' . $this->job_situation);
    }

    // 用户
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
    // 企业
    public function enterprise() {
        return $this->belongsTo(Enterprise::class, 'enterprise_id');
    }
}
