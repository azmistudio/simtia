<?php

namespace Modules\Academic\Repositories\Student;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\ReferenceTrait;
use Modules\Academic\Entities\Students;
use Modules\Academic\Entities\MemorizeCard;
use Carbon\Carbon;

class MemorizeCardEloquent implements MemorizeCardRepository
{

	use HelperTrait;
    use AuditLogTrait;
	use ReferenceTrait;

	public function create(Request $request, $subject)
	{
		$payload = Arr::except($request->all(), ['id']);
        $this->logActivity($request, 0, $subject, 'Tambah');
		return MemorizeCard::create($payload);
	}

    public function update(Request $request, $subject)
    {
        $payload = Arr::except($request->all(), ['students','created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah');
        // 
        return MemorizeCard::where('id', $payload['id'])->update($payload);
    }

	public function data(Request $request)
	{
		$param = $this->gridRequest($request,'asc','class_id');
        $query = MemorizeCard::select('academic.memorize_cards.class_id','academic.memorize_cards.memorize_date')
                    ->groupBy('class_id','memorize_date');
        // filter
        if (auth()->user()->getDepartment->is_all != 1)
        {
            $query = $query->whereHas('getClass.getSchoolYear', function($qry) {
                $qry->where('department_id', auth()->user()->department_id);
            });
        } 
        $fdept = isset($request->params['fdept']) ? $request->params['fdept'] : '';
        if ($fdept != '') 
        {
            $query = $query->whereHas('getClass.getSchoolYear', function($qry) use ($fdept) {
                $qry->where('department_id', $fdept);
            });
        }
        $fclass = isset($request->params['fclass']) ? $request->params['fclass'] : '';
        if ($fclass != '') 
        {
            $query->where('class_id', $fclass);
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function($model){
            $model['department_id'] = $model->getClass->getSchoolYear->getDepartment->name;
            $model['id_class'] = $model->class_id;
            $model['class_id'] = $model->getClass->class;
            $model['date'] = $model->memorize_date;
            $model['memorize_date'] = $this->formatDate($model->memorize_date,'local');
            return $model;
        });
        return $result;
	}

    public function dataCard(Request $request)
    {
        $param = $this->gridRequest($request,'asc','student_id');
        $query = Students::selectRaw('
                        academic.students.id AS student_id,
                        academic.students.student_no,
                        academic.students.name,
                        COALESCE((SELECT id FROM academic.memorize_cards WHERE class_id = ? AND memorize_date = ? AND student_id = academic.students.id),0) AS id,
                        (SELECT from_surah_id FROM academic.memorize_cards WHERE class_id = ? AND memorize_date = ? AND student_id = academic.students.id) AS from_surah,
                        (SELECT to_surah_id FROM academic.memorize_cards WHERE class_id = ? AND memorize_date = ? AND student_id = academic.students.id) AS to_surah,
                        (SELECT from_verse FROM academic.memorize_cards WHERE class_id = ? AND memorize_date = ? AND student_id = academic.students.id) AS from_verse,
                        (SELECT to_verse FROM academic.memorize_cards WHERE class_id = ? AND memorize_date = ? AND student_id = academic.students.id) AS to_verse,
                        (SELECT status FROM academic.memorize_cards WHERE class_id = ? AND memorize_date = ? AND student_id = academic.students.id) AS status
                    ',
                    [
                        $request->class_id, $request->memorize_date,
                        $request->class_id, $request->memorize_date,
                        $request->class_id, $request->memorize_date,
                        $request->class_id, $request->memorize_date,
                        $request->class_id, $request->memorize_date,
                        $request->class_id, $request->memorize_date
                    ])
                    ->where('academic.students.is_active', 1)
                    ->where('academic.students.class_id', $request->class_id);
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->orderBy($param['sort'], $param['sort_by'])->get();
        return $result;
    }

    public function destroy($id, $subject)
    {
        $request = new Request();
        $this->logActivity($request, $id, $subject, 'Hapus');
        return MemorizeCard::destroy($id);
    }

    private function logActivity(Request $request, $model_id, $subject, $action) 
    {
        if ($action == 'Tambah')
        {
            $data = array(
                'student_id' => $request->student_id, 
                'employee_id' => $request->employee_id, 
                'memorize_date' => $request->memorize_date, 
                'from_surah' => $request->from_surah, 
                'to_surah' => $request->to_surah, 
                'from_verse' => $request->from_verse, 
                'to_verse' => $request->to_verse, 
            );
            $this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
        } else {
            $query = MemorizeCard::find($model_id);
            $before = array(
                'student_id' => $query->student_id, 
                'employee_id' => $query->employee_id, 
                'memorize_date' => $query->memorize_date, 
                'from_surah' => $query->from_surah_id, 
                'to_surah' => $query->to_surah_id, 
                'from_verse' => $query->from_verse, 
                'to_verse' => $query->to_verse, 
            );
            $after = array(
                'student_id' => $request->has('student_id') ? $request->student_id : $query->student_id, 
                'employee_id' => $request->has('employee_id') ? $request->employee_id : $query->employee_id, 
                'memorize_date' => $request->has('memorize_date') ? $request->memorize_date : $query->memorize_date, 
                'from_surah' => $request->has('from_surah') ? $request->from_surah : $query->from_surah_id, 
                'to_surah' => $request->has('to_surah') ? $request->to_surah : $query->to_surah_id, 
                'from_verse' => $request->has('from_verse') ? $request->from_verse : $query->from_verse, 
                'to_verse' => $request->has('to_verse') ? $request->to_verse : $query->to_verse, 
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