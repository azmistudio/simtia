<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'employee_id' => 'nullable|int',
            'gender' => 'required|int',
            'pob' => 'required|string',
            'dob' => 'required|string',
            'tribe' => 'required|int',
            'section' => 'required|int',
            'marital' => 'required|int',
            'national_id' => 'required',
            'mobile' => 'required',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png',
            'work_start' => 'required',
            'email' => 'required|email',
            'address' => 'required',
            'photo' => 'nullable|file|image|mimes:jpeg,png,jpg|max:2048'
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
