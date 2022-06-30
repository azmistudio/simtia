<?php

namespace Modules\Finance\Repositories\Receipt;

use Illuminate\Http\Request;

interface PaymentMajorRepository
{
	public function create(Request $request, $subject);
	public function update(Request $request, $subject);
	public function data(Request $request);
	public function destroy($id, $subject);
	public function dataStudent(Request $request);
	public function dataPaymentStudent(Request $request);
	
	public function dataRecapStudent($bookyear_id, $department_id, $grade_id, $class_id);
	public function recapStudentArrear($bookyear_id, $receipt_id, $student_id);
	public function recapStudentArrearLast($bookyear_id, $receipt_id, $student_id);
}