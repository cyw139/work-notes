<?php


namespace App\Imports\ExamSystem;

use App\Models\ExamSystem\Category;
use App\Models\ExamSystem\Classes;
use App\Models\ExamSystem\Enterprise;
use App\Models\ExamSystem\EnterpriseManager;
use App\Models\ExamSystem\Register;
use App\Models\ExamSystem\User;
use App\Models\ExamSystem\UserContact;
use App\Models\ExamSystem\UserIdentity;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserFirstSheetImport extends BaseSheetImport
{
    public function __construct(int $userId)
    {
        parent::__construct($userId);
    }

    /**
     * @inheritDoc
     */
    public function collection(Collection $rows)
    {
        // TODO: Implement collection() method.
        foreach ($rows as $index => $row) {
            if ($index === 0) {
                $this->getNameIndexMap($row);
            } else {
                $this->import($row);
            }
        }
    }

    private function import($row) {

        $user = $this->importUser($row);
        return $user;
    }
    private function importUser($row)
    {
        // 身份证不存在
        $id_card = $row[$this->nameIndexMap['公民身份证号码']];
        if (!$id_card) {
            return false;
        }
        // 记录已存在
        $userData = User::firstWhere(['id_card' => $id_card]);
        if ($userData) {
            return $userData->toArray();
        }
        $sex = substr($id_card, -2, 1) % 2 === 1 ? 1 : 2;
        $password = bcrypt(substr($id_card, -6));
        $true_name = $row[$this->nameIndexMap['姓名']];
        $mobile = intval($row[$this->nameIndexMap['手机号码']]);
        $nationality = $row[$this->nameIndexMap['民族']];
        $id_card_address = $row[$this->nameIndexMap['身份证住址']];
        $issuing_authority = $row[$this->nameIndexMap['身份证签发机关']];
        $valid_period_start = $row[$this->nameIndexMap['身份证有效期限起']] ?? '';
        $valid_period_end = $row[$this->nameIndexMap['身份证有效期限止']] ?? '';
        if (!$valid_period_start || !$valid_period_end) {
            return false;
        }
        $valid_period_start = $this->transformDate($valid_period_start)->getTimestamp();
        $valid_period_end = $this->transformDate($valid_period_end)->getTimestamp();
        // 用户基础信息
        $user = [
            'password' => $password,
            'id_card' => $id_card,
            'admin_id' => $this->userId,
            'true_name' => $true_name,
            'sex' => $sex,
            'nationality' => $nationality,
            'id_card_address' => $id_card_address,
            'issuing_authority' => $issuing_authority,
            'valid_period_start' => $valid_period_start,
            'valid_period_end' => $valid_period_end,
        ];
        $this->userDataFormatter($user);
        $user_identity = [
            'admin_id' => $this->userId,
            'true_name' => $true_name,
            'sex' => $sex,
            'nationality' => $nationality,
            'id_card_address' => $id_card_address,
            'issuing_authority' => $issuing_authority,
            'valid_period_start' => $valid_period_start,
            'valid_period_end' => $valid_period_end,
        ];
        $this->userDataFormatter($user_identity);
        $user_contact = [
            'type' => 'mobile',
            'name' => $mobile,
            'admin_id' => $this->userId,
        ];


        DB::connection('mysql_exam_system')->beginTransaction();
        try {
            // 用户创建
            UserIdentity::formatter($user);
            $result_user = User::create($user);
            if (!$result_user) {
                DB::connection('mysql_exam_system')->rollBack();
                Log::error(__('common.user_create'.OP_FAILURE) . ": " . json_encode($user));
                return false;
            }
            // 身份证创建
            $user_identity['user_id'] = $result_user['id'];
            $result_user_identity = UserIdentity::create($user_identity);
            if (!$result_user_identity) {
                DB::connection('mysql_exam_system')->rollBack();
                Log::error(__('common.user_identity_create'.OP_FAILURE) . ": " . json_encode($user_identity));
                return false;
            }
            // 手机创建
            $user_contact['user_id'] = $result_user['id'];
            $result_user_contact = UserContact::create($user_contact);
            if (!$result_user_contact) {
                DB::connection('mysql_exam_system')->rollBack();
                Log::error(__('common.user_mobile_create'.OP_FAILURE) . ": " . json_encode($user_contact));
                return false;
            }

            DB::connection('mysql_exam_system')->commit();
            $result_user['user_identity'] = $result_user_identity;
            $result_user['user_contact'] = $user_contact;
            return $result_user;
        } catch (QueryException $ex) {
            DB::connection('mysql_exam_system')->rollback();
            Log::error($ex->getMessage());
            return false;
        }
    }

    private function userDataFormatter(&$items) {
        foreach ($items as $key => &$item) {
            switch ($key) {
                case 'mobile':
                    $item = $item === '无' ? '' : $item;
                    break;
            }
        }
    }
}
