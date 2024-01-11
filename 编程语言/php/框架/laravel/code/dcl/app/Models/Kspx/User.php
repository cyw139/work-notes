<?php

namespace App\Models\Kspx;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends KspxBaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'user';
    protected $primaryKey = 'uid';

    protected $fillable = [
        'login',
        'uname',
        'true_name',
        'id_card',
        'mhm_id',
        'deleted_at',
    ];

    protected $hidden = [
        'password',
        'salt',
    ];

    public function examsQuestion()
    {
        return $this->hasMany(ExamsQuestion::class);
    }

}
