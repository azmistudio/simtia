<?php

namespace Modules\Academic\Repositories\Academic;

use Illuminate\Http\Request;

interface ColumnRepository
{
	public function create(Request $request, $subject);
	public function update(Request $request, $subject);
	public function data(Request $request);
	public function destroy($id, $subject);
}