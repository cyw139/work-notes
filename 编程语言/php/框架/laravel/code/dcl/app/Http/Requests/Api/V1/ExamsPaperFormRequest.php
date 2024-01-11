<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Kspx\ExamsModule;
use App\Models\Kspx\ExamsPaper;
use App\Models\Kspx\ExamsQuestion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExamsPaperFormRequest extends FormRequest
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
            //'exams_paper_id' => 'integer|min:1',
            'exams_paper_title' => [
                'string',
                'min:0',
                'max:255'
            ],
            'exams_paper_description' => [
                'string',
                'min:0',
                'max:255'
            ],
            'exams_question_type_id' => [
                Rule::in(array_keys(ExamsQuestion::TYPES))
            ],
            'level' => [
                Rule::in(array_keys(ExamsPaper::LEVELS))
            ],
            'exams_subject_id' => [
                'required',
                'integer',
                'min:1',
                'exists:App\Models\Kspx\ExamsSubject,exams_subject_id'
            ],
            'exams_module_id' => [
                'required',
                'integer',
                'min:-1',
                'exists:App\Models\Kspx\ExamsModule,exams_module_id'
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
            'sort' => [
                'integer',
                'min:1'
            ],
            'exams_limit' => [
                'required',
                'integer',
                'min:0'
            ],
            'assembly_type' => [
                'required',
                'integer',
                Rule::in(array_keys(ExamsPaper::ASSEMBLY_TYPES))
            ],
            'paper_options' => [
                'required',
                'array'
            ],
            'paper_options.*.question_type' => [
                'required',
                Rule::in(array_keys(ExamsQuestion::TYPES)),
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
            'price' => [
                'required',
                'numeric',
                'min:0'
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
            'exams_paper_id.required' => '缺少ID',
            'exams_paper_id.integer' => 'ID必须为数字',
            'exams_paper_title.required' => '缺少标题',
            'exams_paper_title.max' => '标题长度不超过255个字符',
        ];
    }
}
