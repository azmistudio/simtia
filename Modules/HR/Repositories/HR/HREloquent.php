<?php

namespace Modules\HR\Repositories\HR;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use Modules\HR\Entities\Employee;

class HREloquent implements HRRepository
{

	use AuditLogTrait;
	use HelperTrait;

	public function create(Request $request, $subject)
	{
		$lastNip = Employee::orderByDesc('id')->pluck('employee_id')->first();
        $newNip = !empty($lastNip) ? $lastNip : 100;
		// 
		$payload = $request->all();
        $payload['photo'] = $request->input('photo');
        $payload['employee_id'] = isset($request->employee_id) ? $request->employee_id : intval($newNip) + 1;
        $this->logActivity($request, 0, $subject, 'Tambah');
		return Employee::create($payload);
	}

	public function update(Request $request, $subject)
	{
		if ($request->hasFile('photo')) 
		{
        	$payload = Arr::except($request->all(), ['created_at','_token']);
		    $payload['photo'] = $request->input('photo');
		} else {
			$payload = Arr::except($request->all(), ['photo','created_at','_token']);
		}
		$this->logActivity($request, $request->id, $subject, 'Ubah');
		return Employee::where('id', $request->id)->update($payload);
	}

	public function data(Request $request)
	{
		$param = $this->gridRequest($request);
        // query
        $query = Employee::select('*');
        // filter
        $section = isset($request->params['fsection']) ? $request->params['fsection'] : '';
        if ($section != '')
        {
            $query = $query->where('section', $section);
        }
        $nip = isset($request->params['fnip']) ? $request->params['fnip'] : '';
        if ($nip != '')
        {
            $query = $query->where('employee_id', $nip);
        }
        $name = isset($request->params['fname']) ? $request->params['fname'] : '';
        if ($name != '')
        {
            $query = $query->where('name', 'like', '%'.Str::lower($name).'%');
        }
        $dob = isset($request->params['fdob']) ? $request->params['fdob'] : '';
        if ($dob != '')
        {
            $query = $query->where('dob', $this->tglSys($dob));
        }
        $gender = isset($request->params['fgender']) ? $request->params['fgender'] : '';
        if ($gender != '')
        {
            $query = $query->where('gender', $gender);
        }
        $active = isset($request->params['factive']) ? $request->params['factive'] : '';
        if ($active != '')
        {
            $query = $query->where('is_active', $active);
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            $model['name'] = $model->title_first .' '. $model->name .' '. $model->title_end;
            $model['section'] = $model->getSection->name;
            return $model->only(['id','employee_id','name','photo','section']);
        });
        return $result;
	}

	public function destroy($id, $subject)
	{
		$request = new Request();
		$this->logActivity($request, $id, $subject, 'Hapus');
		return Employee::destroy($id);
	}
	
	public function combogrid(Request $request)
	{
        $param = $this->gridRequest($request);
        $query = Employee::where('is_active', 1);
        // filter
        $filter = $request->has('q') ? $request->q : '';
        if ($filter != '') 
        {
        	if (!is_string($filter))
        	{
            	$query = $query->where('employee_id', $filter);
        	}
        }
        $fsection = $request->has('section') ? $request->section : '';
        if ($fsection != '') 
        {
            $query->where('section', $fsection);
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            $model['name'] = $this->getEmployeeName($model->id);
            $model['section'] = $model->getSection->name;
            return $model->only(['id','employee_id','name','section','email']);
        });
        return $result;
	}

	private function logActivity(Request $request, $model_id, $subject, $action) 
	{
		if ($action == 'Tambah')
		{
			$data = array(
				'name' => $request->name, 
				'email' => $request->email, 
				'section' => $request->section, 
				'is_active' => $request->is_active, 
				'is_retired' => $request->is_retired, 
			);
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			$query = Employee::find($model_id);
			$before = array(
				'name' => $query->name, 
				'email' => $query->email, 
				'section' => $query->section, 
				'is_active' => $query->is_active, 
				'is_retired' => $query->is_retired, 
			);
			$after = array(
				'name' => $request->has('name') ? $request->name : $query->name, 
				'email' => $request->has('email') ? $request->email : $query->email, 
				'section' => $request->has('section') ? $request->section : $query->section, 
				'is_active' => $request->has('is_active') ? $request->is_active : $query->is_active, 
				'is_retired' => $request->has('is_retired') ? $request->is_retired : $query->is_retired, 
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