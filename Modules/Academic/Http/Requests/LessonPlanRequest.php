<?php

namespace Modules\Academic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LessonPlanRequest extends FormRequest
{
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
            'grade_id' => 'required|int',
            'semester_id' => 'required|int',
            'lesson_id' => 'required|int',
            'code' => 'required|string',
            'subject' => 'required|string',
            'lesson_plan_file.*' => 'nullable|mimes:jpg,bmp,png,pdf,doc,docx,xls,xlsx,ppt,pptx|max:10024'
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
