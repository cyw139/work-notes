<?php

namespace App\Models\ExamSystem;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class EnterpriseManager extends ExamSystemBaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'enterprise_manager';
    protected $primaryKey = 'id';

    protected $fillable = [
        'enterprise_id',
        'user_id',
        'job_title',
        'job_situation',
        'situation_description',
        'admin_id',
    ];

    protected $hidden = [
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class, 'enterprise_id', 'id');
    }

    /**
     * 创建企业和企业管理员关系
     * @param $enterprise_id
     * @param $manager_id
     * @param int $admin_id
     * @return array | false
     */
    public static function createRelation($request, int $admin_id = 0)
    {
        $enterprise_id = $request['enterprise_id'];
        $manager_id = $request['manager_id'];
        if (!$enterprise_id || !$manager_id) {
            return false;
        }
        DB::connection('mysql_exam_system')->beginTransaction();
        try {
            $result = EnterpriseManager::withoutTrashed()->firstWhere([
                'enterprise_id'=> $enterprise_id,
            ]);
            if ($result) {
                $result = $result->toArray();
                if ($result['user_id'] === $manager_id) {
                    return $result;
                } else {
                    EnterpriseManager::destroy($result['id']);
                }
            }
            $data = [
                'enterprise_id'=> $enterprise_id,
                'user_id'=> $manager_id,
                'admin_id' => $admin_id,
                'job_title' => $request['job_title'],
                'job_situation' => $request['job_situation'],
                'situation_description' => $request['situation_description'],
            ];
            $result = EnterpriseManager::create($data);
            if (!$result) {
                DB::connection('mysql_exam_system')->rollBack();
                return false;
            }
            DB::connection('mysql_exam_system')->commit();
            return $result ? $result->toArray() : [];
        } catch (QueryException $ex) {
            DB::connection('mysql_exam_system')->rollBack();
            return false;
        }
    }

}
