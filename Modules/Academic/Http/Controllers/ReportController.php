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
use Modules\Academic\Entities\Admission;
use Modules\Academic\Entities\SchoolYear;
use Modules\Academic\Repositories\Admission\AdmissionReportEloquent;
use Modules\Academic\Repositories\Student\StudentReportEloquent;
use Modules\Academic\Repositories\Presence\PresenceDailyEloquent;
use Modules\Academic\Repositories\Presence\PresenceLessonEloquent;
use Modules\Academic\Repositories\Academic\AcademicEloquent;
use Modules\Academic\Repositories\Lesson\LessonDataEloquent;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use View;
use Exception;

class ReportController extends Controller
{
    use HelperTrait;
    use PdfTrait;
    use ReferenceTrait;
    use DepartmentTrait;

    function __construct(
        AdmissionReportEloquent $admissionReportEloquent,
        StudentReportEloquent $studentReportEloquent,
        PresenceDailyEloquent $presenceDailyEloquent,
        PresenceLessonEloquent $presenceLessonEloquent,
        AcademicEloquent $academicEloquent,
        LessonDataEloquent $lessonDataEloquent,
    )
    {
        $this->admissionReportEloquent = $admissionReportEloquent;
        $this->studentReportEloquent = $studentReportEloquent;
        $this->presenceDailyEloquent = $presenceDailyEloquent;
        $this->presenceLessonEloquent = $presenceLessonEloquent;
        $this->academicEloquent = $academicEloquent;
        $this->lessonDataEloquent = $lessonDataEloquent;
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
        return view('academic::reports.index', $data);
    }

    /* Reference */

    /**
     * Display a listing of data for combobox.
     * @return JSON
     */
    public function comboBoxGrade($id, $is_all)
    {
        $all[] = array('value' => 0, 'text' => 'Semua');
        $grades = $this->academicEloquent->comboboxGrade($id);
        foreach ($grades as $grade) 
        {
            $options[] = array(
                'value' => $grade->id,
                'text' => $grade->text
            );
        }
        $result = $is_all > 0 ? array_merge($all, $options) : $options;
        return response()->json($result);
    }

    /**
     * Display a listing of data for combobox.
     * @return JSON
     */
    public function comboBoxClass($grade_id, $schoolyear_id, $is_all)
    {
        $all[] = array('value' => 0, 'text' => 'Semua');
        $classes = $this->academicEloquent->comboBoxClass($grade_id, $schoolyear_id);
        if (count($classes) > 0)
        {
            foreach ($classes as $class) 
            {
                $options[] = array(
                    'value' => $class->id,
                    'text' => $class->text
                );
            }
            $result = $is_all > 0 ? array_merge($all, $options) : $options;
            return response()->json($result);
        } else {
            return response()->json(array());
        }
    }

    /**
     * Display a listing of data for combobox.
     * @return JSON
     */
    public function comboBoxLesson($id, $is_all)
    {
        $all[] = array('value' => 0, 'text' => 'Semua');
        $lessons = $this->lessonDataEloquent->combobox($id);
        foreach ($lessons as $lesson) 
        {
            $options[] = array(
                'value' => $lesson->id,
                'text' => Str::title($lesson->text)
            );
        }
        if ($is_all > 0)
        {
            $result = count($lessons) > 0 ? array_merge($all, $options) : $all;
        } else {
            $result = $options;
        }
        return response()->json($result);
    }

    /* Admission Stat */

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function admissionProspect(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $window = explode(".", $request->w);
        $data['InnerHeight'] = $window[0];
        $data['InnerWidth'] = $window[1];
        //
        return view('academic::reports.admissions.admission_prospect', $data);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function admissionProspectData(Request $request)
    {
        return response()->json($this->admissionReportEloquent->admissionProspectData($request));
    }

    /* Admission Stat */

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function admissionStat(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $window = explode(".", $request->w);
        $data['InnerHeight'] = $window[0];
        $data['InnerWidth'] = $window[1];
        //
        return view('academic::reports.admissions.admission_stat', $data);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function admissionStatData(Request $request)
    {
        try 
        {
            $result = $this->admissionReportEloquent->admissionStatData($request);
            $response = [ 'success' => true, 'message' => '', 'data' => $result ];
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), '');
        }
        return response()->json($response);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function admissionStatDataDetail(Request $request)
    {
        try 
        {
            $result = $this->admissionReportEloquent->admissionStatDataDetail($request);
            $response = [ 'success' => true, 'message' => '', 'data' => $result ];
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), '');
        }
        return response()->json($response);
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function admissionStatPrint(Request $request)
    {
        $payload = json_decode($request->data);
        $result = $this->admissionReportEloquent->admissionStatData($request);
        $total = 0;
        foreach ($result as $q) 
        {
            $total += $q->y;
        }
        foreach ($result as $q) 
        {
            $vals['rows'][] = array(
                'subject' => $q->label,
                'total' => $q->y,
                'percent' => number_format(($q->y / $total) *100, 2) . '%',
            );
        }
        $vals['payloads'] = $payload;
        $vals['subtitle'] = 'Asal Sekolah';
        switch ($payload->category) 
        {
            case 'blood_type':
                $vals['subtitle'] = 'Golongan Darah';
                break;
            case 'gender':
                $vals['subtitle'] = 'Jenis Kelamin';
                break;
            case 'tribe':
                $vals['subtitle'] = 'Suku';
                break;
            case 'born':
                $vals['subtitle'] = 'Tahun Lahir';
                break;
            case 'age':
                $vals['subtitle'] = 'Usia';
                break;
            default:
                break;
        }
        $vals['profile'] = $this->getInstituteProfile();
        $view = View::make('academic::reports.admissions.admission_stat_pdf', $vals);
        $name = Str::lower(config('app.name')) .'_statistik_psb';
        $hashfile = md5(date('Ymdhis') . '_' . $name) . '.html';
        // 
        Storage::disk('local')->put('public/downloads/'.$hashfile, $view->render());
        echo $hashfile;
    }

