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
use Modules\Academic\Entities\Students;
use Carbon\Carbon;

class PlacementEloquent implements PlacementRepository
{

	use AuditLogTrait;
	use HelperTrait;
	use ReferenceTrait;

	public function data(Request $request)
	{
		$param = $this->gridRequest($request);
        $query = Students::select('*');
        // filter
        $generation = isset($request->fgeneration) ? $request->fgeneration : '';
        if ($generation != '') 
        {
            $query = $query->where('generation_id', $generation);
        }
        $class = isset($request->fclass) ? $request->fclass : '';
        if ($class != '') 
        {
            $query = $query->where('class_id', $class);
        }
        $name = isset($request->fname) ? $request->fname : '';
        if ($name != '') 
        {
            $query = $query->whereRaw('LOWER(name) like ?', ['%'.Str::lower($name).'%']);
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            return $model->only(['id','name','student_no','prospect_student_id']);
        });
        return $result;
	}	

	public function destroy($id, $subject)
	{
		$request = new Request();
		$this->logActivity($request, $id, $subject, 'Hapus');
		DB::table('academic.student_class_histories')->where('student_id',$id)->delete();
		DB::table('academic.student_dept_histories')->where('student_id',$id)->delete();
		DB::table('academic.column_students')->where('student_id',$id)->delete();
		return Students::destroy($id);
	}

	private function logActivity(Request $request, $model_id, $subject, $action) 
	{
		$query = Students::find($model_id);
		$before = array(
			'student_no' => $query->student_no, 
			'name' => $query->name, 
			'prospect_student_group_id' => $query->prospect_student_group_id, 
			'prospect_student_id' => $query->prospect_student_id, 
			'class_id' => $query->class_id, 
			'student_status' => $query->student_status, 
		);
        $this->logTransaction('#', $action .' '. $subject, json_encode($before), '{}');
	}
}