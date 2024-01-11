<?php

namespace App\Http\Requests\Api\V1\ExamSystem;

use App\Models\ExamSystem\UserEnterprisePosition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserEnterprisePositionUpdateFormRequest extends FormRequest
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
            'id' => 'required|integer|min:1|exists:App\Models\ExamSystem\UserEnterprisePosition,id',
            'user_id' => 'required|integer|min:1|exists:App\Models\ExamSystem\User,id',
            'enterprise_id' => 'required|integer|min:1|exists:App\Models\ExamSystem\Enterprise,id',
            'entry_date' => 'date',
            'job_title' => 'nullable|string|max:20',
            'work_type' => 'nullable|string|max:20',
            'job' => 'nullable|string|max:20',
            'section' => 'nullable|string|max:40',
            'job_situation' => Rule::in(UserEnterprisePosition::JOB_STATUS),
        ];
    }
}
