<?php

namespace App\Models\FacebookAuto;

use App\Models\ExamSystem\Category;
use App\Models\ExamSystem\ExamSystemBaseModel;
use App\Models\ExamSystem\Paper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfileModel extends ExamSystemBaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'profile';
    protected $primaryKey = 'id';

    protected $fillable = [
        'ixb_browser_id',
        'mobile_area',
        'mobile',
        'gender',
        'email',
        'birth_date',
        'birth_year',
    ];

    protected $hidden = [
    ];

}
