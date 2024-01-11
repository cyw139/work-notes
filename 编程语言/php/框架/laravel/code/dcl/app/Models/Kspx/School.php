<?php

namespace App\Models\Kspx;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends KspxBaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'school';
    protected $primaryKey = 'id';

    protected $fillable = [
        'title',
    ];

    protected $hidden = [
    ];

    public function examsQuestion()
    {
        return $this->hasMany(ExamsQuestion::class);
    }

}
