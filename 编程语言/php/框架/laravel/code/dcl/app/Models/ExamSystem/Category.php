<?php

namespace App\Models\ExamSystem;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Category extends TreeBaseModel
{
    use HasFactory, SoftDeletes;

    const WORK_TYPE = 1; // 工种类别
    const ENTERPRISE_TYPE = 2; // 企业类别
    const TYPES = [
        self::WORK_TYPE => [ 'id' => self::WORK_TYPE, 'name' => '工种类型'],
        self::ENTERPRISE_TYPE => [ 'id' => self::ENTERPRISE_TYPE, 'name' => '企业类型'],
    ];

    protected $table = 'category';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'pid',
        'type',
        'admin_id',
    ];

    protected $hidden = [
    ];

    public function getParentIdName()
    {
        return 'pid';
    }

    public static function ancestorsFormatter(Collection $collection, $level = null) {
        if ($collection && $collection->count() > 0) {
            $collection->each(function($obj) use ($level) {
                $item = $obj;
                if (is_string($level) ) {
                    $levels = explode('.', $level);
                    foreach ($levels as $level) {
                        $item = $item->{$level};
                    }
                }
                if ($item->category_id && $item->category_id > 0) {
                    $result = Category::ancestorsAndSelf($item->category_id);
                    $item->category->ancestors = $result->map(function($item) {
                        return ['id' => $item->id, 'name' => $item->name];
                    });
                }
            });
        }
    }
}
