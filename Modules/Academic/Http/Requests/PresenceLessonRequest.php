<?php

namespace Modules\Academic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PresenceLessonRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'class_id' => 'required|int',
            'semester_id' => 'required|int',
            'lesson_id' => 'required',
            'employee_id' => 'required|int',
            'subject' => 'required',
            'teacher_type' => 'required|int',
            'lesson_schedule_id' => 'required|int',
            'date' => 'required|array|min:2'
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
