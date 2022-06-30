<?php

namespace App\Repositories\Institute;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\DepartmentTrait;
use App\Models\Institute;
use App\Models\Department;
use Carbon\Carbon;

class InstituteEloquent implements InstituteRepository
{

	use AuditLogTrait;
	use HelperTrait;
	use DepartmentTrait;

	public function create(Request $request, $subject)
	{
		$payload = $request->all();
		$payload['logo'] = $request->input('logo');
		$this->logActivity($request, 0, $subject, 'Tambah');
		return Institute::create($payload);
	}

	public function update(Request $request, $subject)
	{
		if ($request->hasFile('logo')) 
		{
        	$payload = Arr::except($request->all(), ['created_at','_token']);
		    $payload['logo'] = $request->input('logo');
		} else {
			$payload = Arr::except($request->all(), ['logo','created_at','_token']);
		}
        $this->logActivity($request, $request->id, $subject, 'Ubah');
        return Institute::where('id', $request->id)->update($payload);
	}

	public function data(Request $request)
	{
		$param = $this->gridRequest($request);
        $query = Institute::select('*');
		if (auth()->user()->getDepartment->is_all != 1)
		{
			$query = $query->where('department_id', auth()->user()->department_id);
		} 
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
        	$model['deptid'] = $model->getDepartment->name;
        	return $model->only(['id','deptid','name','logo']);
        });
        return $result;
	}

	public function destroy($id, $subject)
	{
		$request = new Request();
		$this->logActivity($request, $id, $subject, 'Hapus');
		return Institute::destroy($id);
	}

	private function logActivity(Request $request, $model_id, $subject, $action) 
	{
		if ($action == 'Tambah')
		{
			$data = array(
				'name' => $request->name, 
				'department_id' => $request->department_id, 
			);
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			$query = Institute::find($model_id);
			$before = array(
				'name' => $query->name, 
				'department_id' => $query->department_id, 
			);
			$after = array(
				'name' => $request->has('name') ? $request->name : $query->name, 
				'department_id' => $request->has('department_id') ? $request->department_id : $query->department_id, 
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