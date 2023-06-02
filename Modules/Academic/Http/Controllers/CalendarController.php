<?php

namespace Modules\Academic\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\ReferenceTrait;
use App\Http\Traits\DepartmentTrait;
use Modules\Academic\Entities\SchoolYear;
use Modules\Academic\Entities\Calendar;
use Modules\Academic\Entities\CalendarActivity;
use Modules\Academic\Http\Requests\CalendarActivityRequest;
use Modules\Academic\Repositories\Academic\CalendarEloquent;
use Modules\Academic\Repositories\Academic\CalendarActivityEloquent;
use Carbon\Carbon;
use View;
use Exception;

class CalendarController extends Controller
{
    use HelperTrait;
    use ReferenceTrait;
    use DepartmentTrait;

    private $subject = 'Data Kalender Akademik';
    private $subject_activity = 'Data Aktivitas Kalender Akademik';

    function __construct(CalendarEloquent $calendarEloquent, CalendarActivityEloquent $calendarActivityEloquent)
    {
        $this->calendarEloquent = $calendarEloquent;
        $this->calendarActivityEloquent = $calendarActivityEloquent;
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
        $schoolyears = SchoolYear::where('is_active', 1);
        $calendars = Calendar::where('is_active', 1);
        if (auth()->user()->getDepartment->is_all != 1)
        {
            $schoolyears = $schoolyears->where('department_id', auth()->user()->department_id);
            $calendars = $calendars->whereHas('getSchoolYear', function($qry) {
                $qry = $qry->where('department_id', auth()->user()->department_id);
            });
        }
        $data['calendars'] = $calendars->get()->map(function($model){
            $model['period'] = $model->getSchoolYear->start_date->format('d/m/Y') .' s.d '. $model->getSchoolYear->end_date->format('d/m/Y');
            $model['department'] = $model->getSchoolYear->getDepartment->name;
            $model['school_year'] = $model->getSchoolYear->school_year;
            return $model;
        });
        $data['schoolyears'] = $schoolyears->get();
        $data['depts'] = $this->listDepartment();
        return view('academic::pages.calendars.calendar', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'schoolyear_id' => 'required|string',
            'description' => 'required|string',
        ]);
        try
        {
            $schoolyears = explode('-', $request->schoolyear_id);
            $request->merge([
                'schoolyear_id' => $schoolyears[0],
                'is_active' => $request->is_active ?: 1,
                'logged' => auth()->user()->email,
            ]);
            if ($request->id < 1)
            {
                $this->calendarEloquent->create($request, $this->subject);
            } else {
                $this->calendarEloquent->update($request, $this->subject);
            }
            $response = $this->getResponse('store', '', $this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Tahun Ajaran');
        }
        return response()->json($response);
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
            $this->calendarEloquent->destroy($id, $this->subject);
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
        return response()->json($this->calendarEloquent->data($request));
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function list()
    {
        return response()->json($this->calendarEloquent->list());
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function dataActivity(Request $request)
    {
        return response()->json($this->calendarActivityEloquent->data($request));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function storeActivity(CalendarActivityRequest $request)
    {
        $validated = $request->validated();
        $calendars = explode('-', $request->calendar_id);
        try
        {
            $check_period = Calendar::find($calendars[0]);
            if (
                strtotime($this->formatDate($request->start,'sys')) < strtotime($check_period->getSchoolYear->start_date) ||
                strtotime($this->formatDate($request->end,'sys')) > strtotime($check_period->getSchoolYear->end_date)
            )
            {
                throw new Exception('Periode Tanggal Mulai & Tanggal Akhir harus sesuai Periode Kalender Akademik.', 1);
            } else {
                $request->merge([
                    'calendar_id' => $calendars[0],
                    'description' => !empty($request->description) ? $request->description : '-',
                    'start' => $this->formatDate($request->start,'sys'),
                    'end' => $this->formatDate($request->end,'sys'),
                    'logged' => auth()->user()->email,
                ]);
                if ($request->id < 1)
                {
                    $this->calendarActivityEloquent->create($request, $this->subject_activity);
                } else {
                    $this->calendarActivityEloquent->update($request, $this->subject_activity);
                }
                $response = $this->getResponse('store', '', $this->subject_activity);
            }
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), $this->subject_activity);
        }
        return response()->json($response);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function showActivity($id)
    {
        return response()->json(CalendarActivity::where('id', $id)->get()->map(function($model) {
            $model['department'] = $model->getCalendar->getSchoolYear->getDepartment->name;
            $model['school_year'] = $model->getCalendar->getSchoolYear->school_year;
            $model['period'] = $model->getCalendar->getSchoolYear->start_date->format('d/m/Y') .' s.d '. $model->getCalendar->getSchoolYear->end_date->format('d/m/Y');
            return $model;
        })[0]);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroyActivity($id)
    {
        try
        {
            $this->calendarActivityEloquent->destroy($id, $this->subject);
            $response = $this->getResponse('destroy', '', $this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), $this->subject);
        }
        return response()->json($response);
    }

    /**
     * Display a view resource.
     * @return Renderable
     */
    public function indexYearly($id)
    {
        $data['calendar'] = Calendar::where('id', $id)->get()->map(function($model) {
            $model['department'] = $model->getSchoolYear->getDepartment->name;
            $model['start_date'] = $model->getSchoolYear->start_date->format('Y-m-d');
            $model['end_date'] = $model->getSchoolYear->end_date->format('Y-m-d');
            $model['period'] = $model->getSchoolYear->start_date->format('d/m/Y') .' s.d '. $model->getSchoolYear->end_date->format('d/m/Y');
            return $model->only(['id','description','department','start_date','end_date','period']);
        })[0];
        $data['activities'] = CalendarActivity::where('calendar_id', $id)->get();
        return view('academic::pages.calendars.calendar_view', $data);
    }
}
