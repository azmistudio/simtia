<?php

namespace Modules\Finance\Repositories\Reference;

use Illuminate\Http\Request;

interface CodeRepository
{
	public function create(Request $request, $subject);
	public function update(Request $request, $subject);
	public function data(Request $request);
	public function destroy($id, $subject);
	public function dataGrid(Request $request);
	public function totalBalance($position, $bookyear_id);
	public function combobox($id);
	public function combogrid(Request $request);
	public function listSummary($startdate, $lastdate, $bookyear_id);
	public function list($balance);
	public function listBalance($bookyear_id, $lastdate);
	public function listProfitLoss($category_id, $startdate, $lastdate, $bookyear_id);
	public function accountBalance($code, $lastdate, $department_id);
	public function getBalance($account_id, $department_id, $date);
	public function getBalanceSub($account_id, $department_id, $date);
}