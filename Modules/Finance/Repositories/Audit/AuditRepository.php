<?php

namespace Modules\Finance\Repositories\Audit;

use Illuminate\Http\Request;

interface AuditRepository
{
	public function data(Request $request);
	public function dataAuditPaymentMajor($source, $bookyear_id, $department_id, $startdate, $enddate);
	public function dataAuditJournalVoucher($source, $bookyear_id, $department_id, $startdate, $enddate);
	public function dataAuditReceiptMajor($source, $bookyear_id, $department_id, $startdate, $enddate);
	public function dataAuditReceiptVoluntary($source, $bookyear_id, $department_id, $startdate, $enddate);
	public function dataAuditReceiptOther($source, $bookyear_id, $department_id, $startdate, $enddate);
	public function dataAuditExpense($source, $bookyear_id, $department_id, $startdate, $enddate);
	public function dataAuditSaving($source, $bookyear_id, $department_id, $startdate, $enddate);
}