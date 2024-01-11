<?php

namespace App\Models\Kspx;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamsSubject extends TreeBaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'exams_subject';
    protected $primaryKey = 'exams_subject_id';

    protected $fillable = [
        'title',
        'pid',
        'exams_module_id',
    ];

    protected $hidden = [
        'deleted_at',
        'sort',
        'web_id',
        'mhm_id',
    ];

    public function getParentIdName()
    {
        return 'pid';
    }

    public function examsModule()
    {
        return $this->belongsTo(ExamsModule::class, 'exams_module_id', 'exams_module_id');
    }

    public function examsPoint()
    {
        return $this->hasMany(ExamsPoint::class, 'exams_point_id', 'exams_point_id');
    }

}
