<?php

namespace Modules\Academic\Repositories\Exam;

use Illuminate\Http\Request;

interface ExamRepository
{
	public function create(Request $request, $subject);
	public function update(Request $request, $subject);
	public function data(Request $request);
	public function destroy($id, $subject);
	public function dataScore(Request $request);
	public function dataScoreWeight(Request $request);
	public function comboGrid(Request $request);
	public function comboGridExam(Request $request);
	public function reportAssessmentScores($lesson_id, $student_id, $class_id, $semester_id, $lesson_exam_id, $assessment_id);
	public function reportExams($lesson_id, $class_id, $semester_id);
	public function reportExamDates($lesson_id, $class_id, $semester_id);
	public function reportExamCount($lesson_id, $class_id, $semester_id);
}