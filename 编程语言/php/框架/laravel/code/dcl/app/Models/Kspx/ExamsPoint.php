<?php

namespace App\Models\Kspx;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamsPoint extends KspxBaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'exams_point';
    protected $primaryKey = 'exams_point_id';

    protected $fillable = [
        'title',
        'exams_subject_id',
    ];

    protected $hidden = [
        'deleted_at',
        'mhm_id',
        'web_id',
    ];

    public function examsSubject()
    {
        return $this->belongsTo(ExamsSubject::class, 'exams_subject_id', 'exams_subject_id');
    }

}
