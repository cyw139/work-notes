<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//Route::middleware('auth:api')->post('/me', function (Request $request) {
//    return $request->user();
//});


$api = app('Dingo\Api\Routing\Router');

$api->version('v1.1', [
    'namespace' => '\App\Http\Controllers\Api',
    'middleware' => ['bindings', 'cors'],
],function($api) {
    $api->post('login', 'AuthController@login');
    $api->post('me', 'AuthController@me');
    // 全局配置
    $api->post('config', 'ConfigController@index');

    // 模拟考试系统
    $api->group(['prefix' => 'exam-system'], function($api) {
        // 用户管理
        $api->group(['prefix' => 'user'], function($api) {
            $api->get('', 'V1\ExamSystem\UserController@index'); // 列表
            $api->get('detail', 'V1\ExamSystem\UserController@detail'); // 详情
            $api->put('', 'V1\ExamSystem\UserController@update'); // 更新
            $api->put('recover', 'V1\ExamSystem\UserController@recover'); // 恢复
            $api->post('', 'V1\ExamSystem\UserController@create'); // 添加
            $api->post('import/photos', 'V1\ExamSystem\UserController@importPhotos'); // 照片批量导入
            $api->delete('', 'V1\ExamSystem\UserController@remove'); // 删除
//            $api->delete('forever', 'V1\ExamSystem\UserController@forceRemove'); // 永久删除
            $api->put('password', 'V1\ExamSystem\UserController@resetPassword'); // 重置密码
            // 联系方式 - 手机
            $api->group(['prefix' => 'mobile'], function($api) {
                $api->post('', 'V1\ExamSystem\UserContactController@createMobile'); // 添加
                $api->delete('', 'V1\ExamSystem\UserContactController@remove'); // 删除
                $api->put('recover', 'V1\ExamSystem\UserContactController@recover'); // 恢复
            });
            // 身份证
            $api->group(['prefix' => 'identity'], function($api) {
                $api->post('', 'V1\ExamSystem\UserIdentityController@change'); // 更换
            });
            // 学历
            $api->group(['prefix' => 'education'], function($api) {
                $api->get('', 'V1\ExamSystem\UserEducationController@index'); // 列表
                $api->get('detail', 'V1\ExamSystem\UserEducationController@detail'); // 详情
                $api->put('', 'V1\ExamSystem\UserEducationController@update'); // 更新
                $api->put('recover', 'V1\ExamSystem\UserEducationController@recover'); // 恢复
                $api->post('', 'V1\ExamSystem\UserEducationController@create'); // 添加
                $api->delete('', 'V1\ExamSystem\UserEducationController@remove'); // 删除
                $api->delete('forever', 'V1\ExamSystem\UserEducationController@forceRemove'); // 永久删除
            });
            // 企业岗位
            $api->group(['prefix' => 'enterprise_position'], function($api) {
                $api->get('', 'V1\ExamSystem\UserEnterprisePositionController@index'); // 列表
                $api->get('detail', 'V1\ExamSystem\UserEnterprisePositionController@detail'); // 详情
                $api->put('', 'V1\ExamSystem\UserEnterprisePositionController@update'); // 更新
                $api->put('recover', 'V1\ExamSystem\UserEnterprisePositionController@recover'); // 恢复
                $api->post('', 'V1\ExamSystem\UserEnterprisePositionController@create'); // 添加
                $api->delete('', 'V1\ExamSystem\UserEnterprisePositionController@remove'); // 删除
                $api->delete('forever', 'V1\ExamSystem\UserEnterprisePositionController@forceRemove'); // 永久删除
            });
            // 培训
            $api->group(['prefix' => 'training'], function($api) {
                $api->post('', 'V1\ExamSystem\UserIdentityController@change'); // 更换
            });
        });
        // 班级管理
        $api->group(['prefix' => 'classes'], function($api) {
            $api->get('', 'V1\ExamSystem\ClassesController@index'); // 列表
            $api->get('detail', 'V1\ExamSystem\ClassesController@detail'); // 详情
            $api->put('', 'V1\ExamSystem\ClassesController@update'); // 更新
            $api->put('recover', 'V1\ExamSystem\ClassesController@recover'); // 恢复
            $api->post('', 'V1\ExamSystem\ClassesController@create'); // 添加
            $api->delete('', 'V1\ExamSystem\ClassesController@remove'); // 删除
            $api->delete('forever', 'V1\ExamSystem\ClassesController@forceRemove'); // 永久删除
        });
        // 企业管理
        $api->group(['prefix' => 'enterprise'], function($api) {
            $api->get('', 'V1\ExamSystem\EnterpriseController@index'); // 列表
            $api->get('managers', 'V1\ExamSystem\EnterpriseController@managers'); // 企业管理员
            $api->get('detail', 'V1\ExamSystem\EnterpriseController@detail'); // 详情
            $api->put('', 'V1\ExamSystem\EnterpriseController@update'); // 更新
            $api->put('manager', 'V1\ExamSystem\EnterpriseController@updateManager'); // 更新企业管理员
            $api->put('recover', 'V1\ExamSystem\EnterpriseController@recover'); // 恢复
            $api->post('', 'V1\ExamSystem\EnterpriseController@create'); // 添加
            $api->delete('', 'V1\ExamSystem\EnterpriseController@remove'); // 删除
            $api->delete('forever', 'V1\ExamSystem\EnterpriseController@forceRemove'); // 永久删除
        });
        // 类别管理
        $api->group(['prefix' => 'category'], function($api) {
            $api->get('', 'V1\ExamSystem\CategoryController@index'); // 列表
            $api->get('name-unique', 'V1\ExamSystem\CategoryController@nameUnique'); // 父节点的直接子节点名称唯一
            $api->get('tree', 'V1\ExamSystem\CategoryController@tree'); // 树
            $api->get('subtree', 'V1\ExamSystem\CategoryController@subtree'); // 子树
            $api->get('children', 'V1\ExamSystem\CategoryController@children'); // 直接孩子们
            $api->get('detail', 'V1\ExamSystem\CategoryController@detail'); // 详情
            $api->put('', 'V1\ExamSystem\CategoryController@update'); // 更新
            $api->put('recover', 'V1\ExamSystem\CategoryController@recover'); // 恢复
            $api->post('', 'V1\ExamSystem\CategoryController@create'); // 添加
            $api->post('import', 'V1\ExamSystem\CategoryController@import'); // 导入
            $api->delete('', 'V1\ExamSystem\CategoryController@remove'); // 删除
            $api->delete('forever', 'V1\ExamSystem\CategoryController@forceRemove'); // 永久删除
        });
        // 报名管理
        $api->group(['prefix' => 'register'], function($api) {
            $api->get('', 'V1\ExamSystem\RegisterController@index'); // 列表
            $api->get('detail', 'V1\ExamSystem\RegisterController@detail'); // 详情
            $api->put('', 'V1\ExamSystem\RegisterController@update'); // 更新
            $api->put('recover', 'V1\ExamSystem\RegisterController@recover'); // 恢复
            $api->post('', 'V1\ExamSystem\RegisterController@create'); // 添加
            $api->post('import', 'V1\ExamSystem\RegisterController@import'); // 导入
            $api->delete('', 'V1\ExamSystem\RegisterController@remove'); // 删除
            $api->delete('forever', 'V1\ExamSystem\RegisterController@forceRemove'); // 永久删除
        });
        // 试题
        $api->group(['prefix' => 'question'], function($api) {
            $api->get('', 'V1\ExamSystem\QuestionController@index'); // 列表
            $api->get('detail', 'V1\ExamSystem\QuestionController@detail'); // 详情
            $api->get('type/amount/stat', 'V1\ExamSystem\QuestionController@typeAmountStat'); // 类型数量统计
            $api->put('', 'V1\ExamSystem\QuestionController@update'); // 更新
            $api->put('recover', 'V1\ExamSystem\QuestionController@recover'); // 恢复
            $api->post('', 'V1\ExamSystem\QuestionController@create'); // 添加
            $api->post('import', 'V1\ExamSystem\QuestionController@import'); // 导入
            $api->delete('', 'V1\ExamSystem\QuestionController@remove'); // 删除
            $api->delete('forever', 'V1\ExamSystem\QuestionController@forceRemove'); // 永久删除
        });
        // 试卷
        $api->group(['prefix' => 'paper'], function($api) {
            $api->get('', 'V1\ExamSystem\PaperController@index'); // 列表
            $api->get('detail', 'V1\ExamSystem\PaperController@detail'); // 详情
            $api->put('', 'V1\ExamSystem\PaperController@update'); // 更新
            $api->put('recover', 'V1\ExamSystem\PaperController@recover'); // 恢复
            $api->post('', 'V1\ExamSystem\PaperController@create'); // 添加
            $api->post('import', 'V1\ExamSystem\PaperController@import'); // 导入
            $api->delete('', 'V1\ExamSystem\PaperController@remove'); // 删除
            $api->delete('forever', 'V1\ExamSystem\PaperController@forceRemove'); // 永久删除
        });
        // 成绩
        $api->group(['prefix' => 'exam'], function($api) {
            $api->get('', 'V1\ExamSystem\ExamController@index'); // 列表
            $api->get('detail', 'V1\ExamSystem\ExamController@detail'); // 详情
        });
        // 上传
        $api->group(['prefix' => 'upload'], function ($api) {
            $api->post('picture', 'V1\ExamSystem\UploadController@picture'); // 上传图片
        });
    });

    // 上传
    $api->group(['prefix' => 'upload'], function ($api) {
        $api->post('video', 'UploadController@video'); // 上传视频
        $api->post('picture', 'UploadController@picture'); // 上传图片
    });
    // 成绩
//    $api->group(['prefix' => ''], function($api) {
//
//    });
    // 报班
    $api->group(['prefix' => 'course'], function($api) {
        // 班级管理
        // 报班企业
        // 报班学员
    });
    // 课程超市
    $api->group(['prefix' => 'supermarket'], function($api) {
        // 班级
//    $api->group(['prefix' => 'course'], function($api) {
//    });
        // 课程
        $api->group(['prefix' => 'course'], function($api) {
        });
        // 课件
        $api->group(['prefix' => 'courseware'], function($api) {
            $api->get('', 'V1\CoursewareController@index'); // 列表
            $api->get('detail', 'V1\CoursewareController@detail'); // 详情
            $api->put('', 'V1\CoursewareController@update'); // 更新
            $api->put('recover', 'V1\CoursewareController@recover'); // 恢复
            $api->post('', 'V1\CoursewareController@create'); // 添加
            $api->delete('', 'V1\CoursewareController@remove'); // 删除
            $api->delete('forever', 'V1\CoursewareController@forceRemove'); // 永久删除
            // 课件分类
            $api->group(['prefix' => 'category'], function($api) {
                $api->get('', 'V1\CoursewareCategoryController@index'); // 列表
                $api->get('tree', 'V1\CoursewareCategoryController@tree'); // 树
                $api->get('subtree', 'V1\CoursewareCategoryController@subtree'); // 子树
                $api->get('children', 'V1\CoursewareCategoryController@children'); // 直接孩子们
                $api->get('detail', 'V1\CoursewareCategoryController@detail'); // 详情
                $api->put('', 'V1\CoursewareCategoryController@update'); // 更新
                $api->put('recover', 'V1\CoursewareCategoryController@recover'); // 恢复
                $api->post('', 'V1\CoursewareCategoryController@create'); // 添加
                $api->delete('', 'V1\CoursewareCategoryController@remove'); // 软删除
                $api->delete('forever', 'V1\CoursewareCategoryController@forceRemove'); // 永久删除
            });
        });
    });
    // 考试
    $api->group(['prefix' => 'exams'], function($api) {
        // 板块
        $api->group(['prefix' => 'module'], function($api) {
            $api->get('', 'V1\ExamsModuleController@index'); // 列表
            $api->get('all', 'V1\ExamsModuleController@all'); // 全部
            $api->get('detail', 'V1\ExamsModuleController@detail'); // 详情
            $api->put('', 'V1\ExamsModuleController@update'); // 更新
            $api->post('', 'V1\ExamsModuleController@create'); // 添加
            $api->delete('', 'V1\ExamsModuleController@remove'); // 删除
            $api->delete('forever', 'V1\ExamsModuleController@forceRemove'); // 永久删除
        });
        // 分类
        $api->group(['prefix' => 'subject'], function($api) {
            $api->get('', 'V1\ExamsSubjectController@index'); // 列表
            $api->get('tree', 'V1\ExamsSubjectController@tree'); // 树
            $api->get('subtree', 'V1\ExamsSubjectController@subtree'); // 子树
            $api->get('children', 'V1\ExamsSubjectController@children'); // 直接孩子们
            $api->get('detail', 'V1\ExamsSubjectController@detail'); // 详情
            $api->put('', 'V1\ExamsSubjectController@update'); // 更新
            $api->put('recover', 'V1\ExamsSubjectController@recover'); // 恢复
            $api->post('', 'V1\ExamsSubjectController@create'); // 添加
            $api->delete('', 'V1\ExamsSubjectController@remove'); // 软删除
            $api->delete('forever', 'V1\ExamsSubjectController@forceRemove'); // 永久删除
        });
        // 考点
        $api->group(['prefix' => 'point'], function($api) {
            $api->get('', 'V1\ExamsPointController@index'); // 列表
            $api->get('allBySubjectId', 'V1\ExamsPointController@allBySubjectId'); // 通过分类ID获取所有考点
            $api->get('detail', 'V1\ExamsPointController@detail'); // 详情
            $api->put('', 'V1\ExamsPointController@update'); // 更新
            $api->post('', 'V1\ExamsPointController@create'); // 添加
            $api->delete('', 'V1\ExamsPointController@remove'); // 删除
            $api->delete('forever', 'V1\ExamsPointController@forceRemove'); // 永久删除
        });
        // 试题
        $api->group(['prefix' => 'question'], function($api) {
            $api->get('', 'V1\ExamsQuestionController@index'); // 列表
            $api->get('detail', 'V1\ExamsQuestionController@detail'); // 详情
            $api->get('type/amount/stat', 'V1\ExamsQuestionController@typeAmountStat'); // 类型数量统计
            $api->put('', 'V1\ExamsQuestionController@update'); // 更新
            $api->post('', 'V1\ExamsQuestionController@create'); // 添加
            $api->post('import', 'V1\ExamsQuestionController@import'); // 导入
            $api->delete('', 'V1\ExamsQuestionController@remove'); // 删除
            $api->delete('forever', 'V1\ExamsQuestionController@forceRemove'); // 永久删除
        });
        // 试卷
        $api->group(['prefix' => 'paper'], function($api) {
            $api->get('', 'V1\ExamsPaperController@index'); // 列表
            $api->get('detail', 'V1\ExamsPaperController@detail'); // 详情
            $api->put('', 'V1\ExamsPaperController@update'); // 更新
            $api->put('recover', 'V1\ExamsPaperController@recover'); // 恢复
            $api->post('', 'V1\ExamsPaperController@create'); // 添加
            $api->delete('', 'V1\ExamsPaperController@remove'); // 删除
            $api->delete('forever', 'V1\ExamsPaperController@forceRemove'); // 永久删除
        });
    });
});
