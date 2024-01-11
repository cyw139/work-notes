<?php

namespace App\Http\Requests\Api\V1\FacebookAuto;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileCreateFormRequest extends FormRequest
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
            'ixb_browser_id' => 'required|numeric',
            'fb_account_id' => 'required|numeric',
            'fb_account_name' => 'required|string|min:1|max:50',
            'mobile_area' => 'required|string|min:1|max:10',
            'mobile' => 'required|string|min:1|max:20',
            'gender' => 'required|string|min:1|max:20',
            'email' => 'required|email|min:1|max:50',
            'birth_date' => 'required|string|min:1|max:20',
            'birth_year' => 'required|string|min:1|max:20',
        ];
    }
}
