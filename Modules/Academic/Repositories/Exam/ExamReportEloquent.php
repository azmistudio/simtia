<?php

namespace Modules\Academic\Repositories\Exam;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\ReferenceTrait;
use Modules\Academic\Entities\SchoolYear;
use Modules\Academic\Entities\Students;
use Modules\Academic\Entities\ScoreAspect;
use Modules\Academic\Entities\Exam;
use Modules\Academic\Entities\ExamReport;
use Modules\Academic\Entities\ExamReportScoreInfo;
use Modules\Academic\Entities\LessonAssessment;
use Carbon\Carbon;

class ExamReportEloquent implements ExamReportRepository
{

	use AuditLogTrait;
	use HelperTrait;
	use ReferenceTrait;

	public function create(Request $request, $subject)
	{
		$payload = $request->all();
		$this->logActivity($request, 0, $subject, 'Tambah');
		return ExamReport::create($payload);
	}

	public function update(Request $request, $subject)
	{
		$payload = Arr::only($request->all(), ['id','lesson_id','class_id','semester_id','employee_id','score_aspect_id','logged']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah');
        //
        return ExamReport::where('id', $payload['id'])->update($payload);
	}

	public function data(Request $request)
	{
		$param = $this->gridRequest($request,'asc','id');
        $query = ExamReport::select('*');
        if (auth()->user()->getDepartment->is_all != 1)
        {
            $query = $query->whereHas('getSemester', function($qry) {
                $qry->where('department_id', auth()->user()->department_id);
            });
        }
        // filter
        $fdept = isset($request->params['fdept']) ? $request->params['fdept'] : '';
        if ($fdept != '')
        {
            $query = $query->whereHas('getSemester', function($qry) use ($fdept) {
                $qry->where('department_id', $fdept);
            });
        }
        $class = isset($request->params['fclass']) ? $request->params['fclass'] : '';
        if ($class != '')
        {
            $query = $query->where('class_id', $class);
        }
        $lesson = isset($request->params['flesson']) ? $request->params['flesson'] : '';
        if ($lesson != '')
        {
            $query = $query->where('lesson_id', $lesson);
        }
        $code = isset($request->params['fcode']) ? $request->params['fcode'] : '';
        if ($code != '')
        {
            $query = $query->where('code', 'like', '%'.Str::lower($code).'%');
        }
        // result
        $result["total"] = $query->count('id');
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            $model['grade_id'] = $model->getClass->grade_id;
            $model['class'] = $model->getClass->class;
            $model['lesson'] = $model->getLesson->name;
            $model['score_aspect'] = $model->getScoreAspect->remark;
            $model['department'] = $model->getSemester->getDepartment->name;
            return $model;
        });
        return $result;
	}

	public function destroy($id, $subject)
	{
		$request = new Request();
		$this->logActivity($request, $id, $subject, 'Hapus');
        DB::table('academic.exam_report_score_finals')->where('exam_report_id', $id)->delete();
        DB::table('academic.exam_report_score_infos')->where('exam_report_id', $id)->delete();
		return ExamReport::destroy($id);
	}

    public function list(Request $request, $array)
    {
        $param = $this->gridRequest($request,'asc','student_id');
        $query = DB::table('academic.exam_report_score_finals')->select(
                    'academic.exam_report_score_finals.student_id',
                    'academic.students.student_no',
                    DB::raw('INITCAP(academic.students.name) as name'),
                )
                ->join('academic.students','academic.students.id','=','academic.exam_report_score_finals.student_id')
                ->whereIn('exam_report_id', $array);
        // filter
        $filters = json_decode($request->filterRules);
        if (count($filters) > 0)
        {
            foreach ($filters as $val)
            {
                $query = $query->where($val->field, 'like', '%'.Str::lower($val->value).'%');
            }
        }
        $query = $query->groupBy('academic.exam_report_score_finals.student_id','academic.students.student_no','academic.students.name');
        // result
        $result["total"] = $query->distinct()->count('student_id');
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get();
        return $result;
    }

	public function comboGrid(Request $request)
    {
        $query = ExamReport::select(
                    'academic.exam_reports.lesson_id',
                    'academic.exam_reports.class_id',
                    'academic.exam_reports.semester_id',
                    'academic.exam_reports.employee_id',
                    'academic.classes.grade_id',
                    'academic.classes.schoolyear_id',
                    'departments.name',
                    'academic.grades.grade',
                    'academic.schoolyears.school_year',
                    'academic.semesters.semester',
                    'academic.classes.class',
                    'academic.lessons.name',
                    'employees.name',
                )
                ->join('academic.lessons','academic.lessons.id','=','academic.exam_reports.lesson_id')
                ->join('academic.classes','academic.classes.id','=','academic.exam_reports.class_id')
                ->join('employees','employees.id','=','academic.exam_reports.employee_id')
                ->join('academic.score_aspects','academic.score_aspects.id','=','academic.exam_reports.score_aspect_id')
                ->join('academic.semesters','academic.semesters.id','=','academic.exam_reports.semester_id')
                ->join('departments','departments.id','=','academic.semesters.department_id')
                ->join('academic.grades','academic.grades.id','=','academic.classes.grade_id')
                ->join('academic.schoolyears','academic.schoolyears.id','=','academic.classes.schoolyear_id')
                ->where('academic.semesters.is_active',1)
                ->groupBy(
                    'academic.exam_reports.lesson_id',
                    'academic.exam_reports.class_id',
                    'academic.exam_reports.semester_id',
                    'academic.exam_reports.employee_id',
                    'academic.classes.grade_id',
                    'academic.classes.schoolyear_id',
                    'departments.name',
                    'academic.grades.grade',
                    'academic.schoolyears.school_year',
                    'academic.semesters.semester',
                    'academic.classes.class',
                    'academic.lessons.name',
                    'employees.name',
                );
        // result
        $result["rows"] = $query->get()->map(function($model) {
            $model['seq'] = $model->lesson_id.$model->class_id.$model->semester_id.$model->employee_id.$model->grade_id.$model->schoolyear_id;
            $model['department'] = $model->getSemester->getDepartment->name;
            $model['grade'] = $model->getClass->getGrade->grade;
            $model['school_year'] = $model->getClass->getSchoolYear->school_year;
            $model['semester'] = $model->getSemester->semester;
            $model['class'] = $model->getClass->class;
            $model['lesson'] = $model->getLesson->name;
            $model['employee'] = $model->getEmployee->title_first .' '. $model->getEmployee->name .' '. $model->getEmployee->title_end;
            return $model;
        });
        return $result;
    }

    public function getLessons(Request $request)
    {
        return ExamReport::join('academic.exam_report_score_finals','academic.exam_report_score_finals.exam_report_id','=','academic.exam_reports.id')
                    ->join('academic.exam_report_score_infos','academic.exam_report_score_infos.exam_report_id','=','academic.exam_reports.id')
                    ->where('academic.exam_reports.lesson_id', $request->lesson_id)
                    ->where('academic.exam_reports.class_id', $request->class_id)
                    ->where('academic.exam_reports.semester_id', $request->semester_id)
                    ->where('academic.exam_reports.employee_id', $request->employee_id)
                    ->where('academic.exam_report_score_finals.student_id', $request->student_id)
                    ->where('academic.exam_report_score_infos.lesson_id', $request->lesson_id)
                    ->where('academic.exam_report_score_infos.class_id', $request->class_id)
                    ->where('academic.exam_report_score_infos.semester_id', $request->semester_id)
                    ->select(
                        'academic.exam_reports.lesson_id',
                        'academic.exam_reports.class_id',
                        'academic.exam_reports.semester_id',
                        'academic.exam_reports.score_aspect_id',
                        DB::raw('academic.exam_report_score_finals.id as final_id'),
                        'academic.exam_report_score_finals.value',
                        'academic.exam_report_score_finals.value_letter',
                        'academic.exam_report_score_finals.comment',
                        DB::raw('academic.exam_report_score_infos.id as info_id'),
                        DB::raw('academic.exam_report_score_infos.value as kkm'),
                    )->get();
    }

    public function getSocials(Request $request)
    {
        return ExamReport::join('academic.exam_report_score_finals','academic.exam_report_score_finals.exam_report_id','=','academic.exam_reports.id')
                    ->join('academic.exam_report_score_infos','academic.exam_report_score_infos.exam_report_id','=','academic.exam_reports.id')
                    ->where('academic.exam_reports.lesson_id', $request->lesson_id)
                    ->where('academic.exam_reports.class_id', $request->class_id)
                    ->where('academic.exam_reports.semester_id', $request->semester_id)
                    ->where('academic.exam_reports.employee_id', $request->employee_id)
                    ->where('academic.exam_report_score_finals.student_id', $request->student_id)
                    ->where('academic.exam_report_score_infos.lesson_id', $request->lesson_id)
                    ->where('academic.exam_report_score_infos.class_id', $request->class_id)
                    ->where('academic.exam_report_score_infos.semester_id', $request->semester_id)
                    ->select(
                        'academic.exam_reports.lesson_id',
                        'academic.exam_reports.class_id',
                        'academic.exam_reports.semester_id',
                        'academic.exam_reports.score_aspect_id',
                        DB::raw('academic.exam_report_score_finals.id as final_id'),
                        'academic.exam_report_score_finals.value',
                        'academic.exam_report_score_finals.value_letter',
                        'academic.exam_report_score_finals.comment',
                        DB::raw('academic.exam_report_score_infos.id as info_id'),
                        DB::raw('academic.exam_report_score_infos.value as kkm'),
                    )->get()[0];
    }

    public function reportLeggerLessonGet($class_id, $semester_id)
    {
        return ExamReportScoreInfo::select(
                    'academic.lessons.id',
                    DB::raw('UPPER(academic.lessons.name) as lesson'),
                )
                ->join('academic.lessons','academic.lessons.id','=','academic.exam_report_score_infos.lesson_id')
                ->where('academic.exam_report_score_infos.class_id', $class_id)
                ->where('academic.exam_report_score_infos.semester_id', $semester_id)
                ->groupBy('academic.lessons.id','academic.lessons.name')
                ->get();
    }

    public function reportLeggerLessonAll($lesson_ids, $class_id, $semester_id)
    {
        return ExamReportScoreInfo::select(
                    'academic.score_aspects.id',
                    DB::raw('UPPER(academic.score_aspects.basis) as basis'),
                    DB::raw('UPPER(academic.score_aspects.remark) as remark')
                )
                ->leftJoin('academic.exam_reports','academic.exam_reports.id','=','academic.exam_report_score_infos.exam_report_id')
                ->leftJoin('academic.exam_report_score_finals','academic.exam_report_score_finals.exam_report_info_id','=','academic.exam_report_score_infos.id')
                ->leftJoin('academic.score_aspects','academic.score_aspects.id','=','academic.exam_reports.score_aspect_id')
                ->whereIn('academic.exam_report_score_infos.lesson_id', $lesson_ids)
                ->where('academic.exam_report_score_infos.class_id', $class_id)
                ->where('academic.exam_report_score_infos.semester_id', $semester_id)
                ->groupBy('academic.score_aspects.id','academic.score_aspects.basis','academic.score_aspects.remark')
                ->get();
    }

    public function reportLeggerLessonAllScores($class_id, $semester_id)
    {
        return ExamReportScoreInfo::select(
                    'academic.students.student_no',
                    DB::raw('INITCAP(academic.students.name) as student'),
                    'academic.exam_report_score_finals.value',
                    DB::raw('UPPER(academic.exam_report_score_finals.value_letter) as value_letter'),
                    'academic.exam_report_score_finals.comment',
                    'academic.exam_report_score_finals.student_id',
                    'academic.lesson_assessments.score_aspect_id',
                )
                ->join('academic.exam_report_score_finals','academic.exam_report_score_finals.exam_report_info_id','=','academic.exam_report_score_infos.id')
                ->join('academic.lesson_assessments','academic.lesson_assessments.id','=','academic.exam_report_score_finals.lesson_assessment_id')
                ->join('academic.students','academic.students.id','=','academic.exam_report_score_finals.student_id')
                ->where('academic.exam_report_score_infos.class_id', $class_id)
                ->where('academic.exam_report_score_infos.semester_id', $semester_id)
                ->orderBy('academic.students.id','asc')
                ->get();
    }

    public function reportLeggerLesson($lesson_id, $class_id, $semester_id)
    {
        return ExamReportScoreInfo::select(
                    'academic.score_aspects.id',
                    DB::raw('UPPER(academic.score_aspects.basis) as basis'),
                    DB::raw('UPPER(academic.score_aspects.remark) as remark')
                )
                ->leftJoin('academic.exam_reports','academic.exam_reports.id','=','academic.exam_report_score_infos.exam_report_id')
                ->leftJoin('academic.exam_report_score_finals','academic.exam_report_score_finals.exam_report_info_id','=','academic.exam_report_score_infos.id')
                ->leftJoin('academic.score_aspects','academic.score_aspects.id','=','academic.exam_reports.score_aspect_id')
                ->where('academic.exam_report_score_infos.lesson_id', $lesson_id)
                ->where('academic.exam_report_score_infos.class_id', $class_id)
                ->where('academic.exam_report_score_infos.semester_id', $semester_id)
                ->groupBy('academic.score_aspects.basis','academic.score_aspects.remark','academic.score_aspects.id')
                ->get();
    }

    public function reportLeggerLessonData($lesson_id, $class_id, $semester_id, $student_id, $score_aspect_id)
    {
        return ExamReportScoreInfo::select(
                    'academic.exam_report_score_finals.value',
                    'academic.exam_report_score_finals.value_letter',
                    'academic.exam_report_score_finals.comment',
                    'academic.exam_report_score_finals.student_id',
                    'academic.lesson_assessments.score_aspect_id',
                    'academic.students.student_no',
                    DB::raw('INITCAP(academic.students.name) as student')
                )
                ->join('academic.exam_reports','academic.exam_reports.id','=','academic.exam_report_score_infos.exam_report_id')
                ->join('academic.exam_report_score_finals','academic.exam_report_score_finals.exam_report_info_id','=','academic.exam_report_score_infos.id')
                ->join('academic.lesson_assessments','academic.lesson_assessments.id','=','academic.exam_report_score_finals.lesson_assessment_id')
                ->join('academic.students','academic.students.id','=','academic.exam_report_score_finals.student_id')
                ->where('academic.exam_report_score_infos.lesson_id', $lesson_id)
                ->where('academic.exam_report_score_infos.class_id', $class_id)
                ->where('academic.exam_report_score_infos.semester_id', $semester_id)
                ->where('academic.students.id', $student_id)
                ->where('academic.lesson_assessments.score_aspect_id', $score_aspect_id)
                ->orderBy('academic.students.id','asc')
                ->get();
    }

    //
    public function reportLeggerStudents($schoolyear_id, $class_id)
    {
        $schoolyear = SchoolYear::select('is_active')->where('id', $schoolyear_id)->first();
        if ($schoolyear->is_active == 1)
        {
            $students = Students::select('id','student_no', DB::raw('INITCAP(name) as student'))
                            ->where('class_id', $class_id)
                            ->where('is_active', 1)
                            ->orderBy('student_no')
                            ->get();
        } else {
            $students = Students::select('academic.students.id', 'academic.students.student_no', DB::raw('INITCAP(academic.students.name) as student'))
                            ->join('academic.student_class_histories','academic.student_class_histories.student_id','academic.students.id')
                            ->where('academic.student_class_histories.class_id', $class_id)
                            ->orderBy('academic.students.student_no')
                            ->get();
        }
        return $students;
    }

    public function reportLeggerClassGet($class_id, $semester_id, $student_id, $lesson_id)
    {
        $query = DB::table('academic.exam_report_score_finals')->select(
                    'academic.lessons.id',
                    DB::raw('UPPER(academic.lessons.name) as lesson'),
                )
                ->join('academic.exam_report_score_infos','academic.exam_report_score_infos.id','=','academic.exam_report_score_finals.exam_report_info_id')
                ->join('academic.lessons','academic.lessons.id','=','academic.exam_report_score_infos.lesson_id')
                ->join('academic.lesson_assessments','academic.lesson_assessments.id','=','academic.exam_report_score_finals.lesson_assessment_id')
                // ->where('academic.exam_report_score_infos.class_id', $class_id)
                ->where('academic.exam_report_score_infos.semester_id', $semester_id);
        if ($lesson_id > 0)
        {
            $query = $query->where('academic.exam_report_score_infos.lesson_id', $lesson_id);
        }
        $query = $query->whereIn('academic.exam_report_score_finals.student_id',$student_id)
                    ->orderBy('academic.lessons.name','asc')
                    ->groupBy('academic.lessons.id','academic.lessons.name')
                    ->get();
        return $query;
    }

    public function reportLeggerClassAll($lesson_id, $arr_student)
    {
        return DB::table('academic.exam_report_score_finals')->select(
                    'academic.score_aspects.id',
                    DB::raw('UPPER(academic.score_aspects.basis) as basis'),
                    DB::raw('UPPER(academic.score_aspects.remark) as remark')
                )
                ->join('academic.exam_report_score_infos','academic.exam_report_score_infos.id','=','academic.exam_report_score_finals.exam_report_info_id')
                ->join('academic.lessons','academic.lessons.id','=','academic.exam_report_score_infos.lesson_id')
                ->join('academic.lesson_assessments','academic.lesson_assessments.id','=','academic.exam_report_score_finals.lesson_assessment_id')
                ->join('academic.score_aspects','academic.score_aspects.id','=','academic.lesson_assessments.score_aspect_id')
                ->whereIn('academic.exam_report_score_finals.student_id',$arr_student)
                ->where('academic.lesson_assessments.lesson_id',$lesson_id)
                ->orderBy('academic.score_aspects.remark','asc')
                ->groupBy('academic.score_aspects.remark','academic.score_aspects.id','academic.score_aspects.basis')
                ->get();
    }

    public function reportLeggerClassScoreAspect($arr_aspect)
    {
        return ScoreAspect::select(
                    'academic.score_aspects.id',
                    DB::raw('UPPER(academic.score_aspects.basis) as basis'),
                    DB::raw('UPPER(academic.score_aspects.remark) as remark')
                )
                ->whereIn('id', $arr_aspect)
                ->get();
    }

    public function reportLeggerClassScoreAspectOpt()
    {
        $query = ScoreAspect::get();
        $options = array();
        foreach ($query as $index => $row)
        {
            $options[$row->id] = $row->basis;
        }
        return $options;
    }

    public function reportLeggerClassScore($student_id, $lesson_id, $semester_id, $class_id, $score_aspect_id)
    {
        return DB::table('academic.exam_report_score_finals')->select('academic.exam_report_score_finals.value')
                ->join('academic.exam_report_score_infos','academic.exam_report_score_infos.id','=','academic.exam_report_score_finals.exam_report_info_id')
                ->join('academic.lesson_assessments','academic.lesson_assessments.id','=','academic.exam_report_score_finals.lesson_assessment_id')
                ->where('academic.exam_report_score_finals.student_id', $student_id)
                ->where('academic.exam_report_score_infos.lesson_id', $lesson_id)
                ->where('academic.exam_report_score_infos.semester_id', $semester_id)
                // ->where('academic.exam_report_score_infos.class_id', $class_id)
                ->where('academic.lesson_assessments.score_aspect_id', $score_aspect_id)
                ->first();
    }

	private function logActivity(Request $request, $model_id, $subject, $action)
	{
		if ($action == 'Tambah')
		{
			$data = array(
				'lesson_id' => $request->lesson_id,
				'class_id' => $request->class_id,
				'semester_id' => $request->semester_id,
				'employee_id' => $request->employee_id,
				'score_aspect_id' => $request->score_aspect_id,
			);
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			$query = ExamReport::find($model_id);
			$before = array(
				'lesson_id' => $query->lesson_id,
				'class_id' => $query->class_id,
				'semester_id' => $query->semester_id,
				'employee_id' => $query->employee_id,
				'score_aspect_id' => $query->score_aspect_id,
			);
			$after = array(
				'lesson_id' => $request->has('lesson_id') ? $request->lesson_id : $query->lesson_id,
				'class_id' => $request->has('class_id') ? $request->class_id : $query->class_id,
				'semester_id' => $request->has('semester_id') ? $request->semester_id : $query->semester_id,
				'employee_id' => $request->has('employee_id') ? $request->employee_id : $query->employee_id,
				'score_aspect_id' => $request->has('score_aspect_id') ? $request->score_aspect_id : $query->score_aspect_id,
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
