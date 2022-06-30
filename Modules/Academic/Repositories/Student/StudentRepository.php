<?php

namespace Modules\Academic\Repositories\Student;

use Illuminate\Http\Request;

interface StudentRepository
{
	public function update(Request $request, $subject);
	public function showIn($params);
	public function data(Request $request);
	public function list(Request $request);
	public function destroy($id, $subject);
	
	public function comboGridPlacement(Request $request);
	public function comboGrid(Request $request);
	public function dataRoom(Request $request);
}