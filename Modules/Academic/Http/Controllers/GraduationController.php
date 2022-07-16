<?php

namespace Modules\Academic\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\PdfTrait;
use App\Http\Traits\ReferenceTrait;
use Modules\Academic\Entities\Classes;
use Modules\Academic\Entities\Students;
use Modules\Academic\Entities\StudentAlumni;
use Modules\Academic\Entities\AdmissionProspect;
use Modules\Academic\Http\Requests\StudentMutationRequest;
use Modules\Academic\Http\Requests\StudentAlumniRequest;
use Modules\Academic\Repositories\Student\StudentAlumniEloquent;
use Carbon\Carbon;
use View;
use Exception;

class GraduationController extends Controller
{
    use HelperTrait;
    use ReferenceTrait;

    private $subject = 'Data Kenaikan Kelas';
    private $subject_unpromote = 'Data Tidak Naik Kelas';
    private $subject_graduate = 'Data Kelulusan Pindah Departemen';
    private $subject_alumni = 'Data Kelulusan Alumni';

    function __construct(StudentAlumniEloquent $studentAlumniEloquent)
    {
        $this->studentAlumniEloquent = $studentAlumniEloquent;
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
        return view('academic::pages.graduations.graduation_promote', $data);
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
            'class_id' => 'required|int',
            'class_id_dst' => 'required|int',
        ]);
        try {
            // check capacity
            $class_cap = Classes::where('id', $request->class_id_dst)->first()->capacity;
            $class_quota = Students::where('class_id', $request->class_id_dst)->where('is_active', 1)->count();
            $remaining = $class_cap - $class_quota;
            $students = count($request->students);
            if ($class_cap <= $class_quota)
            {
                throw new Exception('Kapasitas Kelas Tujuan sudah penuh, silahkan pilih kelas lain.', 1);
            } elseif ($students > $remaining) {
                throw new Exception('Jumlah Santri melebihi Kuota Kelas Tujuan.', 1);
            } else {
                DB::transaction(function () use ($request) {
                    for ($i = 0; $i < count($request->students); $i++)
                    {
                        Students::where('id', $request->students[$i]['id'])->update(['class_id' => $request->class_id_dst]);
                        DB::table('academic.student_class_histories')
                            ->where('student_id', $request->students[$i]['id'])
                            ->where('class_id', $request->class_id)
                            ->update(['active' => 0]);
                        DB::table('academic.student_class_histories')->insert([
                            'student_id' => $request->students[$i]['id'],
                            'class_id' => $request->class_id_dst,
                            'start_date' => date('Y-m-d'),
                            'active' => 1,
                            'status' => 1,
                            'remark' => $request->students[$i]['remark'],
                            'logged' => auth()->user()->email,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                });
                $response = $this->getResponse('store', '', $this->subject);
            }
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), $this->subject);
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
            for ($i = 0; $i < count($request->students); $i++)
            {
                $history = DB::table('academic.student_class_histories')->where('student_id', $request->students[$i]['id'])->count();
                if ($history == 1)
                {
                    throw new Exception('Kenaikan Kelas Santri tidak dapat dihapus.', 1);
                }
            }
            DB::transaction(function () use ($request) {
                for ($i = 0; $i < count($request->students); $i++)
                {
                    $histories = DB::table('academic.student_class_histories')->where('student_id', $request->students[$i]['id'])->orderByDesc('start_date')->limit(2)->get();
                    DB::table('academic.student_class_histories')->where('student_id', $request->students[$i]['id'])->where('class_id', $request->class_id)->delete();
                    DB::table('academic.student_class_histories')->where('id', $histories[1]->id)->update(['active' => 1]);
                    Students::where('id', $request->students[$i]['id'])->update(['class_id' => $histories[1]->class_id]);
                }
            });
            $response = $this->getResponse('destroy', '', $this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), $this->subject);
        }
        return response()->json($response);
    }

    // Unpromote

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexUnpromote(Request $request)
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
        return view('academic::pages.graduations.graduation_unpromote', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function storeUnpromote(Request $request)
    {
        $validated = $request->validate([
            'students' => 'required',
            'class_id' => 'required|int',
            'class_id_dst' => 'required|int',
        ]);
        try {
            // check capacity
            $class_cap = Classes::where('id', $request->class_id_dst)->first()->capacity;
            $class_quota = Students::where('class_id', $request->class_id_dst)->where('is_active', 1)->count();
            $remaining = $class_cap - $class_quota;
            $students = count($request->students);
            // 
            if ($class_cap <= $class_quota)
            {
                throw new Exception('Kapasitas Kelas Tujuan sudah penuh, silahkan pilih kelas lain.', 1);
            } elseif ($students > $remaining) {
                throw new Exception('Jumlah Santri melebihi Kuota Kelas Tujuan.', 1);
            } else {
                DB::transaction(function () use ($request) {
                    for ($i = 0; $i < count($request->students); $i++)
                    {
                        Students::where('id', $request->students[$i]['id'])->update(['class_id' => $request->class_id_dst]);
                        DB::table('academic.student_class_histories')
                            ->where('student_id', $request->students[$i]['id'])
                            ->where('class_id', $request->class_id)
                            ->update(['active' => 0]);
                        DB::table('academic.student_class_histories')->insert([
                            'student_id' => $request->students[$i]['id'],
                            'class_id' => $request->class_id_dst,
                            'start_date' => date('Y-m-d'),
                            'active' => 1,
                            'status' => 1,
                            'remark' => $request->students[$i]['remark'],
                            'logged' => auth()->user()->email,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                });
                $response = $this->getResponse('store', '', $this->subject_unpromote);
            }
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), $this->subject_unpromote);
        }
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @return Renderable
     */
    public function destroyUnpromote(Request $request)
    {
        try
        {
            for ($i = 0; $i < count($request->students); $i++)
            {
                $history = DB::table('academic.student_class_histories')->where('student_id', $request->students[$i]['id'])->count();
                if ($history == 1)
                {
                    throw new Exception('Data Santri Tidak Naik Kelas tidak dapat dihapus.', 1);
                }
            }
            DB::transaction(function () use ($request) {
                for ($i = 0; $i < count($request->students); $i++)
                {
                    $histories = DB::table('academic.student_class_histories')->where('student_id', $request->students[$i]['id'])->orderByDesc('start_date')->limit(2)->get();
                    DB::table('academic.student_class_histories')->where('student_id', $request->students[$i]['id'])->where('class_id', $request->class_id)->delete();
                    DB::table('academic.student_class_histories')->where('id', $histories[1]->id)->update(['active' => 1]);
                    Students::where('id', $request->students[$i]['id'])->update(['class_id' => $histories[1]->class_id]);
                }
            });
            $response = $this->getResponse('destroy', '', $this->subject_unpromote);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), $this->subject_unpromote);
        }
        return response()->json($response);
    }

    // Mutation

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexMutation(Request $request)
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
        return view('academic::pages.graduations.graduation_mutation', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function storeMutation(StudentMutationRequest $request)
    {
        $validated = $request->validated();
        try
        {
            // check capacity
            $class_cap = Classes::where('id', $request->class_id_dst)->first()->capacity;
            $class_quota = Students::where('class_id', $request->class_id_dst)->where('is_active', 1)->count();
            $remaining = $class_cap - $class_quota;
            $students = count($request->students);

            if ($class_cap <= $class_quota)
            {
                throw new Exception('Kapasitas Kelas Tujuan sudah penuh, silahkan pilih kelas lain.', 1);
            } elseif ($students > $remaining) {
                throw new Exception('Jumlah Santri melebihi Kuota Kelas Tujuan.', 1);
            } else {
                DB::transaction(function () use ($request) {
                    for ($i = 0; $i < count($request->students); $i++)
                    {
                        $lastNIS = Students::select('student_no')->orderByDesc('id')->limit(1)->first();
                        $newNIS = $lastNIS != null ? substr($request->generation_dst, 2) . '00000' . sprintf('%03d',intval(substr($lastNIS->student_no, -3)) + 1) : substr($request->generation_dst, 2) . '00000' . sprintf('%03d', 1);
                        $studentNo = $request->students[$i]['student_num'] != '' ? $request->students[$i]['student_num'] : $newNIS;
                        $remark = isset($request->students[$i]['remark']) ? $request->students[$i]['remark'] : '-';
                        //
                        $q_student = DB::select("SELECT academic.sp_student_graduations(
                            ".$request->students[$i]['id'].'::int4'.",
                            ".$request->class_id_dst.'::int4'.",
                            '".$studentNo."',
                            '".$remark."',
                            1".'::int2'.",
                            '".auth()->user()->email."'
                        )");
                        Students::where('id', $request->students[$i]['id'])->update(['is_active' => 0, 'alumni' => 1]);
                        AdmissionProspect::where('student_id', $request->students[$i]['id'])->update(['is_active' => 0]);
                        DB::table('academic.student_class_histories')
                            ->where('student_id', $request->students[$i]['id'])
                            ->where('class_id', $request->class_id)
                            ->where('active', 1)
                            ->update(['active' => 0]);
                        DB::table('academic.student_class_histories')->insert([
                            'student_id' => $q_student[0]->sp_student_graduations,
                            'class_id' => $request->class_id_dst,
                            'start_date' => date('Y-m-d'),
                            'active' => 1,
                            'status' => 1,
                            'remark' => $request->students[$i]['remark'],
                            'logged' => auth()->user()->email,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                        DB::table('academic.student_dept_histories')
                            ->where('student_id', $request->students[$i]['id'])
                            ->where('department_id', $request->department_id)
                            ->where('active', 1)
                            ->update(['active' => 0]);
                        $request->merge([
                            'student_id' => $q_student[0]->sp_student_graduations,
                            'end_class' => $request->class_id,
                            'end_grade' => $request->grade_id,
                            'graduate_date' => Carbon::createFromFormat('d/m/Y',$request->date),
                            'remark' => $request->students[$i]['remark'],
                            'logged' => auth()->user()->email,
                        ]);
                        $this->studentAlumniEloquent->create($request, $this->subject_alumni);
                        DB::table('academic.student_dept_histories')->insert([
                            'student_id' => $q_student[0]->sp_student_graduations,
                            'old_student_id' => $request->students[$i]['id'],
                            'department_id' => $request->department_id_dst,
                            'start_date' => date('Y-m-d'),
                            'active' => 1,
                            'status' => 1,
                            'remark' => $request->students[$i]['remark'],
                            'logged' => auth()->user()->email,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                        DB::table('academic.room_placements')
                            ->where('student_id', $request->students[$i]['id'])
                            ->delete();
                    }
                });
                $response = $this->getResponse('store', '', $this->subject_graduate);
            }
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), $this->subject_graduate);
        }
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @return Renderable
     */
    public function destroyMutation(Request $request)
    {
        try
        {
            DB::transaction(function () use ($request) {
                for ($i = 0; $i < count($request->students); $i++)
                {
                    StudentAlumni::where('student_id', $request->students[$i]['id'])->delete();
                    $old_id = DB::table('academic.student_dept_histories')->select('old_student_id')->where('student_id', $request->students[$i]['id'])->first();
                    Students::where('id', $old_id->old_student_id)->update(['is_active' => 1, 'alumni' => 0]);
                    Students::where('id', $request->students[$i]['id'])->delete();
                    AdmissionProspect::where('student_id', $request->students[$i]['id'])->update(['is_active' => 1]);
                }
            });
            $response = $this->getResponse('destroy', '', $this->subject_graduate);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), $this->subject_graduate);
        }
        return response()->json($response);
    }


    // Alumni

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexAlumni(Request $request)
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
        return view('academic::pages.graduations.graduation_alumni', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function storeAlumni(StudentAlumniRequest $request)
    {
        $validated = $request->validated();
        try
        {
            DB::transaction(function () use ($request) {
                for ($i = 0; $i < count($request->students); $i++)
                {
                    Students::where('id', $request->students[$i]['id'])->update(['is_active' => 0, 'alumni' => 1]);
                    AdmissionProspect::where('student_id', $request->students[$i]['id'])->update(['is_active' => 0]);
                    DB::table('academic.student_class_histories')->where('student_id', $request->students[$i]['id'])->where('active', 1)->update(['active' => 0]);
                    DB::table('academic.student_dept_histories')->where('student_id', $request->students[$i]['id'])->where('active', 1)->update(['active' => 0]);
                    DB::table('academic.room_placements')->where('student_id', $request->students[$i]['id'])->delete();
                    $request->merge([
                        'student_id' => $request->students[$i]['id'],
                        'end_class' => $request->class_id,
                        'end_grade' => $request->grade_id,
                        'graduate_date' => $this->formatDate($request->date,'sys'),
                        'remark' => $request->students[$i]['remark'],
                        'logged' => auth()->user()->email,
                    ]);
                    $this->studentAlumniEloquent->create($request, $this->subject_alumni);
                }
            });
            $response = $this->getResponse('store', '', $this->subject_alumni);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), $this->subject_alumni);
        }
        return response()->json($response);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function dataAlumni(Request $request)
    {
        return response()->json($this->studentAlumniEloquent->data($request));
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function combogridAlumni(Request $request)
    {
        return response()->json($this->studentAlumniEloquent->comboGrid($request));
    }

    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @return Renderable
     */
    public function destroyAlumni(Request $request)
    {
        try
        {
            DB::transaction(function () use ($request) {
                for ($i = 0; $i < count($request->students); $i++)
                {
                    Students::where('id', $request->students[$i]['student_id'])->update(['is_active' => 1, 'alumni' => 0]);
                    AdmissionProspect::where('student_id', $request->students[$i]['id'])->update(['is_active' => 0]);
                    $alumni = StudentAlumni::where('student_id', $request->students[$i]['student_id'])->first();
                    $this->studentAlumniEloquent->destroy($alumni->id, $this->subject_alumni);
                }
            });
            $response = $this->getResponse('destroy', '', $this->subject_alumni);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), $this->subject_alumni);
        }
        return response()->json($response);
    }
    
}
