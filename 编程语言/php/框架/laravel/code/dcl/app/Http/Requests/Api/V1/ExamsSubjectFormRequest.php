<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Kspx\ExamsModule;
use App\Models\Kspx\ExamsSubject;
use App\Models\Kspx\TreeBaseModel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExamsSubjectFormRequest extends FormRequest
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
            'pid' => [
                'nullable',
                'integer',
                'min:1',
                'exists:App\Models\Kspx\ExamsSubject,exams_subject_id'
            ],
            'exams_module_id' => [
                'nullable',
                'integer',
                'min:-1',
                'exists:App\Models\Kspx\ExamsModule,exams_module_id'
            ],
            'title' => [
                'required',
                'string',
                'max: 255'
            ],
            'sibling' => [
                'nullable',
                'integer',
                'min:1',
                'exists:App\Models\Kspx\ExamsSubject,exams_subject_id'
            ],
            'position' => [
                Rule::in(TreeBaseModel::POSITIONS)
            ]
        ];
    }

    /**
     * 获取已定义验证规则的错误消息
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'exams_subject_id.required' => '缺少ID',
            'exams_subject_id.integer' => 'ID必须为数字',
            'exams_subject_title.required' => '缺少标题',
            'exams_subject_title.max' => '标题长度不超过255个字符',
        ];
    }
}
