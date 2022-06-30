<?php

namespace Modules\Finance\Repositories\Receipt;

use Illuminate\Http\Request;

interface ReceiptVoluntaryRepository
{
	public function create(Request $request, $subject);
	public function update(Request $request, $subject);
	public function show($id);
	public function data(Request $request);

	public function dataPayment($is_prospect, $student_id);
	public function totalPayment($is_prospect, $student_id);

	public function dataPaymentStudent(Request $request);
	public function totalPaymentReceipt($receipt_id, $student_id, $is_prospect);
	public function lastPaymentReceipt($receipt_id, $student_id, $is_prospect);
	
	public function dataPaymentClass(Request $request);
	public function maxInstallment(Request $request);
	public function paymentClass($bookyear_id, $department_id, $class_id, $is_prospect);

	public function dataPaymentProspectGroup(Request $request);
	public function paymentProspectGroup($department_id, $prospect_group_id);
	public function listPaymentProspectGroup($department_id, $prospect_group_id, $prospect_student_id);
	public function maxInstallmentProspectGroup(Request $request);

	public function dataRecapTotal($bookyear_id, $department_id, $category, $start_date, $end_date, $employee_id);
	public function dataRecapTotalDetail($bookyear_id, $department_id, $category, $start_date, $end_date, $employee_id);
	public function dataRecapDaily($bookyear_id, $department_id, $category, $start_date, $end_date, $employee_id);
	public function dataRecapDailyTrans($bookyear_id, $department_id, $type_id, $trans_date, $employee_id);
	public function dataRecapTransDetail($bookyear_id, $department_id, $receipt_category_id, $trans_date, $type_id, $employee_id);

	public function dataReceiptJournal(Request $request);
}