<?php

namespace Modules\Academic\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use App\Models\Reference;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\PdfTrait;
use App\Http\Traits\ReferenceTrait;
use App\Http\Traits\DepartmentTrait;
use Modules\Academic\Entities\Classes;
use Modules\Academic\Entities\Students;
use Modules\Academic\Entities\Lesson;
use Modules\Academic\Entities\LessonSchedule;
use Modules\Academic\Entities\PresenceDaily;
use Modules\Academic\Entities\PresenceDailyStudent;
use Modules\Academic\Entities\PresenceLesson;
use Modules\Academic\Entities\PresenceLessonStudent;
use Modules\Academic\Entities\PresenceLessonStudentPresent;
use Modules\Academic\Http\Requests\PresenceDailyRequest;
use Modules\Academic\Http\Requests\PresenceLessonRequest;
use Modules\Academic\Repositories\Presence\PresenceDailyEloquent;
use Modules\Academic\Repositories\Presence\PresenceLessonEloquent;
use Carbon\Carbon;
use View;
use Exception;

class PresenceController extends Controller
{

    use HelperTrait;
    use PdfTrait;
    use ReferenceTrait;
    use DepartmentTrait;

    private $subject_daily = 'Data Presensi Harian';
    private $subject_lesson = 'Data Presensi Pelajaran';

