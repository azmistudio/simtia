<?php

namespace Modules\Academic\Repositories\Lesson;

use Illuminate\Http\Request;

interface LessonDataRepository
{
	public function create(Request $request, $subject);
	public function update(Request $request, $subject);
	public function showIn($params);
	public function data(Request $request);
	public function destroy($id, $subject);
	public function combogrid(Request $request);
	public function combogridTeacher(Request $request);
	public function combobox($id);
}