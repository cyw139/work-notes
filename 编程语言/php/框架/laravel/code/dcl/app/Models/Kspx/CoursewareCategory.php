<?php

namespace App\Models\Kspx;

class CoursewareCategory extends TreeBaseModel
{
    protected $table = 'zy_currency_category';
    protected $primaryKey = 'zy_currency_category_id';

    protected $appends = [
//        'full_nodes'
    ];
    protected $fillable = [
        'title',
        'pid',
    ];

    protected $hidden = [
        'sort',
        'deleted_at',
        'middle_ids',
        'is_choice_pc',
        'is_choice_app',
        'is_choice_ranking',
        'is_h5_and_app',
        'is_nav_left',
        're_sort',
        'web_id',
        'mhm_id',
    ];

    public function courseware()
    {
        return $this->hasMany(Courseware::class, 'id', 'id');
    }

    public function getFullNodesAttribute()
    {
        $primaryKey = $this->getPrimaryKey();
        $id = $this->{$primaryKey};
        $ancestors = self::withTrashed()->find($id)->getAncestors([$primaryKey, 'title']);

        return $ancestors;
    }
}
