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
use Modules\Academic\Entities\CalendarActivity;
use Carbon\Carbon;

class CalendarActivityEloquent implements CalendarActivityRepository
{

    use AuditLogTrait;
    use HelperTrait;
    use ReferenceTrait;

    public function create(Request $request, $subject)
    {
        $payload = $request->all();
        $this->logActivity($request, 0, $subject, 'Tambah');
        return CalendarActivity::create($payload);
    }

    public function update(Request $request, $subject)
    {
        $payload = Arr::except($request->all(), ['created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah');
        return CalendarActivity::where('id', $payload['id'])->update($payload);
    }

    public function data(Request $request)
    {
        $param = $this->gridRequest($request, 'asc', 'id');
        $query = CalendarActivity::select('*');
        // filter
        if (auth()->user()->getDepartment->is_all != 1)
        {
            $query = $query->whereHas('getCalendar.getSchoolYear', function($qry) {
                $qry->where('department_id', auth()->user()->department_id);
            });
        } 
        $dept = isset($request->params['fdept']) ? $request->params['fdept'] : '';
        if ($dept != '')
        {
            $query->whereHas('getCalendar.getSchoolYear', function($qry) use($dept) {
                $qry->where('department_id', $dept);
            });
        }
        $cal = isset($request->params['fcal']) ? $request->params['fcal'] : '';
        if ($cal != '')
        {
            $query = $query->where('calendar_id', $cal);
        }
        $calview = isset($request->fcalview) ? $request->fcalview : '';
        if ($calview != '')
        {
            $query = $query->where('calendar_id', $calview);
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            $model['department'] = $model->getCalendar->getSchoolYear->getDepartment->name;
            $model['calendar'] = $model->getCalendar->description;
            $model['start'] = Carbon::createFromFormat('Y-m-d', $model->start)->format('d/m/Y');
            $model['end'] = Carbon::createFromFormat('Y-m-d', $model->end)->format('d/m/Y');
            return $model->only(['id','activity','calendar_id','department','calendar','start','end']);
        });
        return $result;
    }

    public function destroy($id, $subject)
    {
        $request = new Request();
        $this->logActivity($request, $id, $subject, 'Hapus');
        return CalendarActivity::destroy($id);
    }

    private function logActivity(Request $request, $model_id, $subject, $action) 
    {
        if ($action == 'Tambah')
        {
            $data = array(
                'calendar_id' => $request->calendar_id, 
                'start' => $request->start, 
                'end' => $request->end, 
                'activity' => $request->activity, 
            );
            $this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
        } else {
            $query = CalendarActivity::find($model_id);
            $before = array(
                'calendar_id' => $query->calendar_id, 
                'start' => $query->start, 
                'end' => $query->end, 
                'activity' => $query->activity, 
            );
            $after = array(
                'calendar_id' => $request->has('calendar_id') ? $request->calendar_id : $query->calendar_id, 
                'start' => $request->has('start') ? $request->start : $query->start, 
                'end' => $request->has('end') ? $request->end : $query->end, 
                'activity' => $request->has('activity') ? $request->activity : $query->activity, 
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