<?php

namespace Modules\Finance\Repositories\Receipt;

use Illuminate\Http\Request;

interface ReceiptOtherRepository
{
	public function create(Request $request, $subject);
	public function update(Request $request, $subject);
	public function show($id);
	public function data(Request $request);
	public function dataPayment();
	public function totalPayment();

	public function dataRecapTotal($bookyear_id, $department_id, $category, $start_date, $end_date, $employee_id);
	public function dataRecapTotalDetail($bookyear_id, $department_id, $category, $start_date, $end_date, $employee_id);
	public function dataRecapDaily($bookyear_id, $department_id, $category, $start_date, $end_date, $employee_id);
	public function dataRecapDailyTrans($bookyear_id, $department_id, $type_id, $trans_date, $employee_id);
	public function dataRecapTransDetail($bookyear_id, $department_id, $receipt_category_id, $trans_date, $type_id, $employee_id);
	public function dataReceipt(Request $request);

	public function dataReceiptJournal(Request $request);
}