    /* Student Stat */

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function studentStat(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $window = explode(".", $request->w);
        $data['InnerHeight'] = $window[0];
        $data['InnerWidth'] = $window[1];
        //
        return view('academic::reports.students.student_stat', $data);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function studentStatData(Request $request)
    {
        try 
        {
            $result = $this->studentReportEloquent->studentStatData($request);
            $response = [ 'success' => true, 'message' => '', 'data' => $result ];
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), '');
        }
        return response()->json($response);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function studentStatDataDetail(Request $request)
    {
        try 
        {
            $result = $this->studentReportEloquent->studentStatDataDetail($request);
            $response = [ 'success' => true, 'message' => '', 'data' => $result ];
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), '');
        }
        return response()->json($response);
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function studentStatPrint(Request $request)
    {
        $payload = json_decode($request->data);
        $result = $this->studentReportEloquent->studentStatData($request);
        $total = 0;
        foreach ($result as $q) 
        {
            $total += $q->y;
        }
        foreach ($result as $q) 
        {
            $vals['rows'][] = array(
                'subject' => $q->subject,
                'total' => $q->y,
                'percent' => number_format(($q->y / $total) *100, 2) . '%',
            );
        }
        $vals['payloads'] = $payload;
        $vals['subtitle'] = 'Asal Sekolah';
        switch ($payload->category) 
        {
            case 'blood_type':
                $vals['subtitle'] = 'Golongan Darah';
                break;
            case 'gender':
                $vals['subtitle'] = 'Jenis Kelamin';
                break;
            case 'tribe':
                $vals['subtitle'] = 'Suku';
                break;
            case 'born':
                $vals['subtitle'] = 'Tahun Lahir';
                break;
            case 'age':
                $vals['subtitle'] = 'Usia';
                break;
            default:
                break;
        }
        $vals['profile'] = $this->getInstituteProfile();
        $view = View::make('academic::reports.students.student_stat_pdf', $vals);
        $name = Str::lower(config('app.name')) .'_statistik_santri';
        $hashfile = md5(date('Ymdhis') . '_' . $name) . '.html';
        // 
        Storage::disk('local')->put('public/downloads/'.$hashfile, $view->render());
        echo $hashfile;
    }

    /* Student Mutation Stat */

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function studentMutation(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $window = explode(".", $request->w);
        $data['InnerHeight'] = $window[0];
        $data['InnerWidth'] = $window[1];
        //
        $data['departments'] = $this->allDepartment();
        return view('academic::reports.students.student_mutation_stat', $data);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function studentMutationData(Request $request)
    {
        return response()->json($this->studentReportEloquent->studentMutationStatData($request->start, $request->end, $request->department_id));
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function studentMutationDataDetail(Request $request)
    {
        return response()->json($this->studentReportEloquent->studentMutationStatDataDetail($request->start, $request->end, $request->department_id, $request->mutation_id));
    }

    /**
     * Display graph resource.
     * @return JSON
     */
    public function studentMutationGraph(Request $request)
    {
        return response()->json($this->studentReportEloquent->studentMutationGraph($request->start, $request->end, $request->department_id, $request->department));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function studentMutationToPdf(Request $request)
    {
        $payload = json_decode($request->data);
        $total = array_sum(array_column($payload->rows, 'total'));
        $data['payload'] = $payload;
        $data['profile'] = $this->getInstituteProfile();
        $data['graph'] = $total > 0 ? $this->studentReportEloquent->studentMutationGraph($payload->start, $payload->end, $payload->department_id, $payload->department) : '';
        $data['details'] = $this->studentReportEloquent->studentMutationStatDataDetail($payload->start, $payload->end, $payload->department_id, 0);
        $view = View::make('academic::reports.students.student_mutation_stat_pdf', $data);
        $name = Str::lower(config('app.name')) .'_statistik_mutasi_santri';
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortraits($hashfile, $filename);
        echo $filename;
    }
}
