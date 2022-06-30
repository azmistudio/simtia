<?php

namespace Modules\Academic\Repositories\Lesson;

use Illuminate\Http\Request;

interface LessonRepository
{
	// score aspect
	public function createScoreAspect(Request $request, $subject);
	public function updateScoreAspect(Request $request, $subject);
	public function dataScoreAspect(Request $request);
	public function destroyScoreAspect($id, $subject);

	// lesson group
	public function createLessonGroup(Request $request, $subject);
	public function updateLessonGroup(Request $request, $subject);
	public function dataLessonGroup(Request $request);
	public function destroyLessonGroup($id, $subject);
}