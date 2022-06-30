<?php

namespace Modules\Academic\Repositories\Admission;

use Illuminate\Http\Request;

interface ConfigRepository
{
	public function create(Request $request, $subject);
	public function update(Request $request, $subject);
	public function data(Request $request);
	public function destroy($id, $subject);
}