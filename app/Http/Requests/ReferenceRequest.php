<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReferenceRequest extends FormRequest
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
            'code' => 'nullable|string',
            'name' => 'nullable|string',
            'category' => 'nullable|string',
            'remark' => 'nullable|string',
            'order' => 'nullable|int',
            'parent' => 'nullable|int',
        ];
    }
}
