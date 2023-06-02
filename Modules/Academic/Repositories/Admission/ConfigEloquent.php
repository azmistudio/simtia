<?php

namespace Modules\Academic\Repositories\Admission;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\ReferenceTrait;
use Modules\Academic\Entities\AdmissionConfig;
use Carbon\Carbon;

class ConfigEloquent implements ConfigRepository
{

	use AuditLogTrait;
	use HelperTrait;
	use ReferenceTrait;

	public function create(Request $request, $subject)
	{
		$payload = $request->all();
		$this->logActivity($request, 0, $subject, 'Tambah');
		return AdmissionConfig::create($payload);
	}

	public function update(Request $request, $subject)
	{
		$payload = Arr::except($request->all(), ['configs','created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah');
        return AdmissionConfig::where('id', $payload['id'])->update($payload);
	}

	public function data(Request $request)
	{
		$param = $this->gridRequest($request,'desc','academic.admission_configs.id');
        $query = AdmissionConfig::join('academic.admissions','academic.admissions.id','=','academic.admission_configs.admission_id')
        			->select('academic.admission_configs.*','academic.admissions.department_id');
        if (auth()->user()->getDepartment->is_all != 1)
		{
			$query = $query->where('department_id', auth()->user()->department_id);
		}
		// result
        $result["total"] = $query->count();
        $result["rows"] = $query->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            $model['admission_id'] = $model->getAdmission->name;
            $model['department_id'] = $model->getAdmission->getDepartment->name;
            return $model->only(['id','admission_id','department_id']);
        });
        return $result;
	}

	public function destroy($id, $subject)
	{
		$request = new Request();
		$this->logActivity($request, $id, $subject, 'Hapus');
		return AdmissionConfig::destroy($id);
	}

	private function logActivity(Request $request, $model_id, $subject, $action)
	{
		if ($action == 'Tambah')
		{
			$data = array(
				'admission_id' => $request->admission_id,
				'donate_code_1' => $request->donate_code_1,
				'donate_name_1' => $request->donate_name_1,
				'donate_code_2' => $request->donate_code_2,
				'donate_name_2' => $request->donate_name_2,
				'exam_code_01' => $request->exam_code_01,
				'exam_name_01' => $request->exam_name_01,
				'exam_code_02' => $request->exam_code_02,
				'exam_name_02' => $request->exam_name_02,
				'exam_code_03' => $request->exam_code_03,
				'exam_name_03' => $request->exam_name_03,
				'exam_code_04' => $request->exam_code_04,
				'exam_name_04' => $request->exam_name_04,
				'exam_code_05' => $request->exam_code_05,
				'exam_name_05' => $request->exam_name_05,
			);
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			$query = AdmissionConfig::find($model_id);
			$before = array(
				'admission_id' => $query->admission_id,
				'donate_code_1' => $query->donate_code_1,
				'donate_name_1' => $query->donate_name_1,
				'donate_code_2' => $query->donate_code_2,
				'donate_name_2' => $query->donate_name_2,
				'exam_code_01' => $query->exam_code_01,
				'exam_name_01' => $query->exam_name_01,
				'exam_code_02' => $query->exam_code_02,
				'exam_name_02' => $query->exam_name_02,
				'exam_code_03' => $query->exam_code_03,
				'exam_name_03' => $query->exam_name_03,
				'exam_code_04' => $query->exam_code_04,
				'exam_name_04' => $query->exam_name_04,
				'exam_code_05' => $query->exam_code_05,
				'exam_name_05' => $query->exam_name_05,
			);
			$after = array(
				'admission_id' => $request->has('admission_id') ? $request->admission_id : $query->admission_id,
				'donate_code_1' => $request->has('donate_code_1') ? $request->donate_code_1 : $query->donate_code_1,
				'donate_name_1' => $request->has('donate_name_1') ? $request->donate_name_1 : $query->donate_name_1,
				'donate_code_2' => $request->has('donate_code_2') ? $request->donate_code_2 : $query->donate_code_2,
				'donate_name_2' => $request->has('donate_name_2') ? $request->donate_name_2 : $query->donate_name_2,
				'exam_code_01' => $request->has('exam_code_01') ? $request->exam_code_01 : $query->exam_code_01,
				'exam_name_01' => $request->has('exam_name_01') ? $request->exam_name_01 : $query->exam_name_01,
				'exam_code_02' => $request->has('exam_code_02') ? $request->exam_code_02 : $query->exam_code_02,
				'exam_name_02' => $request->has('exam_name_02') ? $request->exam_name_02 : $query->exam_name_02,
				'exam_code_03' => $request->has('exam_code_03') ? $request->exam_code_03 : $query->exam_code_03,
				'exam_name_03' => $request->has('exam_name_03') ? $request->exam_name_03 : $query->exam_name_03,
				'exam_code_04' => $request->has('exam_code_04') ? $request->exam_code_04 : $query->exam_code_04,
				'exam_name_04' => $request->has('exam_name_04') ? $request->exam_name_04 : $query->exam_name_04,
				'exam_code_05' => $request->has('exam_code_05') ? $request->exam_code_05 : $query->exam_code_05,
				'exam_name_05' => $request->has('exam_name_05') ? $request->exam_name_05 : $query->exam_name_05,
			);
			if ($action == 'Ubah')
			{
		        $this->logTransaction('#', $action .' '. $subject, json_encode($before), json_encode($after));
			} else {
		        $this->logTransaction('#', $action .' '. $subject, json_encode($before), '{}');
			}
		}
	}
}
