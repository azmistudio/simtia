<?php

namespace Modules\Academic\Repositories\Admission;

use Illuminate\Http\Request;

interface PlacementRepository
{
	public function data(Request $request);
	public function destroy($id, $subject);
}