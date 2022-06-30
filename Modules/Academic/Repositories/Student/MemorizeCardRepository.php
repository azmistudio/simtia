<?php

namespace Modules\Academic\Repositories\Student;

use Illuminate\Http\Request;

interface MemorizeCardRepository
{
	public function create(Request $request, $subject);
	public function data(Request $request);
	public function dataCard(Request $request);
	public function destroy($id, $subject);
}