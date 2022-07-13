<?php

namespace Modules\Academic\Repositories\Teacher;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\ReferenceTrait;
use Modules\Academic\Entities\Teacher;
use Carbon\Carbon;

class TeacherEloquent implements TeacherRepository
{

	use AuditLogTrait;
	use HelperTrait;
	use ReferenceTrait;

	public function create(Request $request, $subject)
	{
		$payload = $request->all();
		$this->logActivity($request, 0, $subject, 'Tambah');
		return Teacher::create($payload);
	}

	public function update(Request $request, $subject)
	{
		$payload = Arr::except($request->all(), ['created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah');
        // 
        return Teacher::where('id', $payload['id'])->update($payload);
	}

	public function showIn($params)
	{
        return Teacher::whereIn('id', $params)->orderBy('id')->get()->map(function ($model) {
		            $model['is_active'] = $this->getActive()[$model->is_active];
		            return $model;
		        });
	}

	public function data(Request $request)
	{
		$param = $this->gridRequest($request, 'asc', 'id');
        $query = Teacher::select('*');
        if (auth()->user()->getDepartment->is_all != 1)
        {
            $query = $query->whereHas('getLesson', function($qry) {
                $qry->where('department_id', auth()->user()->department_id);
            });
        } 
        // filter
        $fdept = isset($request->params['fdept']) ? $request->params['fdept'] : '';
        if ($fdept != '') 
        {
            $query = $query->whereHas('getLesson', function($qry) use ($fdept) {
                $qry->where('department_id', $fdept);
            });
        }
        $lesson = isset($request->params['flesson']) ? $request->params['flesson'] : '';
        if ($lesson != '') 
        {
        	$query = $query->whereHas('getLesson', function($qry){
        		$qry->whereRaw('LOWER(name) like ?', ['%'.Str::lower($lesson).'%']);
        	});
        }
        $name = isset($request->params['fname']) ? $request->params['fname'] : '';
        if ($name != '') 
        {
        	$query = $query->whereHas('getEmployee', function($qry){
        		$qry->whereRaw('LOWER(name) like ?', ['%'.Str::lower($name).'%']);
        	});
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            $model['lesson_id'] = $model->getLesson->name;
            $model['status_id'] = $model->getStatus->name;
            $model['employee'] = $model->getEmployee->title_first .' '. $model->getEmployee->name .' '. $model->getEmployee->title_end;
            $model['department'] = $model->getLesson->getDepartment->name;
            return $model->only(['id','employee_id','lesson_id','status_id','employee','department']);
        });
        return $result;
	}

	public function destroy($id, $subject)
	{
		$request = new Request();
		$this->logActivity($request, $id, $subject, 'Hapus');
		return Teacher::destroy($id);
	}

	public function combogrid(Request $request)
    {
        $param = $this->gridRequest($request, 'asc', 'seq');
        $query = DB::table('academic.teachers_view')->select('*');
        // 
        if (auth()->user()->getDepartment->is_all != 1)
        {
        	$query->where('department_id', auth()->user()->department_id);
        } 
        // filter
        $filter = isset($request->q) ? $request->q : '';
        if ($filter != '') 
        {
    		$query->whereRaw('LOWER(employee) like ?', ['%'.Str::lower($filter).'%']);
        }
        $fdept = $request->has('department_id') ? $request->department_id : '';
        if ($fdept != '') 
        {
            $query->where('department_id', $fdept);
        }
        $fgrade = $request->has('grade_id') ? $request->grade_id : '';
        if ($fgrade != '') 
        {
            $query->where('grade_id', $fgrade);
        }
        $result["total"] = $query->count();
        $result["rows"] = $query->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
        	$model->department = Str::upper($model->department);
        	$model->lesson = Str::title($model->lesson);
        	$model->employee = $this->getEmployeeName($model->employee_id);
        	$model->grade = Str::upper($model->grade);
        	$model->employee_no = $this->getEmployeeNo($model->employee_id);
        	return $model;
        });
        return $result;
    }

    public function combogridGroup(Request $request)
    {
        $param = $this->gridRequest($request, 'asc', 'employee_id');
        $query = DB::table('academic.teachers_view')
        			->select('department_id','employee_id','grade_id','status_id','department','employee','status')
        			->groupBy('department_id','employee_id','grade_id','status_id','department','employee','status');
        // 
        if (auth()->user()->getDepartment->is_all != 1)
        {
        	$query->where('department_id', auth()->user()->department_id);
        } 
        // filter
        $filter = isset($request->q) ? $request->q : '';
        if ($filter != '') 
        {
    		$query->whereRaw('LOWER(employee) like ?', ['%'.Str::lower($filter).'%']);
        }
        $fdept = $request->has('department_id') ? $request->department_id : '';
        if ($fdept != '') 
        {
            $query->where('department_id', $fdept);
        }
        $fgrade = $request->has('grade_id') ? $request->grade_id : '';
        if ($fgrade != '') 
        {
            $query->where('grade_id', $fgrade);
        }
        $result["total"] = $query->count();
        $result["rows"] = $query->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
        	$model->employee = $this->getEmployeeName($model->employee_id);
        	$model->employee_no = $this->getEmployeeNo($model->employee_id);
        	return $model;
        });
        return $result;
    }

    public function list($id, $department_id)
    {
        return Teacher::where('employee_id', $id)
        		->whereHas('getLesson', function($qry) use ($department_id) {
        			$qry->where('department_id', $department_id);
        		})
        		->get()->map(function($model) {
        			$model['id'] = $model->lesson_id;
		        	$model['lesson'] = $model->getLesson->name;
		        	return $model->only(['id','lesson']);
		        });
    }

    public function reportAssessmentTeacher($lesson_id, $class_id)
    {
        return Teacher::select('employees.id', DB::raw("CONCAT(title_first, ' ', INITCAP(employees.name), CASE WHEN title_end <> '' THEN ', ' ELSE '' END, title_end) as teacher"))
                                ->join('academic.lesson_schedule_teachings','academic.lesson_schedule_teachings.employee_id','=','academic.teachers.employee_id')
                                ->join('employees','employees.id','=','academic.teachers.employee_id')
                                ->where('lesson_id', $lesson_id)
                                ->where('status_id', 66)
                                ->where('academic.lesson_schedule_teachings.class_id', $class_id)
                                ->first();
    }
	
	private function logActivity(Request $request, $model_id, $subject, $action) 
	{
		if ($action == 'Tambah')
		{
			$data = array(
				'employee_id' => $request->employee_id, 
				'lesson_id' => $request->lesson_id, 
				'status_id' => $request->status_id, 
				'is_active' => $request->is_active, 
			);
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			$query = Teacher::find($model_id);
			$before = array(
				'employee_id' => $query->employee_id, 
				'lesson_id' => $query->lesson_id, 
				'status_id' => $query->status_id, 
				'is_active' => $query->is_active, 
			);
			$after = array(
				'employee_id' => $request->has('employee_id') ? $request->employee_id : $query->employee_id, 
				'lesson_id' => $request->has('lesson_id') ? $request->lesson_id : $query->lesson_id, 
				'status_id' => $request->has('status_id') ? $request->status_id : $query->status_id, 
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