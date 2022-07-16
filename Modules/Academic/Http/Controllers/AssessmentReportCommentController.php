<?php

namespace Modules\Academic\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\ReferenceTrait;
use App\Http\Traits\AuditLogTrait;
use App\Models\Reference;
use Modules\Academic\Entities\LessonAssessment;
use Modules\Academic\Entities\ScoreAspect;
use Modules\Academic\Entities\Students;
use Modules\Academic\Entities\ExamReport;
use Modules\Academic\Entities\ExamReportScoreFinal;
use Modules\Academic\Entities\ExamReportComment;
use Modules\Academic\Entities\ExamReportCommentLesson;
use Modules\Academic\Entities\ExamReportCommentSocial;
use Modules\Academic\Http\Requests\ExamReportRequest;
use Modules\Academic\Repositories\Lesson\LessonAssessmentEloquent;
use Modules\Academic\Repositories\Exam\ExamReportEloquent;
use Modules\Academic\Repositories\Exam\ExamReportCommentEloquent;
use Carbon\Carbon;
use View;
use Exception;

class AssessmentReportCommentController extends Controller
{
    use HelperTrait;
    use ReferenceTrait;
    use AuditLogTrait;

    private $subject_comment = 'Data Komentar Nilai Rapor';
    private $subject_social = 'Data Komentar Spiritual & Sosial';
    private $subject_template_lesson = 'Data Template Komentar Pelajaran';
    private $subject_template_social = 'Data Template Komentar Sosial';

