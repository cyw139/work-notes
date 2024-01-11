<?php


namespace App\Http\Controllers\Api;


use App\Models\ExamSystem\Category;
use App\Models\ExamSystem\Enterprise;
use App\Models\ExamSystem\Question;
use App\Models\ExamSystem\Register;
use App\Models\ExamSystem\UserEducation;
use App\Models\ExamSystem\UserEnterprisePosition;
use Ramsey\Collection\Collection;

class ConfigController extends AuthController
{
    function __construct()
    {
        parent::__construct();
    }

    public function index() {
        $data = [
            // 模拟考试-app
            'exam_system' => [
                'resource' => [
                    // 图片资源前缀
                    'picture' => [
                        'prefix' => env('UPYUN_EXAM_SYSTEM_PICTURE_PROTOCOL') . '://' . env('UPYUN_EXAM_SYSTEM_PICTURE_DOMAIN') . DIRECTORY_SEPARATOR,
                    ]
                ],
                // 类别种类
                'categoryType' => Category::TYPES,
                // 报名种类
                'registerType' => Register::TYPES,
                // 题库种类
                'questionType' => Question::TYPES,
                // 用户教育程度
                'userEducationLevel' => collect(UserEducation::EDUCATION_LEVELS)->map(function($item) {
                    return [
                        "id" => $item,
                        "name" => __('user_education.education_level' . $item),
                    ];
                }),
                // 用户学制
                'userSchoolSystem' => collect(UserEducation::SCHOOL_SYSTEMS)->map(function($item) {
                    return [
                        "id" => $item,
                        "name" => __('user_education.school_system' . $item),
                    ];
                }),
                // 企业状态
                'enterpriseStatus' => collect(Enterprise::ENTERPRISE_STATUS)->map(function($item) {
                    return [
                        "id" => $item,
                        "name" => __('enterprise.status' . $item),
                    ];
                }),
                // 用户在职情况
                'userEnterprisePositionJobSituation' => collect(UserEnterprisePosition::JOB_STATUS)->map(function($item) {
                    return [
                        "id" => $item,
                        "name" => __('user_enterprise_position.job_status' . $item),
                    ];
                }),
            ],
            // 酷爽培训-app
            'kspx' => [

            ]
        ];
        return $this->responseWithJson($data);
    }
}
