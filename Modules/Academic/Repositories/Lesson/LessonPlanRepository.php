<?php

namespace Modules\Academic\Repositories\Lesson;

use Illuminate\Http\Request;

interface LessonPlanRepository
{
	public function create(Request $request, $subject);
	public function update(Request $request, $subject);
	public function showIn($params);
	public function data(Request $request);
	public function destroy($id, $subject);
	public function combobox(Request $request);
	
	public function planClassData(Request $request);
	public function planClassGraph($semester_id, $lesson_exam_id, $lesson_plan_id, $lesson_id, $grade_id);
	public function planStudentGraph($semester_id, $class_id, $lesson_exam_id, $lesson_plan_id, $lesson_id, $grade_id);
}