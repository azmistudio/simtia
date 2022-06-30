<?php

namespace Modules\Academic\Repositories\Exam;

use Illuminate\Http\Request;

interface ExamReportRepository
{
	public function create(Request $request, $subject);
	public function update(Request $request, $subject);
	public function data(Request $request);
	public function destroy($id, $subject);

	public function list(Request $request, $array);
	public function comboGrid(Request $request);
	
	public function getLessons(Request $request);
	public function getSocials(Request $request);


	public function reportLeggerStudents($schoolyear_id, $class_id);
	public function reportLeggerLessonGet($class_id, $semester_id);
	public function reportLeggerLessonAll($lesson_ids, $class_id, $semester_id);
	public function reportLeggerLesson($lesson_id, $class_id, $semester_id);
	public function reportLeggerLessonData($lesson_id, $class_id, $semester_id, $student_id, $score_aspect_id);
	public function reportLeggerLessonAllScores($class_id, $semester_id);

	public function reportLeggerClassGet($class_id, $semester_id, $student_id, $lesson_id);
	public function reportLeggerClassAll($lesson_id, $arr_student);
	public function reportLeggerClassScoreAspect($arr_aspect);
	public function reportLeggerClassScoreAspectOpt();
	public function reportLeggerClassScore($student_id, $lesson_id, $semester_id, $class_id, $score_aspect_id);
}