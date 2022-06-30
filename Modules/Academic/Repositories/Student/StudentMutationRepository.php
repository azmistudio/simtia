<?php

namespace Modules\Academic\Repositories\Student;

use Illuminate\Http\Request;

interface StudentMutationRepository
{
	public function create(Request $request, $subject);
	public function update(Request $request, $subject);
	public function showIn($params);
	public function data(Request $request);
	public function destroy($id, $subject);
	public function comboGrid(Request $request);
}