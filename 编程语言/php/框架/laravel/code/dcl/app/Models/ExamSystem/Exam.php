<?php

namespace App\Models\ExamSystem;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends ExamSystemBaseModel
{
    use HasFactory, SoftDeletes;

    const STATUS_ASSEMBLE = 1; // 组卷
    const STATUS_SUBMIT = 2; // 交卷
    const STATUS_MARK = 3; // 评卷
    const STATUS_EXPIRED = 4; // 过期
    const STATUS = [
        self::STATUS_ASSEMBLE => '已组卷',
        self::STATUS_SUBMIT => '已交卷',
        self::STATUS_MARK => '已评卷',
        self::STATUS_EXPIRED => '过期',
    ];

    protected $table = 'exam';
    protected $primaryKey = 'id';

    protected $fillable = [
    ];

    protected $hidden = [
    ];

    protected $appends = [
        'status_name'
    ];

    public function getStatusNameAttribute() {
        return self::STATUS[$this->status] ?? "未知";
    }
    // 获取试卷试题
    public function getPaperQuestionsAttribute($value)
    {
        return json_decode($value);
    }
    // 获取试卷答案
    public function getUserAnswersAttribute($value)
    {
        return json_decode($value);
    }

    public function register()
    {
        return $this->belongsTo(Register::class, 'register_id', 'id');
    }
    public function classes()
    {
        return $this->belongsTo(Classes::class, 'class_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
