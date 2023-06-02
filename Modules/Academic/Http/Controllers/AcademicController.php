<?php

namespace Modules\Academic\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\PdfTrait;
use App\Http\Traits\ReferenceTrait;
use App\Http\Traits\DepartmentTrait;
use Modules\Academic\Entities\Grade;
use Modules\Academic\Entities\SchoolYear;
use Modules\Academic\Entities\Semester;
use Modules\Academic\Entities\Classes;
use Modules\Academic\Http\Requests\SchoolYearRequest;
use Modules\Academic\Http\Requests\ClassRequest;
use Modules\Academic\Repositories\Academic\AcademicEloquent;
use Carbon\Carbon;
use View;
use Exception;

class AcademicController extends Controller
{

    use HelperTrait;
    use PdfTrait;
    use ReferenceTrait;
    use DepartmentTrait;

    private $subject_grade = 'Data Tingkat';
    private $subject_schoolyear = 'Data Tahun Ajaran';
    private $subject_semester = 'Data Semester';
    private $subject_class = 'Data Kelas';

    function __construct(AcademicEloquent $academicEloquent)
    {
        $this->academicEloquent = $academicEloquent;
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
        $search[] = array('column' => 'is_active', 'action' => 'eq', 'query' => 1);
        $data['depts'] = $this->listDepartment();
        $data['grades'] = Grade::where('is_active', 1)->get();
        $data['schoolyears'] = SchoolYear::where('is_active', 1)->get();
        return view('academic::pages.academics.index', $data);
    }

