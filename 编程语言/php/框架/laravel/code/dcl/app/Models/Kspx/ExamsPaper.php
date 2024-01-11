<?php

namespace App\Models\Kspx;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamsPaper extends KspxBaseModel
{
    use HasFactory, SoftDeletes;

    const LEVELS = [1 => '简单', 2 => '中等', 3 => '困难']; // 难易度
    const ASSEMBLY_TYPES = [0 => '手动组卷', 1 => '自动组卷']; // 组卷类型
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';

    protected $dateFormat = 'U';
    protected $table = 'exams_paper';
    protected $primaryKey = 'exams_paper_id';

    protected $fillable = [
        'exams_paper_title', // 试卷标题
        'exams_subject_id', // 分类ID
        'exams_module_id', // 板块ID
        'description', // 试卷描述
        'uid', // 用户ID
        'admin_id', // 后台用户ID
        'mhm_id', // 机构ID
        'exams_count', // 试卷考试总次数
        'reply_time', // 答题时间
        'level', // 试卷难度
        'is_rand', // 是否随机试题
        'start_time', // 考试开始时间
        'end_time', // 考试结束时间
        'sort', // 排序
        'exams_limit', // 考试次数限制
        'assembly_type', // 组卷类型
        'paper_options', // 试卷试题类型数据
        'price', // 销售价格
//        'is_del', // 是否删除（准备废弃）
    ];

    protected $hidden = [
        'show_user_group',
        'web_id',
    ];
    // 格式化字段：answer_options
//    public function setAnswerOptionsAttribute($value) {
//        $this->setAttribute('answer_options', serialize($value));
//        $this->attributes['answer_options'] =  serialize($value);
//    }
    public function getPaperOptionsAttribute($value)
    {
        return unserialize($value);
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
    public function examsSubject()
    {
        return $this->belongsTo(ExamsSubject::class, 'exams_subject_id', 'exams_subject_id');
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
