<?php

namespace App\Repositories\Department;

use Illuminate\Http\Request;

interface DepartmentRepository
{
	public function create(Request $request, $subject);
	public function update(Request $request, $subject);
	public function data(Request $request);
	public function destroy($id, $subject);
}