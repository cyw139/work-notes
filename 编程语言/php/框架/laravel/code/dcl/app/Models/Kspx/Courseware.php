<?php

namespace App\Models\Kspx;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Courseware extends KspxBaseModel
{
    use HasFactory, SoftDeletes;

    const STATUS = [
        COURSEWARE_STATUS_FORBIDDEN => '禁用',
        COURSEWARE_STATUS_NORMAL => '正常'
    ];

    protected $table = 'courseware';
    protected $primaryKey = 'id';

    protected $appends = [
        'status_name',
    ];
    protected $fillable = [
        'uid', // 用户ID
        'title', // 课件标题
//        'type', // 课件类型：1:视频;2:音频;3:文本;4:文档;5:视频链接;
//        'video_address', // 视频地址
//        'videokey', // 第三方存储key
        'admin_id', // 后台用户ID
        'status', // 状态【0:禁用;1:正常;】
//        'duration', // 视频时长
//        'filesize', // 视频大小(B)
//        'is_transcoding', // 是否需要转码【1:是;0:否;】
//        'transcoding_status', // 转码状态【0:转码失败;1:已经转码;2:等待转码;】
//        'video_type', // 视频存储第三方【0:本地;1:七牛;2:阿里云;4:cc储存;5:百度DOC服务,6:华为云储存】
//        'is_syn', // 是否同步【0:否;1:是;】
        'sort', // 排序【值越大越靠前】
        'category', // 视频分类id
        'created_at', // 创建于
        'updated_at', // 更新于
        'deleted_at', // 删除于
    ];

    protected $hidden = [
        'web_id',
    ];

    public function getStatusNameAttribute()
    {
        return self::STATUS[$this->status];
    }

    // 关联用户
    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'uid');
    }

    public function attaches()
    {
        return $this->belongsToMany(Attach::class, 'courseware_attach', 'courseware_id', 'attach_id')
            ->withPivot('alias', 'sort')->withTimestamps();
    }

    // 课件分类
    public function coursewareCategory()
    {
        return $this->belongsTo(CoursewareCategory::class, 'category', 'zy_currency_category_id');
    }

}
