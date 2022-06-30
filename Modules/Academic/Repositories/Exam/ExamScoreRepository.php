<?php

namespace Modules\Academic\Repositories\Exam;

use Illuminate\Http\Request;

interface ExamScoreRepository
{
	public function dataScoreStudent($student_id, $lesson_id, $class_id, $semester_id);
	public function dataScoreStudentAvg($student_id, $lesson_id, $class_id, $semester_id);
	public function dataScoreAspect($student_id, $lesson_id);
	public function dataScoreLegger($student_id, $exam_id);
}