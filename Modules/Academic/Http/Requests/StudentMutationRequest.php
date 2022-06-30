<?php

namespace Modules\Academic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentMutationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'students' => 'required',
            'department_id' => 'required|int',
            'department_id_dst' => 'required|int',
            'grade_id' => 'required|int',
            'class_id' => 'required|int',
            'class_id_dst' => 'required|int',
            'date' => 'required',
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
