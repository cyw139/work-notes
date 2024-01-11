<?php

namespace App\Models\ExamSystem;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends ExamSystemBaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'user';
    protected $primaryKey = 'id';

    protected $fillable = [
        'true_name',
        'sex',
        'nationality',
        'id_card_address',
        'id_card_front',
        'id_card_back',
        'photo',
        'issuing_authority',
        'valid_period_start',
        'valid_period_end',
        'password',
        'id_card',
        'admin_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // 用户身份证
    public function userIdentity() {
        return $this->hasOne(UserIdentity::class, 'user_id');
    }
    // 用户联系方式
    public function userContact() {
        return $this->hasMany(UserContact::class, 'user_id');
    }

    // 是否存在身份证
    public static function isExistsIdCard($id_card) {
        return User::withoutTrashed()->firstWhere(['id_card' => $id_card]);
    }

    // 是否存在用户
    public static function isExists($user_id) {
        return User::withoutTrashed()->find($user_id);
    }

}
