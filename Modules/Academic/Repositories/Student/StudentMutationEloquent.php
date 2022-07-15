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
use Modules\Academic\Entities\StudentMutation;
use Carbon\Carbon;

class StudentMutationEloquent implements StudentMutationRepository
{

	use AuditLogTrait;
	use HelperTrait;
	use ReferenceTrait;

	public function create(Request $request, $subject)
	{
		$payload = $request->all();
		$this->logActivity($request, 0, $subject, 'Tambah');
		return StudentMutation::create($payload);
	}

	public function update(Request $request, $subject)
	{
		$payload = Arr::except($request->all(), ['created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah');
        // 
        return StudentMutation::where('id', $payload['id'])->update($payload);
	}

	public function showIn($params)
    {
        return StudentMutation::whereIn('id', $params)->orderBy('id')->get();
    }

	public function data(Request $request)
	{
		$param = $this->gridRequest($request);
        $query = StudentMutation::select('*');
        if (auth()->user()->getDepartment->is_all != 1)
        {
            $query = $query->where('department_id', auth()->user()->department_id);
        } 
        $fdept = isset($request->fdept) ? $request->fdept : '';
        if ($fdept != '') 
        {
            $query = $query->where('department_id', $fdept);
        }
        $year = isset($request->fyear) ? $request->fyear : '';
        if ($year != '') 
        {
            $query = $query->whereYear('mutation_date', $year);
        }
        $type = isset($request->ftype) ? $request->ftype : '';
        if ($type != '') 
        {
            $query = $query->where('mutation_id', $type);
        }
        // result
        $result["total"] = $query->count('id');
        $result["rows"] = $query->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            $model['student_no'] = $model->getStudent->student_no;
            $model['student_name'] = $model->getStudent->name;
            $model['class'] = $model->getStudent->getClass->class;
            $model['student_id'] = $model->getStudent->id;
            $model['department_id'] = $model->department_id;
            $model['end_class'] = $model->getAlumniByStudent->end_class;
            $model['mutation_date'] = $this->formatDate($model->mutation_date,'local');
            $model['mutation_id'] = $this->getReference($model->mutation_id);
            return $model;
        });
        return $result;
	}

	public function destroy($id, $subject)
	{
		$request = new Request();
		$this->logActivity($request, $id, $subject, 'Hapus');
		return StudentMutation::destroy($id);
	}

    public function comboGrid(Request $request)
    {
        $param = $this->gridRequest($request, 'desc', 'seq');
        $query = DB::table('academic.student_mutations_view')->select('*');
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->orderBy($param['sort'], $param['sort_by'])->get();
        return $result;
    }

    // helpers
    
	private function logActivity(Request $request, $model_id, $subject, $action) 
	{
		if ($action == 'Tambah')
		{
			$data = array(
				'student_id' => $request->student_id, 
				'mutation_id' => $request->mutation_id, 
				'mutation_date' => $request->mutation_date, 
				'mutation_date' => $request->mutation_date, 
				'remark' => $request->remark, 
				'department_id' => $request->department_id, 
			);
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			$query = StudentMutation::find($model_id);
			$before = array(
				'student_id' => $query->student_id, 
				'mutation_id' => $query->mutation_id, 
				'mutation_date' => $query->mutation_date, 
				'mutation_date' => $query->mutation_date, 
				'remark' => $query->remark, 
				'department_id' => $query->department_id, 
			);
			$after = array(
				'student_id' => $request->has('student_id') ? $request->student_id : $query->student_id, 
				'mutation_id' => $request->has('mutation_id') ? $request->mutation_id : $query->mutation_id, 
				'mutation_date' => $request->has('mutation_date') ? $request->mutation_date : $query->mutation_date, 
				'mutation_date' => $request->has('mutation_date') ? $request->mutation_date : $query->mutation_date, 
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