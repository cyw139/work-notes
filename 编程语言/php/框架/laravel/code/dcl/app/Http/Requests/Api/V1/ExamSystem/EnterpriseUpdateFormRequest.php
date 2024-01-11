<?php

namespace App\Http\Requests\Api\V1\ExamSystem;

use App\Models\ExamSystem\Enterprise;
use App\Models\ExamSystem\UserEducation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EnterpriseUpdateFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|integer|min:1|exists:App\Models\ExamSystem\Enterprise,id',
            'name' => 'required|string|min:1|max:100',
            'credit_code' => 'required|string|min:1|max:100|unique:App\Models\ExamSystem\Enterprise,credit_code,' . $this->id, // 统一社会信用代码
            'business_license_residence' => 'required|string|min:1|max:255', // 营业执照住所
            'category_id' => 'required|integer|min:1|exists:App\Models\ExamSystem\Category,id', // 企业类别
            'legal_representative' => 'required|min:1|exists:App\Models\ExamSystem\User,id', // 法定代表人
            'status' => Rule::in(Enterprise::ENTERPRISE_STATUS), // 经营状态：0未知、1存续、2注销
            'logo' => 'nullable|string|max:100',
            'established_date' => 'date', // 成立日期
            'registered_capital' => 'nullable|string|max:20', // 注册资本
            'registration_number' => 'required|string|min:1|max:30|unique:App\Models\ExamSystem\Enterprise,registration_number,' . $this->id, // 工商注册号
            'paid_in_capital' => 'nullable|string|min:1|max:20', // 实缴资本
            'taxpayer_identification_number' => 'string|min:1|max:30', // 纳税人识别号
            'organization_code' => 'string|min:1|max:30', // 组织机构代码
            'business_license' => 'string|min:1|max:255', // 营业执照
            'operating_period' => 'array', // 营业期限
            'taxpayer_qualification' => 'nullable|string|max:30', // 纳税人资质
            'registration_date' => 'date',
            'type' => 'string|min:1|max:30',
            'trade' => 'nullable|string|min:1|max:30', // 行业
            'staff_size' => 'integer|min:1|max:1000000', // 人员规模
            'insurance_amount' => 'integer|min:0|max:1000000', // 参保人数
            'registration_authority' => 'required|string|min:1|max:50', // 登记机关
            'former_name' => 'nullable|string|min:1|max:30', // 曾用名
            'former_name_period' => 'nullable|array', // 曾用名开始和结束
            'english_name' => 'nullable|string|max:30', // 英文名
            'business_scope' => 'string|min:1|max:1500', // 经营范围
            'phone' => 'required|string|min:8', // 电话
            'email' => 'nullable|email',
            'url' => 'nullable|url',
        ];
    }
}
