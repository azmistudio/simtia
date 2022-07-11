<?php

namespace Modules\Academic\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Reference;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\PdfTrait;
use App\Http\Traits\ReferenceTrait;
use App\Http\Traits\DepartmentTrait;
use App\Http\Traits\AuditLogTrait;
use Modules\Academic\Entities\LessonSchedule;
use Modules\Academic\Entities\LessonScheduleTime;
use Modules\Academic\Entities\LessonScheduleInfo;
use Modules\Academic\Entities\LessonScheduleTeaching;
use Modules\Academic\Http\Requests\LessonTeachingRequest;
use Modules\Academic\Repositories\Lesson\LessonTeachingEloquent;
use View;
use Exception;

class LessonTeachingController extends Controller
{
    use HelperTrait;
    use PdfTrait;
    use ReferenceTrait;
    use DepartmentTrait;
    use AuditLogTrait;

    private $subject = 'Data Jadwal Guru dan Kelas';

    function __construct(LessonTeachingEloquent $lessonTeachingEloquent)
    {
        $this->lessonTeachingEloquent = $lessonTeachingEloquent;
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
        $data['status'] = Reference::select('id','name')->where('category', 'hr_teaching_status')->get();
        $data['maxtime'] = LessonScheduleTime::select('id')->count();
        return view('academic::pages.lessons.lesson_teaching', $data);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function viewTeacher(Request $request, $department_id, $id)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        //
        $data['times'] = $this->getScheduleTimes($department_id);
        $data['schedule'] = $this->getSchedules();
        return view('academic::pages.lessons.lesson_schedule_teacher', $data);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function viewClass(Request $request, $department_id)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        //
        $data['times'] = $this->getScheduleTimes($department_id);
        $data['schedule'] = $this->getSchedules();
        return view('academic::pages.lessons.lesson_schedule_class', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(LessonTeachingRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try 
        {
            for ($i=0; $i < count($request->dg); $i++) 
            {
                if ($request->dg[$i]['from_time'] > $request->dg[$i]['to_time'])
                {
                    DB::rollBack();
                    throw new Exception('Jam Mulai tidak boleh > Jam Selesai.', 1);
                }
            }
            $request->merge([
                'logged' => auth()->user()->email,
            ]);
            if ($request->id < 1) 
            {
                $query = $this->lessonTeachingEloquent->create($request, $this->subject);
                for ($i=0; $i < count($request->dg); $i++) 
                {
                    $startTime = LessonScheduleTime::select('id','start')->where('department_id', $request->department_id)->where('time', $request->dg[$i]['from_time'])->first();
                    $endTime = LessonScheduleTime::select('id','end')->where('department_id', $request->department_id)->where('time', $request->dg[$i]['to_time'])->first();
                    //
                    $schedule = new LessonSchedule;
                    $schedule->class_id = $request->class_id;
                    $schedule->employee_id = $request->employee_id;
                    $schedule->department_id = $request->department_id;
                    $schedule->schedule_id = $request->schedule_id;
                    $schedule->teaching_id = $query->id;
                    $schedule->lesson_id = $request->dg[$i]['lesson'];
                    $schedule->day = $request->dg[$i]['day_id'];
                    $schedule->from_time = $request->dg[$i]['from_time'];
                    $schedule->to_time = ( $request->dg[$i]['to_time'] - $request->dg[$i]['from_time'] ) + 1;
                    $schedule->feature = 1;
                    $schedule->start = $startTime->start;
                    $schedule->end = $endTime->end;
                    $schedule->time_id_1 = $startTime->id;
                    $schedule->time_id_2 = $endTime->id;
                    $schedule->teaching_status = $request->dg[$i]['teaching'];
                    $schedule->remark = $request->dg[$i]['remark'];
                    $schedule->save();
                }
                DB::commit();
                $response = $this->getResponse('store', '', $this->subject);
            } else {
                LessonSchedule::where('teaching_id', $request->id)->delete();
                for ($i=0; $i < count($request->dg); $i++) 
                {
                    $startTime = LessonScheduleTime::select('id','start')->where('department_id', $request->department_id)->where('time', $request->dg[$i]['from_time'])->first();
                    $endTime = LessonScheduleTime::select('id','end')->where('department_id', $request->department_id)->where('time', $request->dg[$i]['to_time'])->first();
                    // 
                    $schedule = new LessonSchedule;
                    $schedule->class_id = $request->class_id;
                    $schedule->employee_id = $request->employee_id;
                    $schedule->department_id = $request->department_id;
                    $schedule->schedule_id = $request->schedule_id;
                    $schedule->teaching_id = $request->id;
                    $schedule->lesson_id = $request->dg[$i]['lesson'];
                    $schedule->day = $request->dg[$i]['day_id'];
                    $schedule->from_time = $request->dg[$i]['from_time'];
                    $schedule->to_time = ( $request->dg[$i]['to_time'] - $request->dg[$i]['from_time'] ) + 1;
                    $schedule->feature = 1;
                    $schedule->start = $startTime->start;
                    $schedule->end = $endTime->end;
                    $schedule->time_id_1 = $startTime->id;
                    $schedule->time_id_2 = $endTime->id;
                    $schedule->teaching_status = $request->dg[$i]['teaching'];
                    $schedule->remark = $request->dg[$i]['remark'];
                    $schedule->save();
                }
                $this->lessonTeachingEloquent->update($request, $this->subject);
                DB::commit();
                $response = $this->getResponse('store', '', $this->subject);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            $response = $this->getResponse('error', $e->getMessage(), 'Kombinasi Kelas, Guru, Departemen dan Info Jadwal');
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
        $query = LessonScheduleTeaching::where('id',$id)->get()->map(function($model){
            $model['department'] = $model->getDepartment->name;
            $model['employee'] = $model->getEmployee->title_first .' '. $model->getEmployee->name .' '. $model->getEmployee->title_end;
            $model['school_year'] = $model->getClass->getSchoolYear->school_year;
            $model['schoolyear_id'] = $model->getClass->schoolyear_id;
            $model['grade'] = $model->getGradeByDept->grade;
            return $model;
        });
        $sub_query = LessonSchedule::where('teaching_id', $query[0]['id'])->orderBy('day')->get()->map(function($model){
            $model['to_time'] = ($model->to_time + $model->from_time) - 1;
            $model['day_id'] = $model->day;
            $model['lesson'] = $model->lesson_id;
            $model['lesson_id'] = $model->getLesson->name;
            $model['teaching'] = $model->teaching_status;
            $model['teaching_status'] = $model->getTeachingStatus->name;
            $model['day'] = $this->getDayName($model->day);
            return $model;
        });
        return response()->json(array(
            'main' => $query[0],
            'schedules' => $sub_query,
        ));
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
            LessonSchedule::where('teaching_id', $id)->delete();
            $this->lessonTeachingEloquent->destroy($id, $this->subject);
            $response = $this->getResponse('destroy', '', $this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), $this->subject);
        }
        return response()->json($response);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function data(Request $request)
    {
        return response()->json($this->lessonTeachingEloquent->data($request));
    }

    public function checkExist(Request $request)
    {
        $schedule_info = LessonScheduleInfo::select('id')->where('is_active', 1)->get()->implode('id', ',');
        $time = "";
        for ($i = $request->from_time; $i <= $request->to_time ; $i++) 
        { 
            if (strlen($time) != 0)
                $time .= " OR ";
                $time .= "($i >= academic.lesson_schedules.from_time AND $i <= academic.lesson_schedules.from_time + academic.lesson_schedules.to_time - 1)";
        }
        $query_teacher = DB::select("SELECT * FROM academic.fn_check_schedule_teacher('".$schedule_info."',".$request->day.'::int2'.",".$request->employee.'::int2'.",'(".$time.")')");
        if (count($query_teacher) > 0)
        {
            $list = '<ul style="margin-left:15px;">';
            foreach ($query_teacher as $row) 
            {
                $list .= '<li>' . Str::title($row->name) . ', Kelas ' . Str::upper($row->class) . ', Jam ke ' . $row->from_time . '-' . $row->to_time . '</li>';
            }
            $list .= '</ul>';
            $response = [ 'success' => false, 'message' => 'Jadwal Guru di kelas lain yang bentrok: <br/>' . $list ];
        } else {
            $query_class = DB::select("SELECT * FROM academic.fn_check_schedule_class('".$schedule_info."',".$request->day.'::int2'.",".$request->class.'::int2'.",'(".$time.")')");
            if (count($query_class) > 0)
            {
                $list = '<ul style="margin-left:15px;">';
                foreach ($query_class as $row) 
                {
                    $list .= '<li>' . Str::title($row->name) . ', Kelas ' . Str::upper($row->class) . ', Jam ke ' . $row->from_time . '-' . $row->to_time . '</li>';
                }
                $list .= '</ul>';
                $response = [ 'success' => false, 'message' => 'Jadwal bentrok di Kelas yang sama: <br/>' . $list ];
            } else {
                $response = [ 'success' => true, 'message' => '' ];
            }
        }
        return response()->json($response);
    }

    public function print(Request $request, $opt)
    {
        $payload = json_decode($request->data);
        $data['payloads'] = $payload;
        if ($opt == 'teacher')
        {
            $data['times'] = $this->getScheduleTimes($payload->department_id);
            $data['schedule'] = $this->getSchedules();
            $view = View::make('academic::pages.lessons.lesson_teacher_pdf', $data);
            $name = Str::lower(config('app.name')) .'_data_jadwal_guru';
        } else {
            $data['times'] = $this->getScheduleTimes($payload->department_id);
            $data['schedule'] = $this->getSchedules();
            $view = View::make('academic::pages.lessons.lesson_class_pdf', $data);
            $name = Str::lower(config('app.name')) .'_data_jadwal_kelas';
        }
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        //
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfLandscape($hashfile, $filename);
        echo $filename;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexRecap(Request $request)
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
        return view('academic::pages.lessons.lesson_schedule_recap', $data);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function dataRecap(Request $request)
    {
        return response()->json($this->lessonTeachingEloquent->dataRecap($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfRecap(Request $request)
    {
        //
        $vals['models'] = json_decode($request->data);
        $vals['period'] = $request->period;
        $vals['dept'] = $request->department;
        $vals['schoolyear'] = $request->school_year;
        $vals['schedule'] = $request->schedule;
        // 
        $view = View::make('academic::pages.lessons.lesson_schedule_recap_pdf', $vals);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake();
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfLandscape($hashfile, $filename);
        echo $filename;
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function comboGrid(Request $request)
    {
        return response()->json($this->lessonTeachingEloquent->comboGrid($request));
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function comboBox($seq)
    {
        return response()->json($this->lessonTeachingEloquent->comboBox($seq));
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function comboBoxDay(Request $request)
    {
        return response()->json($this->lessonTeachingEloquent->comboBoxDay($request));
    }

    // helper
    private function getScheduleTimes($department_id)
    {
        return LessonScheduleTime::select('id','time','start','end')->where('department_id', $department_id)->get();
    }

    private function getSchedules()
    {
        return LessonSchedule::select('*',DB::raw('day as day_id'))->orderBy('day')->get()->map(function($model){
                    $model['class'] = $model->getClasses->class;
                    $model['lesson'] = $model->getLesson->name;
                    $model['employee'] = $model->getEmployee->title_first .' '. $model->getEmployee->name .' '. $model->getEmployee->title_end;
                    $model['teaching_status'] = $model->getTeachingStatus->name;
                    return $model;
                });
    }

}
