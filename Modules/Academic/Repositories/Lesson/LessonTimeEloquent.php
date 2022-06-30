<?php

namespace Modules\Academic\Repositories\Lesson;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\ReferenceTrait;
use Modules\Academic\Entities\LessonScheduleTime;
use Carbon\Carbon;

class LessonTimeEloquent implements LessonTimeRepository
{

	use AuditLogTrait;
	use HelperTrait;
	use ReferenceTrait;

	public function create(Request $request, $subject)
	{
		$payload = $request->all();
		$this->logActivity($request, 0, $subject, 'Tambah');
		return LessonScheduleTime::create($payload);
	}

	public function update(Request $request, $subject)
	{
		$payload = Arr::except($request->all(), ['created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah');
        // 
        return LessonScheduleTime::where('id', $payload['id'])->update($payload);
	}

	public function showIn($params)
	{
        return LessonScheduleTime::whereIn('id', $params)->orderBy('id')->get();
	}

	public function data(Request $request)
	{
		$param = $this->gridRequest($request);
        $query = LessonScheduleTime::select('*');
        if (auth()->user()->getDepartment->is_all != 1)
		{
			$query = $query->where('department_id', auth()->user()->department_id);
		} 
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            $model['department_id'] = $model->getDepartment->name;
            $model['times'] = $model->start .' - '. $model->end;
            return $model->only(['id','time','department_id','times']);
        });
        return $result;
	}	

	public function destroy($id, $subject)
	{
		$request = new Request();
		$this->logActivity($request, $id, $subject, 'Hapus');
		return LessonScheduleTime::destroy($id);
	}

	public function combogrid(Request $request)
    {
        $param = $this->gridRequest($request);
        $query = LessonScheduleTime::where('is_active', 1);
        if (auth()->user()->getDepartment->is_all != 1)
		{
			$query = $query->where('department_id', auth()->user()->department_id);
		} 
        // filter
        $filter = isset($request->q) ? $request->q : '';
        if ($filter != '') 
        {
            $query = $query->whereRaw('LOWER(code) like ?', ['%'.Str::lower($filter).'%']);
        }
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy('id')->get()->map(function ($model) {
            $model['department'] = $model->getDepartment->name;
            return $model;
        });
        return $result;
    }

    public function combobox($id)
    {
        $query = LessonScheduleTime::where('department_id', $id)->get();
		foreach ($query as $val) 
		{
			$result[] = array(
				'id' => $val->time,
				'time' => $val->time .' | '. substr($val->start, 0, 5) .' - '. substr($val->end, 0, 5)
			);
		}
		return $result;
    }
	
	private function logActivity(Request $request, $model_id, $subject, $action) 
	{
		if ($action == 'Tambah')
		{
			$data = array(
				'time' => $request->time, 
				'department_id' => $request->department_id, 
				'start' => $request->start, 
				'end' => $request->end, 
			);
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			$query = LessonScheduleTime::find($model_id);
			$before = array(
				'time' => $query->time, 
				'department_id' => $query->department_id, 
				'start' => $query->start, 
				'end' => $query->end, 
			);
			$after = array(
				'time' => $request->has('time') ? $request->time : $query->time, 
				'department_id' => $request->has('department_id') ? $request->department_id : $query->department_id, 
				'start' => $request->has('start') ? $request->start : $query->start, 
				'end' => $request->has('end') ? $request->end : $query->end, 
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