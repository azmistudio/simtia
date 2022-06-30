<?php

namespace Modules\Academic\Repositories\Student;

use Illuminate\Http\Request;

interface StudentReportRepository
{
	public function studentStatData(Request $request);
	public function studentStatDataDetail(Request $request);
	public function studentMutationStatData($start, $end, $deptid);
	public function studentMutationStatDataDetail($start, $end, $deptid, $mutation_id);
	public function studentMutationGraph($start, $end, $deptid, $deptname);
}