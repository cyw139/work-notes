<?php

namespace App\Http\Requests\Api\V1\ExamSystem;

use App\Models\ExamSystem\Category;
use App\Models\Kspx\TreeBaseModel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryFormRequest extends FormRequest
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
                'exists:App\Models\ExamSystem\Category,id'
            ],
            'type' => [
                'required',
                'integer',
                Rule::in(array_keys(Category::TYPES)),
            ],
            'name' => [
                'required',
                'string',
                'max: 100',
                Rule::unique('App\Models\ExamSystem\Category')
                    ->where(function($query) {
                    $pid = $this->request->get('pid');
                    $type = $this->request->get('type');
                    $name = $this->request->get('name');
                    return $query->where(['type' => $type, 'pid' => $pid, 'name' => $name]);
                })
            ],
            'sibling' => [
                'nullable',
                'integer',
                'min:1',
                'exists:App\Models\ExamSystem\Category,id'
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
            'id.required' => '缺少ID',
            'name.required' => '缺少类别名称',
            'name.unique' => '父节点下的子节点名称必须唯一，不能重复',
            'name.max' => '标题长度不超过100个字符',
        ];
    }
}
