<?php

namespace App\Http\Requests\Api\V1\ExamSystem;

use App\Models\ExamSystem\UserEducation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserEducationUpdateFormRequest extends FormRequest
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
            'id' => 'required|integer|min:1|exists:App\Models\ExamSystem\UserEducation,id',
            'user_id' => 'required|integer|min:1|exists:App\Models\ExamSystem\User,id',
            'diploma_number' => 'required|string|min:6|max:50|unique:App\Models\ExamSystem\UserEducation,diploma_number,'.$this->id,
            'approval_number' => 'required|string|min:6|max:50',
            'student_id' => 'required|string|min:4|max:30',
            'education_level' => Rule::in(UserEducation::EDUCATION_LEVELS),
            'major' => 'required|string|min:1|max:50',
            'graduation' => 'date',
            'school_system' => Rule::in(UserEducation::SCHOOL_SYSTEMS),
            'graduated_school' => 'required|string|min:1|max:50',
            'diploma_description' => 'required|string|min:1|max:255',
            'diploma_image_front' => 'required|string|max:255',
            'diploma_image_back' => 'required|string|max:255',
        ];
    }
}
