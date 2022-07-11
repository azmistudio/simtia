<?php

namespace App\Repositories\General;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\AuditLogTrait;
use App\Models\Room;
use Carbon\Carbon;

class RoomEloquent implements RoomRepository
{

	use HelperTrait;
	use AuditLogTrait;

	public function create(Request $request, $subject)
	{
		$payload = $request->all();
		$this->logActivity($request, 0, $subject, 'Tambah');
		return Room::create($payload);
	}

	public function update(Request $request, $subject)
	{
		$payload = Arr::except($request->all(), ['created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $request->id, $subject, 'Ubah');
        return Room::where('id', $request->id)->update($payload);
	}

	public function data(Request $request)
	{
        $param = $this->gridRequest($request);
        $query = Room::select('*');
        if (auth()->user()->getDepartment->is_all != 1)
		{
			$query = $query->where('department_id', auth()->user()->department_id);
		} 
		// filter
		$fdept = isset($request->params['fdept']) ? $request->params['fdept'] : '';
        if ($fdept != '') 
        {
            $query = $query->where('department_id', $fdept);
        }
        $name = isset($request->params['fname']) ? $request->params['fname'] : '';
        if ($name != '') 
        {
            $query = $query->whereRaw('LOWER(name) like ?', ['%'.Str::lower($name).'%']);
        }
        //
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function($model){
        	$model['department_id'] = $model->getDepartment->name;
        	$model['capacity'] = $model->capacity .'/'. $model->getOccupied($model->id);
        	return $model;
        });
        return $result;
	}

	public function destroy($id, $subject)
	{
		$request = new Request();
		$this->logActivity($request, $id, $subject, 'Hapus');
		return Room::destroy($id);
	}

	public function combogrid(Request $request)
	{
		$param = $this->gridRequest($request);
		$query = Room::where('is_employee', $request->is_employee);
		if (auth()->user()->getDepartment->is_all != 1)
		{
			$query = $query->where('department_id', auth()->user()->department_id);
		}
        // result
        $result["total"] = $query->count('id');
        $result["rows"] = $query->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            $model['department'] = $model->getDepartment->name;
        	$model['quota'] = $model->capacity .'/'. $model->getOccupied($model->id);
        	$model['gender_name'] = $model->gender == 1 ? 'Ikhwan' : 'Akhwat';
        	$model['employee'] = optional($model->getEmployee)->title_first .' '. optional($model->getEmployee)->name .' '. optional($model->getEmployee)->title_end;
            return $model;
        });
        return $result;
	}

	private function logActivity(Request $request, $model_id, $subject, $action) 
	{
		if ($action == 'Tambah')
		{
			$data = array(
				'department_id' => $request->department_id, 
				'name' => $request->name, 
				'gender' => $request->gender, 
				'capacity' => $request->capacity, 
				'employee_id' => $request->employee_id, 
				'is_employee' => $request->is_employee, 
			);
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			$query = Room::find($model_id);
			$before = array(
				'department_id' => $query->department_id, 
				'name' => $query->name,
				'gender' => $query->gender, 
				'capacity' => $query->capacity, 
				'employee_id' => $query->employee_id, 
				'is_employee' => $query->is_employee,  
			);
			$after = array(
				'name' => $request->has('name') ? $request->name : $query->name, 
				'department_id' => $request->has('department_id') ? $request->department_id : $query->department_id, 
				'gender' => $request->has('gender') ? $request->gender : $query->gender, 
				'capacity' => $request->has('capacity') ? $request->capacity : $query->capacity, 
				'employee_id' => $request->has('employee_id') ? $request->employee_id : $query->employee_id, 
				'is_employee' => $request->has('is_employee') ? $request->is_employee : $query->is_employee, 
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