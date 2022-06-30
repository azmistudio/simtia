<?php

namespace Modules\Academic\Repositories\Lesson;

use Illuminate\Http\Request;

interface LessonScheduleInfoRepository
{
	public function create(Request $request, $subject);
	public function update(Request $request, $subject);
	public function show($search, $is_one);
	public function data(Request $request, $id);
	public function destroy(Request $request, $subject);
	public function list($id);
	public function combogrid(Request $request);
}