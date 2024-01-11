<?php

namespace App\Models\ExamSystem;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paper extends ExamSystemBaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'paper';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name', // 试卷标题
        'category_id', // 工种类别ID
        'description', // 试卷描述
        'admin_id', // 后台用户ID
        'reply_time', // 答题时间
        'start_time', // 考试开始时间
        'end_time', // 考试结束时间
        'exams_limit', // 考试次数限制
        'paper_options', // 试卷试题类型数据
    ];

    protected $hidden = [
    ];
    // 格式化字段：answer_options
//    public function setAnswerOptionsAttribute($value) {
//        $this->setAttribute('answer_options', serialize($value));
//        $this->attributes['answer_options'] =  serialize($value);
//    }
    public function getPaperOptionsAttribute($value)
    {
        return json_decode($value);
    }
    public function getStartTimeAttribute($value)
    {
        return date('Y-m-d H:i:s',$value);
    }
    public function getEndTimeAttribute($value)
    {
        return date('Y-m-d H:i:s',$value);
    }

    // 关联分类
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
