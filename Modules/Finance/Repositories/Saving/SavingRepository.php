<?php

namespace Modules\Finance\Repositories\Saving;

use Illuminate\Http\Request;

interface SavingRepository
{
	public function create(Request $request, $subject);
	public function update(Request $request, $subject);
	public function show($id);
	public function info(Request $request, $is_employee);
	public function data(Request $request, $is_employee);
	public function dataSaving($is_employee, $person_id, $bookyear_id, $saving_type);
	public function totalSaving($is_employee, $person_id, $bookyear_id, $saving_type);
	public function getBalance($saving_id, $person_id, $is_employee);
	public function dataPayment(Request $request);
	public function totalPaymentReceipt($receipt_id, $student_id, $is_employee);
	public function lastPaymentReceipt($receipt_id, $student_id, $is_employee);

	public function dataSavingClass(Request $request);
	public function dataSavingDetail($is_employee, $person_id, $bookyear_id, $start_date, $end_date);
	public function dataSavingDetailInfo($is_employee, $person_id, $bookyear_id, $start_date, $end_date, $saving_id);
	public function dataSavingRecap($is_employee, $department_id, $employee_id, $start_date, $end_date);
	public function dataSavingDetailTrx($is_employee, $department_id, $saving_id, $employee_id, $start_date, $end_date, $type);
	
}