    function __construct(
        LessonAssessmentEloquent $lessonAssessmentEloquent,
        ExamReportEloquent $examReportEloquent,
        ExamReportCommentEloquent $examReportCommentEloquent
    )
    {
        $this->lessonAssessmentEloquent = $lessonAssessmentEloquent;
        $this->examReportEloquent = $examReportEloquent;
        $this->examReportCommentEloquent = $examReportCommentEloquent;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $window = explode(".", $request->w);
        $data['InnerHeight'] = $window[0];
        $data['InnerWidth'] = $window[1];
        $data['ViewType'] = $request->t;
        //
        return view('academic::pages.assessments.assessment_report_comment', $data);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexLesson(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        //
        $data['students'] = array('student_id' => $request->student_id, 'student_no' => $request->student_no, 'student_name' => $request->student_name);
        $data['reports'] = $this->examReportEloquent->getLessons($request);
        $data['grade'] = $request->grade_id;
        $data['templates'] = ExamReportCommentLesson::where('is_active', 1)->get();
        return view('academic::pages.assessments.assessment_report_comment_lesson', $data);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexSocial(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        //
        $data['types'] = Reference::select('id','name')->where('category', 'hr_social_comment_type')->get();
        $data['students'] = array('student_id' => $request->student_id, 'student_no' => $request->student_no, 'student_name' => $request->student_name);
        $data['report'] = $this->examReportEloquent->getSocials($request);
        $data['grade'] = $request->grade_id;
        $data['templates'] = ExamReportCommentSocial::where('is_active', 1)->orderBy('id')->get();
        $data['comments'] = ExamReportComment::where('student_id', $request->student_id)
                                ->where('class_id', $request->class_id)
                                ->where('semester_id', $request->semester_id)
                                ->orderBy('id')
                                ->get();
        return view('academic::pages.assessments.assessment_report_comment_social', $data);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexView(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        //
        $data['exams'] = ExamReport::where('academic.exam_reports.lesson_id', $request->lesson_id)
                            ->where('academic.exam_reports.class_id', $request->class_id)
                            ->where('academic.exam_reports.semester_id', $request->semester_id)
                            ->where('academic.exam_reports.employee_id', $request->employee_id)
                            ->get()->map(function ($model) {
                                $model['remark'] = $model->getScoreAspect->remark;
                                return $model;
                            });
        $idArray = collect($data['exams'])->pluck('id')->toArray();
        $data['finals'] = ExamReportScoreFinal::select('*')->whereIn('exam_report_id', $idArray)
                            ->get()->map(function ($model) {
                                $model['student_no'] = $model->getStudent->student_no;
                                $model['student'] = $model->getStudent->name;
                                $model['score_aspect_id'] = $model->getExamReport->score_aspect_id;
                                return $model;
                            });
        $data['students'] = Students::select('id','student_no','name')->where('class_id', $request->class_id)->where('is_active',1)->get();
        $data['comments'] = ExamReportComment::where('class_id', $request->class_id)
                                ->where('semester_id', $request->semester_id)
                                ->orderByDesc('aspect')
                                ->get()->map(function ($model) {
                                    $model['type'] = $model->getType->name;
                                    return $model;
                                });
        return view('academic::pages.assessments.assessment_report_comment_view', $data);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexTemplate(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $title = ScoreAspect::select('remark')->where('id', $request->score_aspect_id)->first();
        $data['grade'] = $request->grade_id;
        $data['title'] = isset($title) ? $title->remark : $request->remark;
        $data['type_id'] = isset($request->type_id) ? $request->type_id : '-1';
        $data['type'] = isset($request->type_id) ? 'social' : 'lesson';
        $data['requests'] = $request->all();
        if (!isset($request->aspect))
        {
            $data['templates'] = ExamReportCommentLesson::find($request->id);
        } else {
            $data['templates'] = ExamReportCommentSocial::find($request->id);
        }
        return view('academic::pages.assessments.comment_template', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        try 
        {
            if ($request->type == 'lesson')
            {
                for ($i=0; $i < count($request->final_id); $i++) 
                { 
                    ExamReportScoreFinal::where('id', $request->final_id[$i])->update(['comment' => $request->comment[$i]]);
                }
                $response = $this->getResponse('store', '', $this->subject_comment);
            } else {
                $this->examReportCommentEloquent->upsert($request, $this->subject_social);
                $response = $this->getResponse('store', '', $this->subject_social);
            }
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), '');
        }
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @return Renderable
     */
    public function destroy(Request $request)
    {
        try 
        {
            $exams = ExamReport::where('lesson_id', $request->lesson_id)->where('class_id', $request->class_id)->where('semester_id', $request->semester_id)->where('employee_id', $request->employee_id)->get();
            foreach ($exams as $exam) 
            {
                ExamReportScoreFinal::where('exam_report_id', $exam->id)->where('student_id', $request->student_id)->update(['comment' => '']);
            }
            $this->examReportCommentEloquent->destroy($request, $this->subject_comment);
            $response = $this->getResponse('destroy', '', $this->subject_comment);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), $this->subject_comment);
        }
        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function storeTemplate(Request $request)
    {
        try 
        {
            if ($request->id < 1) 
            {
                $request->merge([
                    'is_active' => 1,
                ]);
                if ($request->type == 'lesson')
                {
                    $this->examReportCommentEloquent->createLesson($request, $this->subject_template_lesson);
                    $response = $this->getResponse('store', '', $this->subject_template_lesson);
                } else {
                    $this->examReportCommentEloquent->createSocial($request, $this->subject_template_social);
                    $response = $this->getResponse('store', '', $this->subject_template_social);
                }
            } else {
                if ($request->type == 'lesson')
                {
                    $this->examReportCommentEloquent->updateLesson($request, $this->subject_template_lesson);
                    $response = $this->getResponse('store', '', $this->subject_template_lesson);
                } else {
                    $this->examReportCommentEloquent->updateSocial($request, $this->subject_template_social);
                    $response = $this->getResponse('store', '', $this->subject_template_social);
                }
            }
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), '');
        }
        return response()->json($response);
    }

    public function combobox(Request $request)
    {
        if ($request->type == 'lesson')
        {
            $templates = ExamReportCommentLesson::where('lesson_id', $request->lesson_id)
                            ->where('score_aspect_id', $request->score_aspect_id)
                            ->where('grade_id', $request->grade_id)
                            ->where('is_active', 1)
                            ->get();
        } else {
            $templates = ExamReportCommentSocial::where('lesson_id', $request->lesson_id)
                            ->where('grade_id', $request->grade_id)
                            ->where('aspect', $request->aspect)
                            ->where('is_active', 1)
                            ->get();
        }
        $result = array();
        $i = 1;
        foreach ($templates as $template) 
        {
            $result[] = array(
                'id' => $template->id,
                'name' => $i.'. '. substr(strip_tags(html_entity_decode($template->comment)), 0, 25),
            );
            $i++;
        }
        return response()->json($result);
    }

    public function comboboxValue(Request $request, $id)
    {
        if ($request->type == 'lesson')
        {
            $templates = ExamReportCommentLesson::find($id);
        } else {
            $templates = ExamReportCommentSocial::find($id);
        }
        return $templates->comment;
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroyTemplate(Request $request)
    {
        try 
        {
            if ($request->type == 'lesson')
            {
                $this->examReportCommentEloquent->destroyLesson($request->id, $this->subject_template_lesson);
                $response = $this->getResponse('destroy', '', $this->subject_template_lesson);
            } else {
                $this->examReportCommentEloquent->destroySocial($request->id, $this->subject_template_social);
                $response = $this->getResponse('destroy', '', $this->subject_template_social);
            }
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), '');
        }
        return response()->json($response);
    }
}
