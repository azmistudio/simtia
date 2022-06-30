<?php

namespace Modules\Academic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentRequest extends FormRequest
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
            'gender' => 'required|int',
            'pob' => 'required|string',
            'dob' => 'required',
            'tribe' => 'required|int',
            'student_status' => 'required|int',
            'economic' => 'required|int',
            'citizen' => 'required|int',
            'child_no' => 'required|int',
            'father' => 'required|string',
            'mother' => 'required|string',
            'father_status' => 'required|int',
            'mother_status' => 'required|int',
            'father_pob' => 'required|string',
            'mother_pob' => 'required|string',
            'father_dob' => 'required',
            'mother_dob' => 'required',
            'father_education' => 'required|int',
            'mother_education' => 'required|int',
            'father_job' => 'required|int',
            'email' => 'nullable|email',
            'father_email' => 'nullable|email',
            'mother_email' => 'nullable|email',
            'address' => 'required|string',
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