    function __construct(
        PresenceDailyEloquent $presenceDailyEloquent, 
        PresenceLessonEloquent $presenceLessonEloquent
    )
    {
        $this->presenceDailyEloquent = $presenceDailyEloquent;
        $this->presenceLessonEloquent = $presenceLessonEloquent;
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
        $data['departments'] = $this->listDepartment();
        return view('academic::pages.presences.presence_daily', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(PresenceDailyRequest $request)
    {
        $validated = $request->validated();
        try 
        {
            // check period
            if (strtotime($this->formatDate($request->start_date,'sys')) < strtotime($request->start))
            {
                throw new Exception('Tanggal Awal tidak boleh lebih kecil dari Awal Periode.', 1);
            } elseif (strtotime($this->formatDate($request->end_date,'sys')) > strtotime($request->end)) {
                throw new Exception('Tanggal Akhir tidak boleh lebih besar dari Akhir Periode.', 1);
            } elseif (strtotime($this->formatDate($request->end_date,'sys')) < strtotime($this->formatDate($request->start_date,'sys'))) {
                throw new Exception('Tanggal Akhir tidak boleh lebih kecil dari Tanggal Awal.', 1);
            } else {
                // check presence
                for ($i=0; $i < count($request->students); $i++) 
                {
                    $total = $request->students[$i]['present'] + $request->students[$i]['permit'] + $request->students[$i]['sick'] + $request->students[$i]['absent'] + $request->students[$i]['leave']; 
                    if ($total != $request->active_day)
                    {
                        throw new Exception('Jumlah presensi tiap santri harus berjumlah ' . $request->active_day . ' hari.', 1);
                    }
                }
            }
            $request->merge([
                'start_date' => $this->formatDate($request->start_date,'sys'),
                'end_date' => $this->formatDate($request->end_date,'sys'),
                'logged' => auth()->user()->email,
            ]);
            if ($request->id < 1) 
            {
                $query = $this->presenceDailyEloquent->create($request, $this->subject_daily);
                // 
                for ($i=0; $i < count($request->students); $i++) 
                {
                    $total = $request->students[$i]['present'] + $request->students[$i]['permit'] + $request->students[$i]['sick'] + $request->students[$i]['absent'] + $request->students[$i]['leave']; 
                    if ($total > 0)
                    {
                        $students = new PresenceDailyStudent;
                        $students->presence_id = $query->id;
                        $students->student_id = $request->students[$i]['id'];
                        $students->present = $request->students[$i]['present'];
                        $students->permit = $request->students[$i]['permit'];
                        $students->sick = $request->students[$i]['sick'];
                        $students->absent = $request->students[$i]['absent'];
                        $students->leave = $request->students[$i]['leave'];
                        $students->remark = $request->students[$i]['remark'];
                        $students->logged = auth()->user()->email;
                        $students->save();
                    }
                }
                $response = $this->getResponse('store', '', $this->subject_daily);
            } else {
                PresenceDailyStudent::where('presence_id', $request->id)->delete();
                for ($i=0; $i < count($request->students); $i++) 
                {
                    $total = $request->students[$i]['present'] + $request->students[$i]['permit'] + $request->students[$i]['sick'] + $request->students[$i]['absent'] + $request->students[$i]['leave']; 
                    if ($total > 0)
                    {
                        $students = new PresenceDailyStudent;
                        $students->presence_id = $request->id;
                        $students->student_id = $request->students[$i]['id'];
                        $students->present = $request->students[$i]['present'];
                        $students->permit = $request->students[$i]['permit'];
                        $students->sick = $request->students[$i]['sick'];
                        $students->absent = $request->students[$i]['absent'];
                        $students->leave = $request->students[$i]['leave'];
                        $students->remark = $request->students[$i]['remark'];
                        $students->logged = auth()->user()->email;
                        $students->save();
                    }
                }
                $this->presenceDailyEloquent->update($request, $this->subject_daily);
                $response = $this->getResponse('store', '', $this->subject_daily);
            }
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Kombinasi Semester, Kelas, Tanggal Awal dan Tanggal Akhir');
        }
        return response()->json($response);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return response()->json(PresenceDaily::where('id', $id)->get()->map(function($model){
            $model['department'] = $model->getSemester->getDepartment->name;
            $model['grade'] = $model->getClass->getGrade->grade;
            $model['semester'] = $model->getSemester->semester;
            $model['school_year'] = $model->getClass->getSchoolYear->school_year;
            $model['period'] = $model->getClass->getSchoolYear->start_date->format('d/m/Y') .' s.d '. $model->getClass->getSchoolYear->end_date->format('d/m/Y');
            $model['period_start'] = $model->getClass->getSchoolYear->start_date;
            $model['period_end'] = $model->getClass->getSchoolYear->end_date;
            return $model;
        })[0]);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try 
        {
            $this->presenceDailyEloquent->destroy($id, $this->subject_daily);
            $response = $this->getResponse('destroy', '', $this->subject_daily);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), $this->subject_daily);
        }
        return response()->json($response);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function data(Request $request)
    {
        return response()->json($this->presenceDailyEloquent->data($request));
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function list(Request $request)
    {
        return response()->json($this->presenceDailyEloquent->list($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function print(Request $request)
    {
        $payload = json_decode($request->data);
        $data['presences'] = PresenceDaily::find($payload->id);
        $data['students'] = PresenceDailyStudent::where('presence_id', $payload->id)->get();                     
        $data['profile'] = $this->getInstituteProfile();
        //
        $view = View::make('academic::pages.presences.presence_daily_pdf', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject_daily)->snake();
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortrait($hashfile, $filename);
        echo $filename;
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function printForm(Request $request)
    {
        $payload = json_decode($request->data);
        $data['header'] = Classes::where('id', $payload->id)->first();
        $data['body'] = Students::select('student_no','name')->where('class_id', $payload->id)->where('is_active', 1)->get();
        $data['profile'] = $this->getInstituteProfile();
        //
        $view = View::make('academic::pages.presences.presence_daily_form_pdf', $data);
        $name = Str::lower(config('app.name')) .'_form_'. Str::of($this->subject_daily)->snake() .'_'. $payload->id;
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortrait($hashfile, $filename);
        echo $filename;
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
        $window = explode(".", $request->w);
        $data['InnerHeight'] = $window[0];
        $data['InnerWidth'] = $window[1];
        $data['ViewType'] = $request->t;
        //
        $data['teacher_status'] = Reference::where('category','hr_teacher_status')->get();
        $data['lessons'] = Lesson::where('is_active',1)->get();
        $data['departments'] = $this->listDepartment();
        return view('academic::pages.presences.presence_lesson', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function storeLesson(PresenceLessonRequest $request)
    {
        $validated = $request->validated();
        try 
        {
            $isValid = true;
            $dates = $request->date;
            unset($dates[0]);
            $params = explode('-', $request->lesson_id);
            if ($isValid) 
            {
                $request->merge([
                    'lesson_id' => $params[3],
                    'times' => sprintf('%02d', $request->teach_hour) . ':' . sprintf('%02d', $request->teach_minute),
                    'time' => LessonSchedule::select('start')->where('id', $request->lesson_schedule_id)->first()->start,
                    'logged' => auth()->user()->email,
                ]);
                if ($request->id < 1) 
                {
                    $ids = array();
                    for ($i=1; $i <= count($dates); $i++)
                    {
                        $request->merge([
                            'date' => $dates[$i]
                        ]);
                        $query = $this->presenceLessonEloquent->create($request, $this->subject_lesson);
                        $ids[] = array($query->id, substr($dates[$i], 5, 2), substr($dates[$i], 0, 4));
                    }
                    // 
                    foreach ($ids as $val) 
                    {
                        for ($i=0; $i < count($request->students); $i++) 
                        {
                            $students = new PresenceLessonStudent;
                            $students->presence_id = $val[0];
                            $students->student_id = $request->students[$i]['id'];
                            $students->presence = $this->setPresence($request->students[$i]['presence']);
                            $students->remark = isset($request->students[$i]['remark']) ? $request->students[$i]['remark'] : '';
                            $students->logged = auth()->user()->email;
                            $students->save();
                            //
                            DB::select("SELECT academic.sp_presence_lesson_student_presents(
                                ".$request->students[$i]['id'].'::int8'.",
                                ".$request->class_id.'::int8'.",
                                ".$request->semester_id.'::int8'.",
                                ".$request->lesson_id.'::int8'.",
                                ".$request->employee_id.'::int8'.",
                                ".intval($val[1]).'::int2'.",
                                ".$val[2].'::int2'.",
                                '".auth()->user()->email."'
                            )");
                        }
                    }
                    $response = $this->getResponse('store', '', $this->subject_lesson);
                } else {
                    $array_date = explode(',', $dates[1]);
                    $month = substr(ltrim($array_date[1]), 3,2);
                    $year = substr(ltrim($array_date[1]), 6,4);
                    PresenceLessonStudent::where('presence_id', $request->id)->delete();
                    for ($i=0; $i < count($request->students); $i++) 
                    {
                        $students = new PresenceLessonStudent;
                        $students->presence_id = $request->id;
                        $students->student_id = $request->students[$i]['id'];
                        $students->presence = $this->setPresence($request->students[$i]['presence']);
                        $students->remark = isset($request->students[$i]['remark']) ? $request->students[$i]['remark'] : '';
                        $students->logged = auth()->user()->email;
                        $students->save();
                        //
                        DB::select("SELECT academic.sp_presence_lesson_student_presents(
                            ".$request->students[$i]['id'].'::int8'.",
                            ".$request->class_id.'::int8'.",
                            ".$request->semester_id.'::int8'.",
                            ".$request->lesson_id.'::int8'.",
                            ".$request->employee_id.'::int8'.",
                            ".intval($month).'::int2'.",
                            ".$year.'::int2'.",
                            '".auth()->user()->email."'
                        )");
                    }
                    $request->merge([
                        'date' => Carbon::createFromFormat('d/m/Y',ltrim($array_date[1]))->format('Y-m-d')
                    ]);
                    $this->presenceLessonEloquent->update($request, $this->subject_lesson);
                    $response = $this->getResponse('store', '', $this->subject_lesson);
                }
            }
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Kombinasi Semester, Kelas, Pelajaran, Tanggal dan Guru');
        }
        return response()->json($response);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function dataLesson(Request $request)
    {
        return response()->json($this->presenceLessonEloquent->data($request));
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function showLesson($id)
    {
        return response()->json(PresenceLesson::where('id', $id)->get()->map(function($model){
            $model['department'] = $model->getSemester->getDepartment->name;
            $model['department_id'] = $model->getSemester->getDepartment->id;
            $model['grade'] = $model->getClass->getGrade->grade;
            $model['semester'] = $model->getSemester->semester;
            $model['school_year'] = $model->getClass->getSchoolYear->school_year;
            $model['teacher'] = $model->getEmployee->title_first .' '. $model->getEmployee->name .' '. $model->getEmployee->title_end;
            $model['status'] = $model->getTeacherType->name;
            $model['lesson'] = $model->getLesson->name;
            $model['class'] = $model->getClass->class;
            $model['start_date'] = $model->getClass->getSchoolYear->start_date;
            $model['end_date'] = $model->getClass->getSchoolYear->end_date;
            $model['lesson_schedule'] = $model->lesson_schedule_id;
            $model['p_date'] = $this->getFullDay()[$model->getLessonSchedule->day] .', '. Carbon::createFromFormat('Y-m-d', $model->date)->format('d/m/Y');
            return $model;
        })[0]);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function listLesson(Request $request)
    {
        return response()->json($this->presenceLessonEloquent->list($request));
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroyLesson(Request $request)
    {
        try 
        {
            $dates = $request->dates;
            unset($dates[0]);
            $array_date = explode(',', $dates[1]);
            $month = substr(ltrim($array_date[1]), 3,2);
            $year = substr(ltrim($array_date[1]), 6,4);
            $lesson_id = PresenceLesson::select('lesson_id')->where('id', $request->id)->first()->lesson_id;
            $this->presenceLessonEloquent->destroy($request->id, $this->subject_lesson);
            for ($i=0; $i < count($request->students); $i++) 
            {
                DB::select("SELECT academic.sp_presence_lesson_student_presents(
                    ".$request->students[$i]['id'].'::int8'.",
                    ".$request->class_id.'::int8'.",
                    ".$request->semester_id.'::int8'.",
                    ".$lesson_id.'::int8'.",
                    ".$request->employee_id.'::int8'.",
                    ".intval($month).'::int2'.",
                    ".$year.'::int2'.",
                    '".auth()->user()->email."'
                )");
            }
            $response = $this->getResponse('destroy', '', $this->subject_lesson);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), $this->subject_lesson);
        }
        return response()->json($response);
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function printFormLesson(Request $request)
    {
        $payload = json_decode($request->data);
        $data['header'] = Classes::where('id', $payload->id)->first();
        $data['body'] = Students::select('student_no','name')->where('class_id', $payload->id)->where('is_active', 1)->get();
        $data['lesson'] = $payload->lesson;
        $data['profile'] = $this->getInstituteProfile();
        //
        $view = View::make('academic::pages.presences.presence_lesson_form_pdf', $data);
        $name = Str::lower(config('app.name')) .'_form_'. Str::of($this->subject_daily)->snake() .'_'. $payload->id;
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortraits($hashfile, $filename);
        echo $filename;
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function comboGridLesson(Request $request)
    {
        return response()->json($this->presenceLessonEloquent->combogrid($request));
    }
}
