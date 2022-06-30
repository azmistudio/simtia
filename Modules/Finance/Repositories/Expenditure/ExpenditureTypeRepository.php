<?php

namespace Modules\Finance\Repositories\Expenditure;

use Illuminate\Http\Request;

interface ExpenditureTypeRepository
{
	public function create(Request $request, $subject);
	public function update(Request $request, $subject);
	public function data(Request $request);
	public function destroy($id, $subject);
	public function combobox($id, $department_id);
	public function combogrid(Request $request);
}