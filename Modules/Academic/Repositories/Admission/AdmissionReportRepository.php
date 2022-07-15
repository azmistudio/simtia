<?php

namespace Modules\Academic\Repositories\Admission;

use Illuminate\Http\Request;

interface AdmissionReportRepository
{
	public function admissionStatData(Request $request);
	public function admissionStatDataDetail(Request $request);
	public function admissionProspectData(Request $request);
}