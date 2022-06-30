<?php

namespace Modules\Academic\Repositories\Exam;

use Illuminate\Http\Request;

interface ExamReportCommentRepository
{
	public function upsert(Request $request, $subject);
	public function destroy(Request $request, $subject);

	public function createLesson(Request $request, $subject);
	public function updateLesson(Request $request, $subject);
	public function destroyLesson($id, $subject);

	public function createSocial(Request $request, $subject);
	public function updateSocial(Request $request, $subject);
	public function destroySocial($id, $subject);
}