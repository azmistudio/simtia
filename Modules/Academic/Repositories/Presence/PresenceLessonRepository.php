<?php

namespace Modules\Academic\Repositories\Presence;

use Illuminate\Http\Request;

interface PresenceLessonRepository
{
	public function create(Request $request, $subject);
	public function update(Request $request, $subject);
	public function data(Request $request);
	public function list(Request $request);
	public function destroy($id, $subject);
	public function combogrid(Request $request);
	
	public function reportData(Request $request);
	public function reportDataInfo(Request $request);
	public function reportPresenceLessonClass(Request $request);
	public function reportPresenceLessonTeacher(Request $request);
	public function reportPresenceLessonAbsent(Request $request);
	public function reportPresenceLessonReflection(Request $request);
	public function reportPresenceLessonStat(Request $request);
	public function reportPresenceLessonStatClass(Request $request);
}