    // grade

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function storeGrade(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|int',
            'grade' => 'required',
        ]);
        try
        {
            $request->merge([
                'remark' => Str::lower($request->remark),
                'is_active' => $request->is_active ?: 1,
                'logged' => auth()->user()->email,
            ]);
            if ($request->id < 1)
            {
                if ($request->has('is_all'))
                {
                    foreach ($this->listDepartment() as $dept)
                    {
                        $request->merge([
                            'department_id' => $dept->id
                        ]);
                        $this->academicEloquent->createGrade($request, $this->subject_grade);
                    }
                } else {
                    $this->academicEloquent->createGrade($request, $this->subject_grade);
                }
            } else {
                $this->academicEloquent->updateGrade($request, $this->subject_grade);
            }
            $response = $this->getResponse('store', '', $this->subject_grade);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Departemen dan Tingkat');
        }
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showGrade($id)
    {
        return response()->json(Grade::find($id));
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroyGrade($id)
    {
        try
        {
            $this->academicEloquent->destroyGrade($id, $this->subject_grade);
            $response = $this->getResponse('destroy', '', $this->subject_grade);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Tingkat');
        }
        return response()->json($response);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function dataGrade(Request $request)
    {
        return response()->json($this->academicEloquent->dataGrade($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfGrade(Request $request)
    {
        $idArray = collect(json_decode($request->data))->pluck('id')->toArray();
        $query['models'] = Grade::whereIn('id', $idArray)->orderBy('id')->get()->map(function ($model) {
            $model['is_active'] = $this->getActive()[$model->is_active];
            return $model;
        });
        //
        $view = View::make('academic::pages.academics.grade_pdf', $query);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject_grade)->snake();
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        //
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortrait($hashfile, $filename);
        echo $filename;
    }

    /**
     * Display a listing of data for combobox.
     * @return JSON
     */
    public function comboBoxGrade($id)
    {
        return response()->json($this->academicEloquent->comboboxGrade($id));
    }

    /**
     * Display a listing of data for combogrid.
     * @return JSON
     */
    public function comboGridGrade(Request $request)
    {
        return response()->json($this->academicEloquent->combogridGrade($request));
    }

    // school year

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function storeSchoolYear(SchoolYearRequest $request)
    {
        $validated = $request->validated();
        try
        {
            $request->merge([
                'school_year' => $request->school_year .'/'. ($request->school_year + 1),
                'start_date' => $this->formatDate($request->date_start, 'sys'),
                'end_date' => $this->formatDate($request->date_start, 'sys'),
                'is_active' => $request->is_active ?: 1,
                'logged' => auth()->user()->email,
            ]);
            $year_start = explode('-', $request->start_date);
            $schoolyear = explode('/', $request->school_year);
            if ($year_start[0] != $schoolyear[0])
            {
                throw new Exception('Tahun Ajaran tidak sesuai dengan Tahun Tanggal Mulai.', 1);
            } else {
                if ($request->id < 1)
                {
                    $this->academicEloquent->createSchoolYear($request, $this->subject_schoolyear);
                } else {
                    $this->academicEloquent->updateSchoolYear($request, $this->subject_schoolyear);
                }
                $response = $this->getResponse('store', '', $this->subject_schoolyear);
            }
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Departemen dan Tahun Ajaran');
        }
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showSchoolYear($id)
    {
        $schoolyear = SchoolYear::where('id',$id)->get()->map(function($model){
            $model['date_start'] = $model->start_date->format('d/m/Y');
            $model['date_end'] = $model->end_date->format('d/m/Y');
            return $model;
        })[0];
        return response()->json($schoolyear);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroySchoolYear($id)
    {
        try
        {
            $this->academicEloquent->destroySchoolYear($id, $this->subject_schoolyear);
            $response = $this->getResponse('destroy', '', $this->subject_schoolyear);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Tahun Ajaran');
        }
        return response()->json($response);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function dataSchoolYear(Request $request)
    {
        return response()->json($this->academicEloquent->dataSchoolYear($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfSchoolYear(Request $request)
    {
        //
        $idArray = collect(json_decode($request->data))->pluck('id')->toArray();
        $query['models'] = SchoolYear::whereIn('id', $idArray)->orderBy('id')->get()->map(function ($model) {
            $model['is_active'] = $this->getActive()[$model->is_active];
            return $model;
        });
        //
        $view = View::make('academic::pages.academics.school_year_pdf', $query);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject_schoolyear)->snake();
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        //
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortrait($hashfile, $filename);
        echo $filename;
    }

    /**
     * Display a listing of data for combobox.
     * @return JSON
     */
    public function comboBoxSchoolYear($id)
    {
        return response()->json($this->academicEloquent->comboboxSchoolYear($id));
    }

    /**
     * Display a listing of data for combobox.
     * @return JSON
     */
    public function comboGridSchoolYear(Request $request)
    {
        return response()->json($this->academicEloquent->combogridSchoolYear($request));
    }

    // semester

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function storeSemester(Request $request)
    {
        //
        $validated = $request->validate([
            'department_id' => 'required|int',
            'semester' => 'required',
        ]);
        try
        {
            $request->merge([
                'semester' => Str::lower($request->semester),
                'is_active' => $request->is_active ?: 1,
                'logged' => auth()->user()->email,
            ]);
            if ($request->id < 1)
            {
                if ($request->has('is_all'))
                {
                    foreach ($this->listDepartment() as $dept)
                    {
                        $request->merge([
                            'department_id' => $dept->id
                        ]);
                        $this->academicEloquent->createSemester($request, $this->subject_semester);
                    }
                } else {
                    $query = $this->academicEloquent->createSemester($request, $this->subject_semester);
                }
                $response = $this->getResponse('store', '', $this->subject_semester);
            } else {
                $this->academicEloquent->updateSemester($request, $this->subject_semester);
                $response = $this->getResponse('store', '', $this->subject_semester);
            }
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Departemen dan Semester');
        }
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showSemester($id)
    {
        return response()->json(Semester::find($id));
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroySemester($id)
    {
        try
        {
            $this->academicEloquent->destroySemester($id, $this->subject_semester);
            $response = $this->getResponse('destroy', '', $this->subject_semester);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Semester');
        }
        return response()->json($response);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function dataSemester(Request $request)
    {
        return response()->json($this->academicEloquent->dataSemester($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfSemester(Request $request)
    {
        $idArray = collect(json_decode($request->data))->pluck('id')->toArray();
        $query['models'] = Semester::whereIn('id', $idArray)->orderBy('id')->get()->map(function ($model) {
            $model['is_active'] = $this->getActive()[$model->is_active];
            $model['grade'] = !is_null($model->grade_id) ? $model->getGrade->grade : '-';
            return $model;
        });
        //
        $view = View::make('academic::pages.academics.semester_pdf', $query);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject_semester)->snake();
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        //
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortrait($hashfile, $filename);
        echo $filename;
    }

    /**
     * Display a listing of data for combobox.
     * @return JSON
     */
    public function comboBoxSemester($id)
    {
        return response()->json($this->academicEloquent->comboboxSemester($id));
    }

    /**
     * Display a listing of data for combobox.
     * @return JSON
     */
    public function comboGridSemester(Request $request)
    {
        return response()->json($this->academicEloquent->combogridSemester($request));
    }

    // class

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function storeClass(ClassRequest $request)
    {
        //
        $validated = $request->validated();
        try
        {
            $request->merge([
                'class' => Str::lower($request->class),
                'is_active' => $request->is_active ?: 1,
                'logged' => auth()->user()->email,
            ]);
            if ($request->id < 1)
            {
                $schoolyear = SchoolYear::find($request->schoolyear_id);
                $periods = explode('/', $schoolyear->school_year);
                $period_before = intval($periods[0]) - 1 .'/'. intval($periods[1]) - 1;
                //
                if ($request->has('is_copy'))
                {
                    $classes = Classes::where('schoolyear_id','<>',$request->schoolyear_id)
                                ->whereHas('getGrade', function ($qry) use ($request) {
                                    $qry->where('department_id', $request->department_id);
                                })->get();
                    //
                    foreach ($classes as $class)
                    {
                        if (!isset($classes))
                        {
                            throw new Exception('Periode kelas sebelumnya belum ada.', 1);
                        } else {

                            $request->merge([
                                'class' => Str::lower($class->class),
                                'employee_id' => $class->employee_id,
                                'capacity' => $class->capacity,
                                'is_active' => 2,
                            ]);
                            $this->academicEloquent->createClass($request, $this->subject_class);
                            $response = $this->getResponse('store', '', $this->subject_class);
                        }
                    }
                } else {
                    $this->academicEloquent->createClass($request, $this->subject_class);
                    $response = $this->getResponse('store', '', $this->subject_class);
                }
            } else {
                $this->academicEloquent->updateClass($request, $this->subject_class);
                $response = $this->getResponse('store', '', $this->subject_class);
            }
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Tingkat, Tahun Ajaran dan Nama Kelas');
        }
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showClass($id)
    {
        return response()->json(Classes::find($id));
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroyClass($id)
    {
        try
        {
            $this->academicEloquent->destroyClass($id, $this->subject_class);
            $response = $this->getResponse('destroy', '', $this->subject_class);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Kelas');
        }
        return response()->json($response);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function dataClass(Request $request)
    {
        return response()->json($this->academicEloquent->dataClass($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfClass(Request $request)
    {
        $idArray = collect(json_decode($request->data))->pluck('id')->toArray();
        $query['models'] = $this->academicEloquent->showClassIn($idArray);
        //
        $view = View::make('academic::pages.academics.class_pdf', $query);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject_class)->snake();
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        //
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortrait($hashfile, $filename);
        echo $filename;
    }

    /**
     * Display a listing of data for combogrid.
     * @return JSON
     */
    public function comboGridClass(Request $request)
    {
        return response()->json($this->academicEloquent->combogridClass($request));
    }

    /**
     * Display a listing of data for combogrid.
     * @return JSON
     */
    public function comboGridClassView(Request $request)
    {
        return response()->json($this->academicEloquent->combogridClassView($request));
    }

    /**
     * Display a listing of data for combogrid.
     * @return JSON
     */
    public function comboGridClassPlacement(Request $request)
    {
        return response()->json($this->academicEloquent->combogridClassPlacement($request));
    }

    /**
     * Display a listing of data for combogrid.
     * @return JSON
     */
    public function comboGridClassStudent(Request $request)
    {
        return response()->json($this->academicEloquent->combogridClassStudent($request));
    }

    /**
     * Display a listing of data for combogrid.
     * @return JSON
     */
    public function comboGridClassOnly(Request $request)
    {
        return response()->json($this->academicEloquent->combogridClassOnly($request));
    }

}
