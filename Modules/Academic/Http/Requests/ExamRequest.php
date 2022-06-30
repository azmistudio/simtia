<?php

namespace Modules\Academic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExamRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'lesson_id' => 'required',
            'class_id' => 'required|int',
            'semester_id' => 'required|int',
            'employee_id' => 'required|int',
            'status_id' => 'required|int',
            'score_aspect_id' => 'required|int',
            'lesson_exam_id' => 'required|int',
            'code' => 'required|string',
            'date' => 'required',
            'assessment_id' => 'required',
            'lesson_plan_id' => 'required|int',
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
