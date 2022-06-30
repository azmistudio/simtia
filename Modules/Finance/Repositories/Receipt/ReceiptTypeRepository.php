<?php

namespace Modules\Finance\Repositories\Receipt;

use Illuminate\Http\Request;

interface ReceiptTypeRepository
{
	public function create(Request $request, $subject);
	public function update(Request $request, $subject);
	public function data(Request $request);
	public function destroy($id, $subject);
	public function combobox($id, $department_id);
	public function combogrid(Request $request);
	public function combogridPayment(Request $request);
	public function search($category, $department_id);
}