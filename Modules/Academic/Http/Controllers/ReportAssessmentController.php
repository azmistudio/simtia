<?php

namespace Modules\Academic\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\PdfTrait;
use App\Http\Traits\ReferenceTrait;
use App\Http\Traits\DepartmentTrait;
use Modules\Academic\Entities\Semester;
use Modules\Academic\Entities\SchoolYear;
use Modules\Academic\Entities\LessonExam;
use Modules\Academic\Entities\Teacher;
use Modules\Academic\Entities\Students;
use Modules\Academic\Repositories\Academic\AcademicEloquent;
use Modules\Academic\Repositories\Exam\ExamEloquent;
use Modules\Academic\Repositories\Exam\ExamScoreEloquent;
use Modules\Academic\Repositories\Exam\ExamReportEloquent;
use Modules\Academic\Repositories\Lesson\LessonPlanEloquent;
use Modules\Academic\Repositories\Lesson\LessonDataEloquent;
use Modules\Academic\Repositories\Lesson\LessonExamTypeEloquent;
use Modules\Academic\Repositories\Teacher\TeacherEloquent;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use Carbon\Carbon;
use View;
use Exception;

class ReportAssessmentController extends Controller
{
    use HelperTrait;
    use PdfTrait;
    use ReferenceTrait;
    use DepartmentTrait;

    function __construct(
        AcademicEloquent $academicEloquent,
        ExamEloquent $examEloquent,
        ExamScoreEloquent $examScoreEloquent,
        ExamReportEloquent $examReportEloquent,
        LessonPlanEloquent $lessonPlanEloquent,
        LessonDataEloquent $lessonDataEloquent,
        LessonExamTypeEloquent $lessonExamTypeEloquent,
        TeacherEloquent $teacherEloquent,
    )
    {
        $this->academicEloquent = $academicEloquent;
        $this->examEloquent = $examEloquent;
        $this->examScoreEloquent = $examScoreEloquent;
        $this->examReportEloquent = $examReportEloquent;
        $this->lessonPlanEloquent = $lessonPlanEloquent;
        $this->lessonDataEloquent = $lessonDataEloquent;
        $this->lessonExamTypeEloquent = $lessonExamTypeEloquent;
        $this->teacherEloquent = $teacherEloquent;
    }

