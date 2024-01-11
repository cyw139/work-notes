<?php

namespace App\Http\Requests\Api\V1\ExamSystem;

use Illuminate\Foundation\Http\FormRequest;

class UserFormRequest extends FormRequest
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
            'id' => 'integer|min:1',
            'exams_module_id' => 'required|max:255',
            'title' => 'required|max:255',
        ];
    }
}
