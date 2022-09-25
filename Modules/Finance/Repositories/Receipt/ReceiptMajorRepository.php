<?php

namespace Modules\Finance\Repositories\Receipt;

use Illuminate\Http\Request;

interface ReceiptMajorRepository
{
	public function create(Request $request, $subject);
	public function update(Request $request, $subject);
	public function show($category_id, $id);
	public function data(Request $request);
	
	public function dataInstalment($payment_major_id);
	public function totalInstalment($payment_major_id);
	public function totalInstalments($payment_major_id);
	public function maxInstallment(Request $request);
	
	public function dataPaymentClass(Request $request);
	public function paymentClass($bookyear_id, $class_id, $is_paid, $period);
	public function listPayment($bookyear_id, $student_id, $is_paid, $major_id);
	
	public function totalPaymentReceipt($major_id);
	public function lastPaymentReceipt($major_id);
	
	public function dataPaymentClassArrear(Request $request);
	public function paymentClassArrear($bookyear_id, $receipt_type_id, $duration, $date_delay, $period);
	public function paymentClassDelay($bookyear_id, $receipt_type_id, $duration, $date_delay, $student_id, $period);

	public function dataPaymentProspectGroup(Request $request);
	public function paymentProspectGroup($department_id, $category, $prospect_group_id, $is_paid);
	public function listPaymentProspectGroup($department_id, $prospect_student_id, $is_paid);
	public function maxInstallmentProspectGroup(Request $request);

	public function dataPaymentProspectArrear(Request $request);
	public function paymentProspectArrear($department_id, $category_id, $receipt_type_id, $duration, $date_delay);
	public function paymentProspectDelay($department_id, $category_id, $receipt_type_id, $duration, $date_delay, $student_id);

	public function dataRecapTotal($bookyear_id, $department_id, $category, $start_date, $end_date, $employee_id);
	public function dataRecapTotalDetail($bookyear_id, $department_id, $category, $start_date, $end_date, $employee_id);
	public function dataRecapDaily($bookyear_id, $department_id, $category, $start_date, $end_date, $employee_id);
	public function dataRecapDailyTrans($bookyear_id, $department_id, $type_id, $trans_date, $employee_id);
	public function dataRecapTransDetail($bookyear_id, $department_id, $receipt_category_id, $trans_date, $type_id, $employee_id);
	
	public function dataReceiptJournal(Request $request);
	
}