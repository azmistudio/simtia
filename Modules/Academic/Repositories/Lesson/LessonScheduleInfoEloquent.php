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
use Modules\Academic\Entities\LessonScheduleInfo;
use Carbon\Carbon;

class LessonScheduleInfoEloquent implements LessonScheduleInfoRepository
{
    use AuditLogTrait;
    use HelperTrait;
    use ReferenceTrait;
    
	public function create(Request $request, $subject)
	{
        $request->offsetUnset('isNewRecord');
        $payload = $request->all();
        $this->logActivity($request, 0, $subject, 'Tambah');
        return LessonScheduleInfo::create($payload);
	}

	public function update(Request $request, $subject)
	{
        $payload = Arr::except($request->all(), ['created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah');
        // 
        return LessonScheduleInfo::where('id', $payload['id'])->update($payload);
	}

	public function show($search, $is_one)
	{
        $query = LessonScheduleInfo::select('*');
        foreach ($search as $val) 
        {
            switch ($val['action']) 
            {
                case 'like':
                    $query = $query->where($val['column'], 'like', '%'.Str::lower($val['query']).'%');
                    break;
                default:
                    $query = $query->where($val['column'], $val['query']);
                    break;
            }
        }
        return $is_one == true ? $query->first() : $query->get();
	}

	public function data(Request $request, $id)
	{
        $param = $this->gridRequest($request,'asc','id');
        $query = LessonScheduleInfo::where('schoolyear_id', $id);
        if (auth()->user()->getDepartment->is_all != 1)
        {
            $query = $query->whereHas('getSchoolYear', function($qry) {
                $qry->where('department_id', auth()->user()->department_id);
            });
        } 
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function($model){
            $model['is_active'] = $model['is_active'] == 1 ? 'Tidak' : 'Ya';
            return $model;
        });
        return $result;
	}

	public function destroy($request, $subject)
	{
        $this->logActivity($request, $request->id, $subject, 'Hapus');
        return LessonScheduleInfo::destroy($request->id);
	}

    public function list($id)
    {
        return LessonScheduleInfo::select('id','description')->where('schoolyear_id', $id)->where('is_active', 1)->get();
    }

    public function combogrid(Request $request)
    {
        $param = $this->gridRequest($request,'asc','id');
        $query = LessonScheduleInfo::where('is_active', 1)
                    ->whereHas('getSchoolYear',function($qry) {
                        $qry->where('is_active', 1);
                    });
        if (auth()->user()->getDepartment->is_all != 1)
        {
            $query = $query->whereHas('getSchoolYear', function($qry) {
                $qry->where('department_id', auth()->user()->department_id);
            });
        } 
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function($model){
            $model['start_date'] = $model->getSchoolYear->start_date->format('d/m/Y');
            $model['end_date'] = $model->getSchoolYear->end_date->format('d/m/Y');
            $model['department'] = $model->getSchoolYear->getDepartment->name;
            $model['school_year'] = $model->getSchoolYear->school_year;
            return $model;
        });
        return $result;
    }

    private function logActivity(Request $request, $model_id, $subject, $action) 
    {
        if ($action == 'Tambah')
        {
            $data = array(
                'schoolyear_id' => $request->schoolyear_id, 
                'description' => $request->description, 
                'is_active' => $request->is_active, 
            );
            $this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
        } else {
            $query = LessonScheduleInfo::find($model_id);
            $before = array(
                'schoolyear_id' => $query->schoolyear_id, 
                'description' => $query->description, 
                'is_active' => $query->is_active, 
            );
            $after = array(
                'schoolyear_id' => $request->has('schoolyear_id') ? $request->schoolyear_id : $query->schoolyear_id, 
                'description' => $request->has('description') ? $request->description : $query->description, 
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