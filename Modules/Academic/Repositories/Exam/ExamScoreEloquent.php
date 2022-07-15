<?php

namespace Modules\Academic\Repositories\Exam;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\ReferenceTrait;
use Modules\Academic\Entities\Exam;
use Modules\Academic\Entities\ExamScore;
use Carbon\Carbon;

class ExamScoreEloquent implements ExamScoreRepository
{

	use HelperTrait;
	use ReferenceTrait;

	public function dataScoreStudent($student_id, $lesson_id, $class_id, $semester_id)
    {
        $query = ExamScore::select(
                    DB::raw("to_char(academic.exams.date, 'DD/MM/YYYY') as date"),
                    'academic.exam_scores.score',
                    'academic.exam_scores.remark',
                    'academic.exams.semester_id',
                    'academic.exams.lesson_exam_id',
                )
                ->join('academic.exams','academic.exams.id','=','academic.exam_scores.exam_id')
                ->where('academic.exam_scores.student_id', $student_id)
                ->where('academic.exams.lesson_id', $lesson_id)
                ->where('academic.exams.class_id', $class_id);

        if ($semester_id > 0)
        {
            $query = $query->where('academic.exams.semester_id', $semester_id);
        }

        return $query->get();
    }

    public function dataScoreStudentAvg($student_id, $lesson_id, $class_id, $semester_id)
    {
        $query = ExamScore::select(
                    'academic.exams.semester_id',
                    'academic.exams.lesson_exam_id',
                    DB::raw('AVG(academic.exam_scores.score) as avg')
                )
                ->join('academic.exams','academic.exams.id','=','academic.exam_scores.exam_id')
                ->where('academic.exam_scores.student_id', $student_id)
                ->where('academic.exams.lesson_id', $lesson_id)
                ->where('academic.exams.class_id', $class_id);

        if ($semester_id > 0)
        {
            $query = $query->where('academic.exams.semester_id', $semester_id);
        }

        return $query->groupBy('academic.exams.semester_id','academic.exams.lesson_exam_id')->get();;
    }

    public function dataScoreAspect($student_id, $lesson_id)
    {
        return Exam::select(
                    'academic.score_aspects.id',
                    DB::raw('UPPER(academic.score_aspects.basis) as basis'),
                    DB::raw('INITCAP(academic.score_aspects.remark) as remark'),
                    'academic.exams.semester_id'
                )
                ->join('academic.exam_scores','academic.exam_scores.exam_id','=','academic.exams.id')
                ->join('academic.lesson_assessments','academic.lesson_assessments.exam_id','=','academic.exams.id')
                ->join('academic.score_aspects','academic.score_aspects.id','=','academic.lesson_assessments.score_aspect_id')
                ->where('academic.exam_scores.student_id', $student_id)
                ->where('academic.exams.lesson_id', $lesson_id)
                ->groupBy('academic.score_aspects.id','academic.score_aspects.basis','academic.score_aspects.remark','academic.exams.semester_id')
                ->get();
    }

    public function dataScoreLegger($student_id, $exam_id)
    {
        $sorted_exam = Arr::sort($exam_id);
        $string = "";
        $arr_exam = implode(',', $sorted_exam);
        foreach ($sorted_exam as $col)
        {
            $string .= ' _' . $col . ' NUMERIC,';
        }
        if (!empty($arr_exam) && !empty($string))
        {
            //
            $query_rows = DB::select("SELECT academic.fn_pivot_exam_scores('".$arr_exam."','".rtrim($string, ',')."')");
            $rows = DB::select($query_rows[0]->fn_pivot_exam_scores);
            $total = collect(DB::select($query_rows[0]->fn_pivot_exam_scores)); 
            //
            $result["total"] = $total->count();
            $result["rows"] = $rows;
        } else {
            $result["total"] = 0;
            $result["rows"] = [];
        }
        return $result;
    }
}