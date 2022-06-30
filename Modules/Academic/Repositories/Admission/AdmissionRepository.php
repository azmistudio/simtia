<?php

namespace Modules\Academic\Repositories\Admission;

use Illuminate\Http\Request;

interface AdmissionRepository
{
	public function create(Request $request, $subject);
	public function update(Request $request, $subject);
	public function data(Request $request);
	public function destroy($id, $subject);
	public function combogrid(Request $request);
}