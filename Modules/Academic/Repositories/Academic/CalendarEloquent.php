<?php

namespace Modules\Academic\Repositories\Academic;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\ReferenceTrait;
use Modules\Academic\Entities\Calendar;
use Carbon\Carbon;

class CalendarEloquent implements CalendarRepository
{
    use AuditLogTrait;
    use HelperTrait;
    use ReferenceTrait;

    public function create(Request $request, $subject)
    {
        $payload = $request->all();
        $this->logActivity($request, 0, $subject, 'Tambah');
        return Calendar::create($payload);
    }

    public function update(Request $request, $subject)
    {
        $payload = Arr::except($request->all(), ['created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah');
        return Calendar::where('id', $payload['id'])->update($payload);
    }

    public function data(Request $request)
    {
        $param = $this->gridRequest($request, 'asc', 'id');
        $query = Calendar::select('*');
        if (auth()->user()->getDepartment->is_all != 1)
        {
            $query = $query->whereHas('getSchoolYear', function($qry) {
                $qry->where('department_id', auth()->user()->department_id);
            });
        } 
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            $model['schoolyear'] = $model->getSchoolYear->school_year;
            $model['department'] = $model->getSchoolYear->getDepartment->name;
            $model['period'] = $model->getSchoolYear->start_date->format('d/m/Y') .' s.d '. $model->getSchoolYear->end_date->format('d/m/Y');
            return $model->only(['id','description','is_active','schoolyear','department','period']);
        });
        return $result;
    }

    public function destroy($id, $subject)
    {
        $request = new Request();
        $this->logActivity($request, $id, $subject, 'Hapus');
        return Calendar::destroy($id);
    }

    public function list()
    {
        $query = Calendar::where('is_active', 1);
        if (auth()->user()->getDepartment->is_all != 1)
        {
            $query = $query->whereHas('getSchoolYear', function($qry) {
                $qry->where('department_id', auth()->user()->department_id);
            });
        } 
        $query = $query->get()->map(function($model) {
                        $model['school_year'] = $model->getSchoolYear->school_year;
                        $model['period'] = $model->getSchoolYear->start_date->format('d/m/Y') .' s.d '. $model->getSchoolYear->end_date->format('d/m/Y');
                        $model['department'] = $model->getSchoolYear->getDepartment->name;
                        return $model;
                    });
        $options[] = array('id' => '', 'description' => '---');
        foreach ($query as $row) 
        {
            $options[] = array(
                'id' => $row->id.'-'.$row->department.'-'.$row->school_year.'-'.$row->period,
                'description' => $row->description
            );
        }
        return $options;
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
            $query = Calendar::find($model_id);
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