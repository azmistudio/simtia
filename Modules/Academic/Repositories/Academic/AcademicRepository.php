<?php

namespace Modules\Academic\Repositories\Academic;

use Illuminate\Http\Request;

interface AcademicRepository
{
	// grade
	public function createGrade(Request $request, $subject);
	public function updateGrade(Request $request, $subject);
	public function dataGrade(Request $request);
	public function destroyGrade($id, $subject);
	public function comboboxGrade($id);
	public function combogridGrade(Request $request);

	// school year
	public function createSchoolYear(Request $request, $subject);
	public function updateSchoolYear(Request $request, $subject);
	public function dataSchoolYear(Request $request);
	public function destroySchoolYear($id, $subject);
	public function comboboxSchoolYear($id);
	public function combogridSchoolYear(Request $request);

	// semester
	public function createSemester(Request $request, $subject);
	public function updateSemester(Request $request, $subject);
	public function dataSemester(Request $request);
	public function destroySemester($id, $subject);
	public function comboboxSemester($id);
	public function combogridSemester(Request $request);
	public function reportAssessmentSemester($dept_id, $lesson_id, $student_id);

	// class
	public function createClass(Request $request, $subject);
	public function updateClass(Request $request, $subject);
	public function showClassIn($params);
	public function quotaClass($id);
	public function dataClass(Request $request);
	public function destroyClass($id, $subject);
	public function combogridClass(Request $request);
	public function combogridClassView(Request $request);
	public function comboGridClassPlacement(Request $request);
	public function comboGridClassStudent(Request $request);
	public function comboboxClass($grade_id, $schoolyear_id);
	public function comboGridClassOnly(Request $request);
}