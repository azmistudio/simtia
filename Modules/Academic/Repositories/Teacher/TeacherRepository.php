<?php

namespace Modules\Academic\Repositories\Teacher;

use Illuminate\Http\Request;

interface TeacherRepository
{
	public function create(Request $request, $subject);
	public function update(Request $request, $subject);
	public function data(Request $request);
	public function destroy($id, $subject);

	public function combogrid(Request $request);
	public function combogridGroup(Request $request);
	public function list($id, $department_id);
	public function reportAssessmentTeacher($lesson_id, $class_id);
}