<?php

namespace App\Repositories\Department;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\DepartmentTrait;
use App\Models\Department;
use Carbon\Carbon;

class DepartmentEloquent implements DepartmentRepository
{

	use AuditLogTrait;
	use HelperTrait;
	use DepartmentTrait;

	public function create(Request $request, $subject)
	{
		$payload = $request->all();
		$this->logActivity($request, 0, $subject, 'Tambah');
		return Department::create($payload);
	}

	public function update(Request $request, $subject)
	{
		$payload = Arr::except($request->all(), ['obdate','created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $request->id, $subject, 'Ubah');
        return Department::where('id', $request->id)->update($payload);
	}

	public function data(Request $request)
	{
        $param = $this->gridRequest($request);
        $query = Department::select('*');
        if ($this->countDepartment())
        {
        	$query = $query->where('is_all',0);
        } 
        // filter
        $name = isset($request->params['fname']) ? $request->params['fname'] : '';
        if ($name != '') 
        {
            $query = $query->whereRaw('LOWER(name) like ?', ['%'.Str::lower($name).'%']);
        }
        $active = isset($request->params['factive']) ? $request->params['factive'] : '';
        if ($active != '') 
        {
            $query = $query->where('is_active', $active);
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
        	$model['employee_id'] = optional($model->getEmployee)->title_first .' '. optional($model->getEmployee)->name .' '. optional($model->getEmployee)->title_end;
        	return $model->only(['id','name','employee_id']);
        });
        return $result;
	}

	public function destroy($id, $subject)
	{
		$request = new Request();
		$this->logActivity($request, $id, $subject, 'Hapus');
		return Department::destroy($id);
	}

	private function logActivity(Request $request, $model_id, $subject, $action) 
	{
		if ($action == 'Tambah')
		{
			$data = array(
				'name' => $request->name, 
				'employee_id' => $request->employee_id, 
				'is_active' => $request->is_active, 
			);
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			$query = Department::find($model_id);
			$before = array(
				'name' => $query->name, 
				'employee_id' => $query->employee_id, 
				'is_active' => $query->is_active, 
			);
			$after = array(
				'name' => $request->has('name') ? $request->name : $query->name, 
				'employee_id' => $request->has('employee_id') ? $request->employee_id : $query->employee_id, 
				'is_active' => $request->has('is_active') ? $request->is_active : $query->is_active, 
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