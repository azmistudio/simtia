<?php

namespace Modules\Academic\Repositories\Student;

use Illuminate\Http\Request;

interface StudentAlumniRepository
{
	public function create(Request $request, $subject);
	public function data(Request $request);
	public function destroy($id, $subject);
	public function comboGrid(Request $request);
}