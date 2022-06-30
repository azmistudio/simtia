<?php

namespace Modules\Finance\Repositories\Saving;

use Illuminate\Http\Request;

interface SavingTypeRepository
{
	public function create(Request $request, $subject);
	public function update(Request $request, $subject);
	public function data(Request $request, $is_employee);
	public function destroy($id, $subject);
	public function combogrid(Request $request);
	public function combobox(Request $request);
}