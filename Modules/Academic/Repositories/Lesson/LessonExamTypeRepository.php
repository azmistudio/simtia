<?php

namespace Modules\Academic\Repositories\Lesson;

use Illuminate\Http\Request;

interface LessonExamTypeRepository
{
	public function create(Request $request, $subject);
	public function update(Request $request, $subject);
	public function showIn($params);
	public function data(Request $request);
	public function destroy($id, $subject);
	public function list($id, $aspect_id);
	public function combobox($id);
	public function reportAssessment($lesson_id, $grade_id, $score_aspect_id, $employee_id);
	
}