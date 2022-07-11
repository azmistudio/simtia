<?php

namespace Modules\Academic\Repositories\Academic;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\ReferenceTrait;
use Modules\Academic\Entities\ColumnProspectStudent;
use Carbon\Carbon;

class ColumnProspectEloquent implements ColumnProspectRepository
{

	use AuditLogTrait;
	use HelperTrait;
	use ReferenceTrait;

	public function create(Request $request, $id)
	{
        $payload = $request->only(['additional','columnopts']);
        if (isset($payload['additional'])) 
        {
			$keys = array_keys(array_filter($payload['additional']));
			$slice_add = Arr::only($payload['additional'], $keys);
			$slice_opt = Arr::only($payload['columnopts'], $keys);
			foreach ($slice_add as $k_add => $v_add) 
			{
				foreach ($slice_opt as $k_opt => $v_opt) 
				{
					if ($k_add == $k_opt)
					{
						$columns = explode("-", $v_opt);
						$data = new ColumnProspectStudent;
						$data->prospect_student_id = $id;
						$data->column_id = $columns[0];
						$data->type = $columns[1];
						$data->values = $v_add;
						$data->save();
					}	
				}
			}
        } 
        return;
	}

	public function destroy($id, $subject)
	{
		$request = new Request();
		$this->logActivity($request, $id, $subject, 'Hapus');
		return ColumnProspectStudent::where('prospect_student_id', $id)->delete();
	}

	private function logActivity(Request $request, $model_id, $subject, $action) 
	{
		if ($action == 'Tambah')
		{
			$data = array(
				'prospect_student_id' => $request->prospect_student_id, 
				'column_id' => $request->column_id, 
				'values' => $request->values, 
			);
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			$query = ColumnProspectStudent::where('prospect_student_id', $model_id)->first();
			$before = array(
				'prospect_student_id' => $query->prospect_student_id, 
				'column_id' => $query->column_id, 
				'values' => $query->values, 
			);
			$after = array(
				'prospect_student_id' => $request->has('prospect_student_id') ? $request->prospect_student_id : $query->prospect_student_id, 
				'column_id' => $request->has('column_id') ? $request->column_id : $query->column_id, 
				'values' => $request->has('values') ? $request->values : $query->values, 
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