<?php


namespace App\Imports\ExamSystem;

use App\Models\ExamSystem\Category;
use App\Models\ExamSystem\Classes;
use App\Models\ExamSystem\Enterprise;
use App\Models\ExamSystem\EnterpriseManager;
use App\Models\ExamSystem\Register;
use App\Models\ExamSystem\User;
use Illuminate\Support\Collection;

class RegisterFirstSheetImport extends BaseSheetImport
{
    protected $classId = 0;
    protected $class = null;
    public function __construct(int $userId, int $classId)
    {
        parent::__construct($userId);
        $this->classId = $classId;
        $this->class = Classes::find($classId);
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

    private function getRegisterTypeValue($typeName) {
        foreach (Register::TYPES as $type) {
            if ($type['name'] === $typeName) {
                return $type['id'];
            }
        }
        return 0;
    }
    private function import($row) {

        $user = $this->importUser($row);
        if (!isset($user['id'])){
            return;
        }
        $enterpriseManager = $this->importEnterpriseManager($row);
        $enterprise = $this->importEnterprise($row);
        EnterpriseManager::createRelation($enterprise['id'], $enterpriseManager['id'], $this->userId);

        $registerData = Register::firstWhere(['class_id' => $this->classId, 'user_id' => $user['id']]);
        if ($registerData) {
            return;
        }
        $register = [
            'class_id' => $this->classId,
            'user_id' => $user['id'],
            'category_id' => $this->class['category_id'],
            'type' => $row[$this->nameIndexMap['申请类别']],
            'health_status' => $row[$this->nameIndexMap['健康状况']],
            'education' => $row[$this->nameIndexMap['文化程度']],
            'majors' => $row[$this->nameIndexMap['专业']],
            'profession' => $row[$this->nameIndexMap['职务（职业）']],
            'job_title' => $row[$this->nameIndexMap['职称']],
            'job_title_majors' => $row[$this->nameIndexMap['职称专业']],
            'enterprise_id' => isset($enterprise['id']) ? $enterprise['id'] : 0,
            'enterprise_manager' => isset($enterpriseManager['id']) ? $enterpriseManager['id'] : 0,
            'admin_id' => $this->userId,
        ];
        $this->registerDataFormatter($register);
        $result = $this->importRegister($register);
    }
    private function importUser($row): array
    {
        // 身份证不存在
        $id_card = $row[$this->nameIndexMap['身份证号码']];
        if (!$id_card) {
            return [];
        }
        // 记录已存在
        $userData = User::firstWhere(['id_card' => $id_card]);
        if ($userData) {
            return $userData->toArray();
        }
        $sex = substr($id_card, -2, 1) % 2 === 1 ? 1 : 2;
        $password = bcrypt(substr($id_card, -6));
        $user = [
            'true_name' => $row[$this->nameIndexMap['姓名']],
            'id_card' => $id_card,
            'sex' => $sex,
            'password' => $password,
            'mobile' => intval($row[$this->nameIndexMap['联系电话']]),
            'nationality' => $row[$this->nameIndexMap['民族']],
            'id_card_address' => $row[$this->nameIndexMap['地址（身份证住址）']],
            'admin_id' => $this->userId,
        ];
        $this->userDataFormatter($user);
        $result = User::create($user);
        return $result ? $result->toArray() : [];
    }
    private function importEnterpriseManager($row): array
    {
        // 身份证不存在
        $id_card = $row[$this->nameIndexMap['企业管理人员身份证号码']];
        if (!$id_card) {
            return [];
        }
        // 记录已存在
        $userData = User::firstWhere(['id_card' => $id_card]);
        if ($userData) {
            return $userData->toArray();
        }
        // 记录不存在
        $sex = substr($id_card, -2, 1) % 2 === 1 ? 1 : 2;
        $password = bcrypt(substr($id_card, -6));
        $enterpriseManager = [
            'true_name' => $row[$this->nameIndexMap['企业管理人员']],
            'id_card' => $id_card,
            'sex' => $sex,
            'password' => $password,
            'mobile' => $row[$this->nameIndexMap['企业管理人员联系电话']],
            'admin_id' => $this->userId,
        ];
        $this->userDataFormatter($enterpriseManager);
        $result = User::create($enterpriseManager);
        return $result ? $result->toArray() : [];
    }
    private function importEnterprise($row): array
    {
        // 统一企业信用代码不存在
        $credit_code = $row[$this->nameIndexMap['统一社会信用代码']];
        if (!$credit_code) {
            return [];
        }
        // 记录已存在
        $enterpriseData = Enterprise::firstWhere(['credit_code' => $credit_code]);
        if ($enterpriseData) {
            return $enterpriseData->toArray();
        }
        // 记录不存在
        $category_name = $row[$this->nameIndexMap['企业类型']];
        $category = Category::FirstWhere(['name' => $category_name, 'type' => Category::ENTERPRISE_TYPE]);
        $category = $category ? $category->toArray() : [];
        $enterprise = [
            'name' => $row[$this->nameIndexMap['企业名称']],
            'credit_code' => $credit_code,
            'category_id' => $category['id'] ?? 0,
            'business_license_residence' => $row[$this->nameIndexMap['企业地址（营业执照住所）']],
            'admin_id' => $this->userId,
        ];
        $result = Enterprise::create($enterprise);
        return $result ? $result->toArray() : [];
    }

    private function importRegister($register): array
    {
        $result = Register::create($register);
        return $result ? $result->toArray() : [];
    }

    private function registerDataFormatter(&$items) {
        foreach ($items as $key => &$item) {
            switch ($key) {
                case 'type':
                    $item = $this->getRegisterTypeValue($item);
                    break;
                case 'health_status':
                case 'education':
                case 'majors':
                case 'profession':
                case 'job_title':
                case 'job_title_majors':
                    $item = $item === '无' ? '' : $item;
                    break;
            }
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
