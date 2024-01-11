<?php

namespace App\Models\ExamSystem;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classes extends ExamSystemBaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'class';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'category_id',
        'paper_id',
        'admin_id',
    ];

    protected $hidden = [
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
    public function paper()
    {
        return $this->belongsTo(Paper::class, 'paper_id', 'id');
    }
}
