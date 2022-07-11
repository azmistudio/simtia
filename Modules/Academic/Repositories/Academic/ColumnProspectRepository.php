<?php

namespace Modules\Academic\Repositories\Academic;

use Illuminate\Http\Request;

interface ColumnProspectRepository
{
	public function create(Request $request, $id);
	public function destroy($id, $subject);
}