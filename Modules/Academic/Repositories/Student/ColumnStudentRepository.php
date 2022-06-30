<?php

namespace Modules\Academic\Repositories\Student;

use Illuminate\Http\Request;

interface ColumnStudentRepository
{
	public function create(Request $request, $id);
	public function destroy($id, $subject);
}