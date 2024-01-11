<?php

namespace App\Models\Kspx;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamsModule extends KspxBaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'exams_module';
    protected $primaryKey = 'exams_module_id';

    protected $fillable = [
        'title',
        'icon',
        'description',
        'btn_text',
        'is_practice',
        'display',
        'sort',
    ];

    protected $hidden = [
        'pid',
        'web_id',
    ];

    public function ExamsSubjects()
    {
        return $this->hasMany('ExamsSubject');
    }

}