    /* Average Lesson Plan Class */

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function planClass(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $window = explode(".", $request->w);
        $data['InnerHeight'] = $window[0];
        $data['InnerWidth'] = $window[1];
        //
        return view('academic::reports.assessments.assessment_plan_class', $data);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function planClassData(Request $request)
    {
        return response()->json($this->lessonPlanEloquent->planClassData($request));
    }

    /**
     * Display a listing of graph.
     * @return JSON
     */
    public function planClassGraph(Request $request)
    {
        return response()->json($this->lessonPlanEloquent->planClassGraph($request->semester_id, $request->lesson_exam_id, $request->lesson_plan_id, $request->lesson_id, $request->grade_id));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function planClassToPdf(Request $request)
    {
        $payload = json_decode($request->data);
        $vals['payload'] = $payload;
        $vals['profile'] = $this->getInstituteProfile();
        $vals['data'] = $this->lessonPlanEloquent->planClassGraph($payload->semester_id, $payload->lesson_exam_id, $payload->lesson_plan_id, $payload->lesson_id, $payload->grade_id);
        $view = View::make('academic::reports.assessments.assessment_plan_class_pdf', $vals);
        $name = Str::lower(config('app.name')) .'_penilaian_avg_rpp_kelas';
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortrait($hashfile, $filename);
        echo $filename;
    }

    /* Average Lesson Plan Student */

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function planStudent(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $window = explode(".", $request->w);
        $data['InnerHeight'] = $window[0];
        $data['InnerWidth'] = $window[1];
        //
        return view('academic::reports.assessments.assessment_plan_student', $data);
    }

    /**
     * Display a listing of graph.
     * @return JSON
     */
    public function planStudentGraph(Request $request)
    {
        return response()->json($this->lessonPlanEloquent->planStudentGraph($request->semester_id, $request->class_id, $request->lesson_exam_id, $request->lesson_plan_id, $request->lesson_id, $request->grade_id));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function planStudentToPdf(Request $request)
    {
        $payload = json_decode($request->data);
        $vals['payload'] = $payload;
        $vals['profile'] = $this->getInstituteProfile();
        $vals['data'] = $this->lessonPlanEloquent->planStudentGraph($payload->semester_id, $payload->class_id, $payload->lesson_exam_id, $payload->lesson_plan_id, $payload->lesson_id, $payload->grade_id);
        $view = View::make('academic::reports.assessments.assessment_plan_student_pdf', $vals);
        $name = Str::lower(config('app.name')) .'_penilaian_avg_rpp_santri';
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortrait($hashfile, $filename);
        echo $filename;
    }

    /* Student Exam Score */

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function score(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $window = explode(".", $request->w);
        $data['InnerHeight'] = $window[0];
        $data['InnerWidth'] = $window[1];
        //
        return view('academic::reports.assessments.student_exam_score', $data);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function scoreDetail(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $data['requests'] = $request->all();
        $data['semesters'] = Semester::where('department_id', $request->department_id)->orderBy('id')->get();
        $data['exams'] = LessonExam::where('lesson_id', $request->lesson_id)->get();
        $data['scores'] = $this->examScoreEloquent->dataScoreStudent($request->student_id, $request->lesson_id, $request->class_id, 0);
        $data['scores_avg'] = $this->examScoreEloquent->dataScoreStudentAvg($request->student_id, $request->lesson_id, $request->class_id, 0);
        //
        return view('academic::reports.assessments.student_exam_score_detail', $data);
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function scoreToPdf(Request $request)
    {
        $payload = json_decode($request->data);
        $vals['payload'] = $payload;
        $vals['profile'] = $this->getInstituteProfile();
        $vals['exams'] = LessonExam::where('lesson_id', $payload->lesson_id)->get();
        $vals['scores'] = $this->examScoreEloquent->dataScoreStudent($payload->student_id, $payload->lesson_id, $payload->class_id, $payload->semester_id);
        $vals['scores_avg'] = $this->examScoreEloquent->dataScoreStudentAvg($payload->student_id, $payload->lesson_id, $payload->class_id, $payload->semester_id);
        $view = View::make('academic::reports.students.student_exam_score_pdf', $vals);
        $name = Str::lower(config('app.name')) .'_penilaian_avg_rpp_santri';
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortrait($hashfile, $filename);
        echo $filename;
    }

    /* Average Student Exam Score */

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function scoreAverage(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $window = explode(".", $request->w);
        $data['InnerHeight'] = $window[0];
        $data['InnerWidth'] = $window[1];
        //
        return view('academic::reports.assessments.student_exam_score_avg', $data);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function scoreAverageDetail(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $data['requests'] = $request->all();
        $data['semesters'] = $this->academicEloquent->reportAssessmentSemester($request->department_id, $request->lesson_id, $request->student_id);
        $data['teacher'] = $this->teacherEloquent->reportAssessmentTeacher($request->lesson_id, $request->class_id);
        $data['score_aspects'] = $this->examScoreEloquent->dataScoreAspect($request->student_id, $request->lesson_id);
        //
        return view('academic::reports.assessments.student_exam_score_avg_detail', $data);
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function scoreAverageToPdf(Request $request)
    {
        $payload = json_decode($request->data);
        $vals['payload'] = $payload;
        $vals['profile'] = $this->getInstituteProfile();
        $vals['exams'] = $this->lessonExamTypeEloquent->reportAssessment($payload->lesson_id, $payload->grade_id, $payload->score_aspect_id, $payload->employee_id);
        $view = View::make('academic::reports.students.student_exam_score_avg_pdf', $vals);
        $name = Str::lower(config('app.name')) .'_rata_ujian_santri';
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortraits($hashfile, $filename);
        echo $filename;
    }

    /* Assessment Score Legger */

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function scoreLegger(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $window = explode(".", $request->w);
        $data['InnerHeight'] = $window[0];
        $data['InnerWidth'] = $window[1];
        //
        return view('academic::reports.assessments.score_legger', $data);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function scoreLeggerView(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $data['requests'] = $request->all();
        $data['exams'] = $this->examEloquent->reportExams($request->lesson_id, $request->class_id, $request->semester_id);
        $data['exam_dates'] = $this->examEloquent->reportExamDates($request->lesson_id, $request->class_id, $request->semester_id);
        return view('academic::reports.assessments.score_legger_view', $data);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function scoreLeggerData(Request $request)
    {
        $exam_id = array();
        $exams = $this->examEloquent->reportExamDates($request->lesson_id, $request->class_id, $request->semester_id);
        foreach ($exams as $exam) 
        {
            $exam_id[] = $exam->id;
        }
        return response()->json($this->examScoreEloquent->dataScoreLegger($request->student_id, $exam_id));
    }

    /**
     * Export resource to Excel Document.
     * @return Excel
     */
    public function scoreLeggerToExcel(Request $request)
    {
        $payload = json_decode($request->data);
        $exam_id = array();
        $exams = $this->examEloquent->reportExams($payload->lesson_id, $payload->class_id, $payload->semester_id);
        $exam_dates = $this->examEloquent->reportExamDates($payload->lesson_id, $payload->class_id, $payload->semester_id);
        foreach ($exam_dates as $val) 
        {
            $exam_id[] = '_'.$val->id;
        }
        $dates = array();
        $cols = array();
        foreach ($exams as $exam)
        {
            foreach ($exam_dates as $date)
            {
                if ($exam->lesson_exam_id == $date->lesson_exam_id)
                {
                    $dates[] = array($exam->lesson_exam_id,$date->id,Carbon::createFromFormat('Y-m-d', $date->date)->format('d/m/Y'));
                    $count = array_count_values(array_column($dates,0));
                }
            }
            $cols[] = array($exam->code, $exam->subject, $exam->lesson_exam_id, $count);
        }
        //
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('laporan_legger_nilai');
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        //
        $index_col = $sheet->getColumnDimensionByColumn(3 + count($dates))->getColumnIndex();
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(25);
        //
        $sheet->mergeCells('A1:'.$index_col.'1');
        $sheet->mergeCells('A2:'.$index_col.'2');
        $sheet->mergeCells('A3:'.$index_col.'3');
        $sheet->mergeCells('A5:A6');
        $sheet->mergeCells('B5:B6');
        $sheet->mergeCells('C5:C6');
        //
        $sheet->setCellValue('A1', Str::upper($request->session()->get('institute')));
        $sheet->setCellValue('A2', 'LAPORAN LEGGER NILAI - DEPARTEMEN '. $payload->department);
        $sheet->setCellValue('A3', 'TAHUN AJARAN ' . $payload->schoolyear . ' - KELAS '. $payload->class . ' - SEMESTER '. $payload->semester . ' - PELAJARAN '. $payload->lesson);
        $sheet->setCellValue('A5', 'NO.');
        $sheet->setCellValue('B5', 'NIS');
        $sheet->setCellValue('C5', 'NAMA');
        //
        $index = 4;
        for ($i = 0; $i < count($cols); $i++)
        {
            if ($i == 0)
            {
                $index = 4;
            } else {
                $index += $cols[$i - 1][3][$cols[$i - 1][2]];
            }
            $sheet->setCellValueByColumnAndRow($index, 5, strtoupper($cols[$i][1]));
            $sheet->mergeCells($sheet->getColumnDimensionByColumn($index)->getColumnIndex().'5:'.$sheet->getColumnDimensionByColumn($index + $cols[$i][3][$cols[$i][2]] - 1)->getColumnIndex().'5');
        }
        //
        for ($i = 0; $i < count($dates); $i++)
        {
            $sheet->setCellValueByColumnAndRow($i + 4, 6, strtoupper($dates[$i][2]));
            $sheet->getColumnDimensionByColumn($i + 4)->setWidth(20);
        }
        //
        $baris = 7;
        $number = 1;
        foreach ($payload->rows as $row) 
        {
            $sheet->setCellValue('A'.$baris,$number);
            $sheet->setCellValue('B'.$baris,$row->student_no);
            $sheet->setCellValue('C'.$baris,$row->student);
            for ($i = 0; $i < count($exam_id); $i++)
            {
                $sheet->setCellValueByColumnAndRow($i + 4, $baris, $row->{$exam_id[$i]});
            }
            $baris++;
            $number++;
        }
        //
        $sheet->getStyle('A1:A2')->applyFromArray($this->PHPExcelCommonStyle()['title']);
        $sheet->getStyle('A3:A3')->applyFromArray($this->PHPExcelCommonStyle()['subTitle']);
        $sheet->getStyle('A5:'.$index_col.'6')->applyFromArray($this->PHPExcelCommonStyle()['header']);
        $sheet->getStyle('A7:A'.$baris = $baris - 1)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('B7:B'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('C7:C'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $ranges = 'D7:'.$index_col.$baris = $baris;
        $sheet->getStyle($ranges)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
        //
        $filename = date('Ymdhis') . '_' . 'simtia_laporan_legger_nilai.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save('storage/downloads/'.$filename);
        echo $filename;
    }

    /* Assessment Score Legger Lesson */

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function scoreLeggerLesson(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $window = explode(".", $request->w);
        $data['InnerHeight'] = $window[0];
        $data['InnerWidth'] = $window[1];
        //
        return view('academic::reports.assessments.score_legger_lesson', $data);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function scoreLeggerLessonView(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $data['requests'] = $request->all();
        $data['students'] = $this->examReportEloquent->reportLeggerStudents($request->schoolyear_id, $request->class_id);
        if ($request->lesson_id > 0)
        {
            $data['score_aspects'] = $this->examReportEloquent->reportLeggerLesson($request->lesson_id, $request->class_id, $request->semester_id);
            return view('academic::reports.assessments.score_legger_lesson_view', $data);
        } else {
            $data['lessons'] = $this->examReportEloquent->reportLeggerLessonGet($request->class_id, $request->semester_id);
            if (count($data['lessons']) > 0) 
            {
                $lesson_ids = collect($data['lessons'])->pluck('id')->unique()->toArray();
                $data['score_aspects'] = $this->examReportEloquent->reportLeggerLessonAll($lesson_ids, $request->class_id, $request->semester_id);
                return view('academic::reports.assessments.score_legger_lesson_all_view', $data);
            } 
        }
    }

    /**
     * Export resource to Excel Document.
     * @return Excel
     */
    public function scoreLeggerLessonToExcel(Request $request)
    {
        $data['requests'] = json_decode($request->data);
        $data['profile'] = $this->getInstituteProfile();
        $data['filename'] = date('Ymdhis') . '_' . 'simtia_laporan_legger_nilai_pelajaran.xlsx';
        $data['students'] = $this->examReportEloquent->reportLeggerStudents($data['requests']->schoolyear_id, $data['requests']->class_id);
        if ($data['requests']->is_all < 1)
        {
            $data['score_aspects'] = $this->examReportEloquent->reportLeggerLesson($data['requests']->lesson_id, $data['requests']->class_id, $data['requests']->semester_id);
            $view = View::make('academic::reports.assessments.score_legger_lesson_view_excel', $data);
        } else {
            $data['lessons'] = $this->examReportEloquent->reportLeggerLessonGet($data['requests']->class_id, $data['requests']->semester_id);
            $lesson_ids = array();
            foreach ($data['lessons'] as $row) 
            {
                $lesson_ids[] = $row->id;
            }
            $data['score_aspects'] = $this->examReportEloquent->reportLeggerLessonAll($lesson_ids, $data['requests']->class_id, $data['requests']->semester_id);
            $view = View::make('academic::reports.assessments.score_legger_lesson_all_view_excel', $data);
        }
        Storage::disk('local')->put('public/downloads/'.$data['filename'], $view->render());
        echo $data['filename'];
    }

    /* Assessment Score Legger Class */

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function scoreLeggerClass(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $window = explode(".", $request->w);
        $data['InnerHeight'] = $window[0];
        $data['InnerWidth'] = $window[1];
        //
        return view('academic::reports.assessments.score_legger_class', $data);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function scoreLeggerClassView(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $data['requests'] = $request->all();
        $lesson_id = $request->lesson_id != '' ? $request->lesson_id : 0;
        return $this->scoreLeggerClassGenerate($request->all(), $request->schoolyear_id, $request->class_id, $request->semester_id, $lesson_id, true);
    }

    /**
     * Export resource to Excel Document.
     * @return Excel
     */
    public function scoreLeggerClassToExcel(Request $request)
    {
        $data['requests'] = json_decode($request->data);
        $data['profile'] = $this->getInstituteProfile();
        $data['filename'] = date('Ymdhis') . '_' . 'simtia_laporan_legger_nilai_kelas.xlsx';
        $lesson_id = $data['requests']->lesson_id != '' ? $data['requests']->lesson_id : 0;
        $view = $this->scoreLeggerClassGenerate($data['requests'], $data['requests']->schoolyear_id, $data['requests']->class_id, $data['requests']->semester_id, $lesson_id, false);
        Storage::disk('local')->put('public/downloads/'.$data['filename'], $view->render());
        echo $data['filename'];
    }

    private function scoreLeggerClassGenerate($requests, $schoolyear_id, $class_id, $semester_id, $lesson_id, $is_view)
    {
        $data['requests'] = $requests;
        $data['aspects'] = $this->examReportEloquent->reportLeggerClassScoreAspectOpt();
        $arr_student = array();
        $data['students'] = $this->examReportEloquent->reportLeggerStudents($schoolyear_id, $class_id);
        foreach ($data['students'] as $student) 
        {
            $arr_student[] = $student->id;
        }
        //
        $arr_lesson = array();
        $arr_asp_lesson = array();
        $arr_lesson_id = array();
        $data['lessons'] = $this->examReportEloquent->reportLeggerClassGet($class_id, $semester_id, $arr_student, $lesson_id);
        if (count($data['lessons']) > 0) 
        {
            $arr_aspect_code = array();
            $aspect_codes = array();
            $score_aspects = array();
            // 
            foreach ($data['lessons'] as $lesson) 
            {
                $arr_lesson[] = array($lesson->id, $lesson->lesson);
                $arr_lesson_id[] = $lesson->id;
            }
            // 
            for ($i=0; $i < count($data['lessons']); $i++) 
            { 
                $lesson_id = $arr_lesson[$i][0];
                $lesson_aspects = $this->examReportEloquent->reportLeggerClassAll($lesson_id, $arr_student);
            
                foreach ($lesson_aspects as $aspect) 
                {
                    $asp_code = $aspect->id;
                    if (!array_key_exists($asp_code, $arr_aspect_code))
                    {
                        $arr_aspect_code[$asp_code] = 1;
                    }
                    $arrTemp[] = $asp_code;
                    $score_aspects[] = $aspect->id;
                }
                $arr_asp_lesson[$lesson_id] = $arrTemp;
            }
            // 
            $data['arr_lessons'] = $arr_lesson;
            $data['arr_asp_lessons'] = $arr_asp_lesson;
            //
            if (count($score_aspects) > 0)
            {
                $data['score_aspects'] = $this->examReportEloquent->reportLeggerClassScoreAspect($score_aspects);
                if ($is_view)
                {
                    return view('academic::reports.assessments.score_legger_class_all_view', $data);
                } else {
                    return View::make('academic::reports.assessments.score_legger_class_all_view_excel', $data);
                }
            }
        } 
    }
}
