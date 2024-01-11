<?php

namespace App\Http\Requests\Api\V1\ExamSystem;

use App\Models\ExamSystem\Question;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaperFormRequest extends FormRequest
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
            'name' => [
                'string',
                'min:0',
                'max:100'
            ],
            'description' => [
                'string',
                'min:0',
                'max:255'
            ],
            'category_id' => [
                'required',
                'integer',
                'min:1',
                'exists:App\Models\ExamSystem\Category,id'
            ],
            'reply_time' => [
                'required',
                'integer',
                'min:0',
            ],
            'start_time' => [
                'required',
                'date'
            ],
            'end_time' => [
                'required',
                'date'
            ],
            'exams_limit' => [
                'required',
                'integer',
                'min:0'
            ],
            'paper_options' => [
                'required',
                'array'
            ],
            'paper_options.*.question_type' => [
                'required',
                Rule::in(array_keys(Question::TYPES)),
                'distinct'
            ],
            'paper_options.*.score' => [
                'required',
                'integer',
                'min:1',
                'max:100'
            ],
//            'paper_options.*.question_type_key' => [
//                'required',
//                Rule::in(ExamsQuestion::TYPES),
//                'distinct'
//            ],
            'paper_options.*.count' => [
                'required',
                'integer',
                'min:1',
                'max:500'
            ],
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
            'id.required' => '缺少ID',
            'id.integer' => 'ID必须为数字',
            'name.required' => '缺少标题',
            'name.max' => '标题长度不超过255个字符',
        ];
    }
}
