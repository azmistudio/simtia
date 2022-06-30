<?php

namespace Modules\Academic\Repositories\Lesson;

use Illuminate\Http\Request;

interface LessonTeachingRepository
{
	public function create(Request $request, $subject);
	public function update(Request $request, $subject);
	public function data(Request $request);
	public function dataRecap(Request $request);
	public function destroy($id, $subject);
	public function comboGrid(Request $request);
	public function comboBox(Request $request);
	public function comboBoxDay(Request $request);
}