<?php

namespace Modules\Academic\Repositories\Student;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\ReferenceTrait;

class RoomPlacementEloquent implements RoomPlacementRepository
{

	use HelperTrait;
	use ReferenceTrait;

	public function create(Request $request, $subject)
	{
		$payload = Arr::only($request->all(), ['room_id','student_id']);
		return DB::table('academic.room_placements')->insert($payload);
	}

	public function data(Request $request)
	{
		$param = $this->gridRequest($request,'asc','student_id');
        $query = DB::table('academic.room_placements')
                    ->select('academic.room_placements.id','academic.students.student_no','academic.students.name','academic.room_placements.student_id')
        			->join('academic.students','academic.students.id','=','academic.room_placements.student_id')
        			->where('room_id', $request->room_id);
        // filter
        $filters = $request->has('filterRules') ? json_decode($request->filterRules) : [];
        if (count($filters) > 0)
        {
            foreach ($filters as $val) 
            {
                $query = $query->whereRaw('LOWER('.$val->field.') LIKE ?', ['%'.Str::lower($val->value).'%']);
            }
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function($model){
            $model->name = Str::title($model->name);
            return $model;
        });
        return $result;
	}

}