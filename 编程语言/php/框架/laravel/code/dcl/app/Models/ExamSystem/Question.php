<?php

namespace App\Models\ExamSystem;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends ExamSystemBaseModel
{
    use HasFactory, SoftDeletes;

    const TYPE_RADIO = 1;
    const TYPE_MULTI = 2;
    const TYPE_JUDGE = 3;
    const TYPES = [
        self::TYPE_RADIO =>['id' => self::TYPE_RADIO, 'name' => 'radio', 'label' => '单选'], // 单选
        self::TYPE_MULTI =>['id' => self::TYPE_MULTI, 'name' => 'multi', 'label' => '多选'], // 多选
        self::TYPE_JUDGE =>['id' => self::TYPE_JUDGE, 'name' => 'judge', 'label' => '判断'], // 判断
    ]; // 试题分类

    protected $table = 'question';
    protected $primaryKey = 'id';

    protected $fillable = [
        'admin_id', // 后台用户ID
        'category_id', // 工种类别
        'type', // 类型
        'content', // 试题内容
        'answer_options', // 答题选项内容
        'answer_true_option', // 正确选项标识
        'analyze', // 试题解析
    ];

    protected $hidden = [
    ];
    public function getAnswerOptionsAttribute($value) {
        return json_decode($value);
    }
    public function getAnswerTrueOptionAttribute($value) {
        return json_decode($value);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

}
