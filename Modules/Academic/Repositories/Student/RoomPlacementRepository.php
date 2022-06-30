<?php

namespace Modules\Academic\Repositories\Student;

use Illuminate\Http\Request;

interface RoomPlacementRepository
{
	public function create(Request $request, $subject);
	public function data(Request $request);
}