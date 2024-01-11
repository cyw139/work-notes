<?php

namespace App\Models\ExamSystem;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Register extends ExamSystemBaseModel
{
    use HasFactory, SoftDeletes;

    const TYPE_INITIAL_TRAINING = 1; // 初训
    const TYPE_REVIEW = 2; // 复审
    const TYPE_REVIEW_RENEWAL = 3; // 复审换证
    const TYPES = [
        self::TYPE_INITIAL_TRAINING => [ 'id' => self::TYPE_INITIAL_TRAINING, 'name' => '初训'],
        self::TYPE_REVIEW => [ 'id' => self::TYPE_REVIEW, 'name' => '复审'],
        self::TYPE_REVIEW_RENEWAL => [ 'id' => self::TYPE_REVIEW_RENEWAL, 'name' => '复审换证'],
    ];
    protected $table = 'register';
    protected $primaryKey = 'id';

    protected $fillable = [
        'class_id',
        'user_id',
        'category_id',
        'type',
        'health_status',
        'education',
        'majors',
        'profession',
        'job_title',
        'job_title_majors',
        'enterprise_id',
        'enterprise_manager',
        'admin_id',
    ];

    protected $hidden = [
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
    public function classes()
    {
        return $this->belongsTo(Classes::class, 'class_id', 'id');
    }
    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class, 'enterprise_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function enterprise_manager()
    {
        return $this->belongsTo(User::class, 'enterprise_manager', 'id');
    }
}
