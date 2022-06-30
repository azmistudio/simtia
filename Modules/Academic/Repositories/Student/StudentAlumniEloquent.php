<?php

namespace Modules\Academic\Repositories\Student;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\ReferenceTrait;
use Modules\Academic\Entities\StudentAlumni;
use Carbon\Carbon;

class StudentAlumniEloquent implements StudentAlumniRepository
{

	use AuditLogTrait;
	use HelperTrait;
	use ReferenceTrait;

	public function create(Request $request, $subject)
	{
		$payload = $request->all();
		$this->logActivity($request, 0, $subject, 'Tambah');
		return StudentAlumni::create($payload);
	}

	public function data(Request $request)
	{
		$param = $this->gridRequest($request,'asc','student_id');
        $query = StudentAlumni::where('end_class', $request->fclass)
                    ->where('end_grade', $request->fgrade)
                    ->where('department_id', $request->fdept)
                    ->whereYear('graduate_date', $request->fperiod);
        // result
        $result["total"] = $query->count('id');
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            $model['student_no'] = $model->getStudent->student_no;
            $model['name'] = $model->getStudent->name;
            $model['class'] = $model->getClass->class;
            $model['graduate_date'] = Carbon::createFromFormat('Y-m-d', $model->graduate_date)->format('d/m/Y');
            return $model->only(['student_id','student_no','name','class','graduate_date']);
        });
        return $result;
	}

	public function destroy($id, $subject)
	{
		$request = new Request();
		$this->logActivity($request, $id, $subject, 'Hapus');
		return StudentAlumni::destroy($id);
	}

    public function comboGrid(Request $request)
    {
        $param = $this->gridRequest($request, 'desc', 'seq');
        $query = DB::table('academic.student_alumnis_view')->select('*');
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get();
        return $result;
    }

    // helpers
    
	private function logActivity(Request $request, $model_id, $subject, $action) 
	{
		if ($action == 'Tambah')
		{
			$data = array(
				'student_id' => $request->student_id, 
				'end_class' => $request->end_class, 
				'end_grade' => $request->end_grade, 
				'graduate_date' => $request->graduate_date, 
				'remark' => $request->remark, 
				'department_id' => $request->department_id, 
			);
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			$query = StudentAlumni::find($model_id);
			$before = array(
				'student_id' => $query->student_id, 
				'end_class' => $query->end_class, 
				'end_grade' => $query->end_grade, 
				'graduate_date' => $query->graduate_date, 
				'remark' => $query->remark, 
				'department_id' => $query->department_id, 
			);
			$after = array(
				'student_id' => $request->has('student_id') ? $request->student_id : $query->student_id, 
				'end_class' => $request->has('end_class') ? $request->end_class : $query->end_class, 
				'end_grade' => $request->has('end_grade') ? $request->end_grade : $query->end_grade, 
				'graduate_date' => $request->has('graduate_date') ? $request->graduate_date : $query->graduate_date, 
				'remark' => $request->has('remark') ? $request->remark : $query->remark, 
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