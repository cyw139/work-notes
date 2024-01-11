<?php

namespace App\Models\ExamSystem;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserContact extends ExamSystemBaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'user_contact';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'type',
        'name',
        'verified_at',
        'admin_id',
    ];

    protected $hidden = [
    ];

    public static function isExistsMobile($mobile) {
        return UserContact::withoutTrashed()->firstWhere([
            'type' => 'mobile',
            'name' => $mobile,
        ]);
    }

}
