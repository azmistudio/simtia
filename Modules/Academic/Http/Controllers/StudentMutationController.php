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
use Modules\Academic\Entities\AdmissionProspect;
use Modules\Academic\Entities\Students;
use Modules\Academic\Entities\StudentMutation;
use Modules\Academic\Entities\StudentAlumni;
use Modules\Academic\Repositories\Student\StudentMutationEloquent;
use Carbon\Carbon;
use View;
use Exception;

class StudentMutationController extends Controller
{
    use HelperTrait;
    use PdfTrait;
    use ReferenceTrait;
    use DepartmentTrait;

    private $subject = 'Data Mutasi Santri';

    function __construct(StudentMutationEloquent $studentMutationEloquent)
    {
        $this->studentMutationEloquent = $studentMutationEloquent;
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
        $data['depts'] = $this->listDepartment();
        $data['years'] = StudentMutation::select(DB::raw("date_part('year', mutation_date) AS year"))->groupBy(DB::raw("date_part('year', mutation_date)"))->get();
        $data['mutations'] = DB::table('references')->select('id','name')->where('category','hr_student_mutation')->orderBy('id')->get();
        return view('academic::pages.students.student_mutation', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'students' => 'required',
            'mutation_id' => 'required|int',
            'department_id' => 'required|int',
            'mutation_date' => 'required',
        ]);
        try
        {
            $request->merge([
                'mutation_date' => $this->formatDate($request->mutation_date,'sys'),
                'remark' => Str::lower($request->remark),
                'logged' => auth()->user()->email,
            ]);
            DB::transaction(function () use ($request) {
                for ($i = 0; $i < count($request->students); $i++)
                {
                    Students::where('id', $request->students[$i]['id'])->update([
                        'is_active' => 0,
                        'mutation' => $request->mutation_id,
                        'alumni' => 1,
                    ]);
                    AdmissionProspect::where('student_id', $request->students[$i]['id'])->update([
                        'is_active' => 0,
                    ]);
                    DB::table('academic.student_class_histories')
                        ->where('student_id', $request->students[$i]['id'])
                        ->where('active', 1)
                        ->update(['active' => 0]);
                    DB::table('academic.student_dept_histories')
                        ->where('student_id', $request->students[$i]['id'])
                        ->where('active', 1)
                        ->update(['active' => 0]);
                    StudentAlumni::insert([
                        'student_id' => $request->students[$i]['id'],
                        'graduate_date' => $request->mutation_date,
                        'end_grade' => $request->grade_id,
                        'end_class' => $request->class_id,
                        'department_id' => $request->department_id,
                        'logged' => auth()->user()->email,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    $request->merge([
                        'student_id' => $request->students[$i]['id'],
                        'department_id' => $request->department_id
                    ]);
                    $this->studentMutationEloquent->create($request, $this->subject);
                    DB::table('academic.room_placements')
                        ->where('student_id', $request->students[$i]['id'])
                        ->delete();
                }
            });
            $response = $this->getResponse('store', '', $this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), '');
        }
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Request $request)
    {
        try
        {
            DB::transaction(function () use ($request) {
                for ($i = 0; $i < count($request->students); $i++)
                {
                    DB::table('academic.student_class_histories')
                        ->where('student_id', $request->students[$i]['student_id'])
                        ->where('class_id', $request->students[$i]['end_class'])
                        ->orderByDesc('start_date')
                        ->limit(1)
                        ->update(['active' => 1]);
                    DB::table('academic.student_dept_histories')
                        ->where('student_id', $request->students[$i]['student_id'])
                        ->where('department_id', $request->students[$i]['department_id'])
                        ->orderByDesc('start_date')
                        ->limit(1)
                        ->update(['active' => 1]);
                    Students::where('id', $request->students[$i]['student_id'])->update([
                        'is_active' => 1,
                        'mutation' => null,
                        'alumni' => 0,
                    ]);
                    StudentAlumni::where('student_id', $request->students[$i]['student_id'])
                        ->where('department_id', $request->students[$i]['department_id'])
                        ->where('end_class', $request->students[$i]['end_class'])
                        ->where('graduate_date', Carbon::createFromFormat('d/m/Y',$request->students[$i]['mutation_date']))
                        ->delete();
                    $this->studentMutationEloquent->destroy($request->students[$i]['id'], $this->subject);
                    AdmissionProspect::where('student_id', $request->students[$i]['id'])->update(['is_active' => 1]);
                }
            });
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
        return response()->json($this->studentMutationEloquent->data($request));
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function combogrid(Request $request)
    {
        return response()->json($this->studentMutationEloquent->comboGrid($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdf(Request $request)
    {
        $idArray = collect(json_decode($request->data))->pluck('id')->toArray();
        $query['models'] = $this->studentMutationEloquent->showIn($idArray);
        $query['profile'] = $this->getInstituteProfile();
        $query['requests'] = $request->all();
        //
        $view = View::make('academic::pages.students.student_mutation_pdf', $query);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake();
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfLandscape($hashfile, $filename);
        echo $filename;
    }
}
