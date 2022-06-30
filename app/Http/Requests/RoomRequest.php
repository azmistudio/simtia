<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoomRequest extends FormRequest
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
            //
            'department_id' => 'required|int',
            'name' => 'required|string',
            'gender' => 'required|int',
            'capacity' => 'required|int',
            'employee_id' => 'required|int',
            'is_employee' => 'required|int',
        ];
    }
}
