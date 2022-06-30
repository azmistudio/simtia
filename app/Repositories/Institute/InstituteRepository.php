<?php

namespace App\Repositories\Institute;

use Illuminate\Http\Request;

interface InstituteRepository
{
	public function create(Request $request, $subject);
	public function update(Request $request, $subject);
	public function data(Request $request);
	public function destroy($id, $subject);
}