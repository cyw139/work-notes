<?php

namespace App\Models\Kspx;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attach extends KspxBaseModel
{
    use HasFactory, SoftDeletes;

    const RESOURCE_TYPES = [
        ATTACH_RESOURCE_TYPE_UNKNOWN => '未知',
        ATTACH_RESOURCE_TYPE_VIDEO => '视频',
        ATTACH_RESOURCE_TYPE_SOUND => '音频',
        ATTACH_RESOURCE_TYPE_PICTURE => '图片',
        ATTACH_RESOURCE_TYPE_DOCUMENT => '文档',
        ATTACH_RESOURCE_TYPE_VIDEO_URL => '视频链接'
    ];
    const STORAGE_TYPES = [
        ATTACH_STORAGE_UNKNOWN => '未知',
        ATTACH_STORAGE_LOCAL => '本地',
        ATTACH_STORAGE_QINIU => '七牛',
        ATTACH_STORAGE_ALIYUN => '阿里云',
        ATTACH_STORAGE_CC => 'cc',
        ATTACH_STORAGE_BAIDU_DOC => '百度文档',
        ATTACH_STORAGE_HUAWEIYUN => 'EduLine存储', // 华为云
        ATTACH_STORAGE_UPYUN => '又拍云',
        ATTACH_STORAGE_THIRD => '第三方链接',
    ];
    const TRANSCODING_STATUS = [
        ATTACH_TRANSCODING_STATUS_NOOP => '无转码',
        ATTACH_TRANSCODING_STATUS_FAILURE => '转码失败',
        ATTACH_TRANSCODING_STATUS_FINISH => '已转码',
        ATTACH_TRANSCODING_STATUS_WAITING => '转码中',
    ];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dateFormat = 'U';
    protected $table = 'attach';
    protected $primaryKey = 'attach_id';

    protected $appends = [
        'resource_type_name', // 资源类型
        'storage_type_name', // 存储类型
        'transcoding_status_name', // 转码状态
    ];

    protected $fillable = [
        'attach_type', // 附件所属类型：如：课件类型（courseware）
        'name', // 附件名称
        'type', // 附件格式
        'size', // 附件大小，单位Byte
        'extension', // 附件扩展名
        'transcoding_status', // 转码状态
        'admin_id', // 后台用户
//        'hash', // 附件hash
//        'private', // 是否私有
//        'id_del', // 是否删除（迁移数据后，准备废弃）
        'save_path', // 保存路径，如 2020/08/16/
        'save_name', // 保存文件名，如 2de35f6a329a.m3u8
//        'save_domain', // 保存域名（不同服务器）
        'from', // 来源类型【0:网站;1:手机网页版;2:android;3:iphone;】
        'width', // 资源宽度
        'height', // 资源高度
        'meta', // 元数据
        'duration', // 时长
        'resource_type', // 资源类型【0:未知;1:视频;2:音频;3:图片;4:文档;5:视频链接;】
//        'zy_video_data_id', // 旧课件表ID：zy_video_data表
        'storage_type', // 存储位置：【-1:未知;0:本地;1:七牛;2:阿里云;4:cc储存;5:百度DOC服务,6:华为云储存,7:又拍云,8:外链】

    ];

    protected $hidden = [
        'hash',
        'is_del',
        'private',
        'save_domain',
        'web_id',
        'zy_video_data_id',
    ];

    public function getResourceTypeNameAttribute()
    {
        return self::RESOURCE_TYPES[$this->resource_type] ?? '';
    }

    public function getStorageTypeNameAttribute()
    {
        return self::STORAGE_TYPES[$this->storage_type] ?? '';
    }
    public function getTranscodingStatusNameAttribute()
    {
        return self::TRANSCODING_STATUS[$this->transcoding_status] ?? '';
    }

    // 关联用户
    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'uid');
    }

}
