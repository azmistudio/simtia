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
use Modules\Academic\Entities\LessonExam;
use Carbon\Carbon;

class LessonExamTypeEloquent implements LessonExamTypeRepository
{

    use AuditLogTrait;
    use HelperTrait;
    use ReferenceTrait;

	public function create(Request $request, $subject)
	{
        $payload = $request->all();
        $this->logActivity($request, 0, $subject, 'Tambah');
        return LessonExam::create($payload);
	}

	public function update(Request $request, $subject)
	{
        $payload = Arr::except($request->all(), ['department_id','created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah');
        // 
        return LessonExam::where('id', $payload['id'])->update($payload);
	}

    public function showIn($params)
    {
        return LessonExam::whereIn('id', $params)->orderBy('id')->get();
    }

	public function data(Request $request)
	{
        $param = $this->gridRequest($request,'asc','id');
        $query = LessonExam::select('*');
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
        $code = isset($request->params['fcode']) ? $request->params['fcode'] : '';
        if ($code != '') 
        {
            $query = $query->whereRaw('LOWER(code) like ?', ['%'.Str::lower($code).'%']);
        }
        $name = isset($request->params['fname']) ? $request->params['fname'] : '';
        if ($name != '') 
        {
            $query = $query->whereRaw('LOWER(subject) like ?', ['%'.Str::lower($name).'%']);
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            $model['lesson_id'] = $model->getLesson->name;
            $model['department'] = $model->getLesson->getDepartment->name;
            return $model;
        });
        return $result;
	}

	public function destroy($id, $subject)
	{
        $request = new Request();
        $this->logActivity($request, $id, $subject, 'Hapus');
        return LessonExam::destroy($id);
	}

    public function list($id, $aspect_id)
    {
        return LessonExam::select('id','subject')->where('lesson_id', $id)->where('score_aspect_id', $aspect_id)->orderBy('id')->get();
    }

    public function combobox($id)
    {
        return LessonExam::selectRaw('id as value, UPPER(subject) AS text')->where('lesson_id', $id)->orderBy('id')->get();
    }

    public function reportAssessment($lesson_id, $grade_id, $score_aspect_id, $employee_id)
    {
        return LessonExam::select(
                    'academic.lesson_exams.id',
                    'academic.lesson_exams.code',
                    'academic.lesson_exams.subject',
                    'academic.lesson_assessments.score_aspect_id',
                    DB::raw('academic.lesson_assessments.id as assessment_id')
                )
                ->join('academic.lesson_assessments','academic.lesson_assessments.exam_id','=','academic.lesson_exams.id')
                ->where('academic.lesson_exams.lesson_id', $lesson_id)
                ->where('academic.lesson_assessments.grade_id', $grade_id)
                ->where('academic.lesson_assessments.score_aspect_id', $score_aspect_id)
                ->where('academic.lesson_assessments.employee_id', $employee_id)
                ->get();
    }

    private function logActivity(Request $request, $model_id, $subject, $action) 
    {
        if ($action == 'Tambah')
        {
            $data = array(
                'code' => $request->code, 
                'lesson_id' => $request->lesson_id, 
                'subject' => $request->subject, 
            );
            $this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
        } else {
            $query = LessonExam::find($model_id);
            $before = array(
                'code' => $query->code, 
                'lesson_id' => $query->lesson_id, 
                'subject' => $query->subject, 
            );
            $after = array(
                'code' => $request->has('code') ? $request->code : $query->code, 
                'lesson_id' => $request->has('lesson_id') ? $request->lesson_id : $query->lesson_id, 
                'subject' => $request->has('subject') ? $request->subject : $query->subject, 
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