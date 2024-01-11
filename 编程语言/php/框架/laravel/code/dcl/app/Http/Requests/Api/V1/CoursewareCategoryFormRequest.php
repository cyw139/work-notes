<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Kspx\TreeBaseModel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CoursewareCategoryFormRequest extends FormRequest
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
                'exists:App\Models\Kspx\CoursewareCategory,zy_currency_category_id'
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
                'exists:App\Models\Kspx\CoursewareCategory,zy_currency_category_id'
            ],
            'position' => [
                Rule::in(TreeBaseModel::POSITIONS)
            ]
        ];
    }
}
