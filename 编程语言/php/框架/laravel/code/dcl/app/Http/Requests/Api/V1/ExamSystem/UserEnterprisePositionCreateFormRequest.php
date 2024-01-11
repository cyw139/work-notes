<?php

namespace App\Http\Requests\Api\V1\ExamSystem;

use App\Models\ExamSystem\UserEducation;
use App\Models\ExamSystem\UserEnterprisePosition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserEnterprisePositionCreateFormRequest extends FormRequest
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
            'user_id' => 'required|integer|min:1|exists:App\Models\ExamSystem\User,id',
            'enterprise_id' => 'required|integer|min:1|exists:App\Models\ExamSystem\Enterprise,id',
            'entry_date' => 'date',
            'job_title' => 'string|max:20',
            'work_type' => 'string|max:20',
            'job' => 'string|max:20',
            'section' => 'nullable|string|max:40',
            'job_situation' => Rule::in(UserEnterprisePosition::JOB_STATUS),
        ];
    }
}
