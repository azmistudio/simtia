<?php

namespace Modules\Academic\Repositories\Academic;

use Illuminate\Http\Request;

interface ColumnOptionRepository
{
	public function create(Request $request, $subject);
	public function update(Request $request, $subject);
	public function data(Request $request, $id);
	public function destroy(Request $request, $subject);
	public function datalist($id);
}