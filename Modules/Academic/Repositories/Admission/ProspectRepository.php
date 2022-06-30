<?php

namespace Modules\Academic\Repositories\Admission;

use Illuminate\Http\Request;

interface ProspectRepository
{
	public function create(Request $request, $subject);
	public function update(Request $request, $subject);
	public function showIn($params);
	public function data(Request $request);
	public function destroy($id, $subject);
	public function dataView(Request $request);
	public function combogrid(Request $request);
}