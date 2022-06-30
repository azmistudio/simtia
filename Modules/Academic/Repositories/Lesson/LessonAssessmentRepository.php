<?php

namespace Modules\Academic\Repositories\Lesson;

use Illuminate\Http\Request;

interface LessonAssessmentRepository
{
	public function create(Request $request, $subject);
	public function update(Request $request, $subject);
	public function show($employee_id, $grade_id, $lesson_id, $score_aspect_id);
	public function dataGroupIn($employeeIdArray, $gradeIdArray, $lessonIdArray, $scoreAspectIdArray);
	public function showIn($employeeIdArray, $gradeIdArray, $lessonIdArray, $scoreAspectIdArray);
	public function data(Request $request);
	public function destroy($employee_id, $grade_id, $lesson_id, $score_aspect_id, $subject);
	public function combobox(Request $request);
}