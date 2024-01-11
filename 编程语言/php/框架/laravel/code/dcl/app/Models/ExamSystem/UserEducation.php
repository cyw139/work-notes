<?php

namespace App\Models\ExamSystem;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserEducation extends ExamSystemBaseModel
{
    use HasFactory, SoftDeletes;

    // 教育程度
    const EDUCATION_LEVELS = [
      EDUCATION_LEVEL_1,
      EDUCATION_LEVEL_2,
      EDUCATION_LEVEL_3,
      EDUCATION_LEVEL_4,
      EDUCATION_LEVEL_5,
      EDUCATION_LEVEL_6,
    ];
    // 学制
    const SCHOOL_SYSTEMS = [
      SCHOOL_SYSTEM_1,
      SCHOOL_SYSTEM_2,
      SCHOOL_SYSTEM_3,
      SCHOOL_SYSTEM_4,
      SCHOOL_SYSTEM_5,
    ];

    protected $table = 'user_education';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'diploma_number',
        'approval_number',
        'student_id',
        'education_level',
        'major',
        'graduation',
        'school_system',
        'graduated_school',
        'diploma_description',
        'diploma_image_front',
        'diploma_image_back',
        'admin_id',
    ];

    protected $hidden = [
    ];

    protected $appends = [
      'education_level_name',
      'school_system_name',
    ];

    public function getEducationLevelNameAttribute() {
        return __('user_education.education_level' . $this->education_level);
    }
    public function getSchoolSystemNameAttribute() {
        return __('user_education.school_system' . $this->school_system);
    }

    // 用户
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
