<?php

namespace Modules\Academic\Repositories\Presence;

use Illuminate\Http\Request;

interface PresenceDailyRepository
{
	public function create(Request $request, $subject);
	public function update(Request $request, $subject);
	public function data(Request $request);
	public function list(Request $request);
	public function destroy($id, $subject);
	public function reportData($start_date, $end_date, $student_id);
	public function reportPresenceDailyClass(Request $request);
	public function reportPresenceDailyAbsent(Request $request);
	public function reportPresenceStat(Request $request);
	public function reportPresenceStatClass(Request $request);
}