<?php

namespace App\Models\Kspx;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamsQuestion extends KspxBaseModel
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';
    const TYPES = [
        1=>'radio', // 单选
        2=>'multi', // 多选
        3=>'judge', // 判断
    ]; // 试题分类

    protected $dateFormat = 'U';
    protected $table = 'exams_question';
    protected $primaryKey = 'exams_question_id';

    protected $fillable = [
        'uid', // 用户ID
        'admin_id', // 后台用户ID
        'exams_question_type_id', // 试题分类ID
        'exams_subject_id', // 分类ID
        'exams_point_id', // 考点ID
        'exams_module_id', // 板块ID
        'level', // 难度
        'content', // 试题内容
        'answer_options', // 答题选项内容
        'answer_true_option', // 正确选项标识
        'analyze', // 试题解析
        'mhm_id', // 机构ID
//        'is_del', // 是否删除（准备废弃）
    ];

    protected $hidden = [
        'deleted_at',
        'web_id',
    ];
    // 格式化字段：answer_options
//    public function setAnswerOptionsAttribute($value) {
////        $this->setAttribute('answer_options', serialize($value));
//        $this->attributes['answer_options'] =  serialize($value);
//    }
    public function getAnswerOptionsAttribute($value) {
        return unserialize($value);
    }
    // 格式化字段：answer_true_option
//    public function setAnswerTrueOptionAttribute($value) {
//        $this->setAttribute('answer_true_option', serialize($value));
//    }
    public function getAnswerTrueOptionAttribute($value) {
        return unserialize($value);
    }
    // 关联分类
    public function examsSubject()
    {
        return $this->belongsTo(ExamsSubject::class, 'exams_subject_id', 'exams_subject_id');
    }
    // 关联考点
    public function examsPoint()
    {
        return $this->belongsTo(ExamsPoint::class, 'exams_point_id', 'exams_point_id');
    }
    // 关联板块
    public function examsModule()
    {
        return $this->belongsTo(ExamsModule::class, 'exams_module_id', 'exams_module_id');
    }
    // 关联用户
    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'uid');
    }
    // 关联机构
    public function school()
    {
        return $this->belongsTo(School::class, 'mhm_id', 'id');
    }

}
