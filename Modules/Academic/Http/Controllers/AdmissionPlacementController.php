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
use App\Http\Traits\ReferenceTrait;
use App\Http\Traits\AuditLogTrait;
use Modules\Academic\Entities\Grade;
use Modules\Academic\Entities\SchoolYear;
use Modules\Academic\Entities\Classes;
use Modules\Academic\Entities\Admission;
use Modules\Academic\Entities\AdmissionProspectGroup;
use Modules\Academic\Entities\AdmissionProspectGroupView;
use Modules\Academic\Entities\AdmissionProspect;
use Modules\Academic\Entities\Students;
use Modules\Academic\Repositories\Academic\AcademicEloquent;
use Modules\Academic\Repositories\Admission\PlacementEloquent;
use Carbon\Carbon;
use View;
use Exception;

class AdmissionPlacementController extends Controller
{

    use AuditLogTrait;
    use HelperTrait;
    use ReferenceTrait;

    private $subject = 'Penempatan Calon Santri';

    function __construct(PlacementEloquent $placementEloquent, AcademicEloquent $academicEloquent)
    {
        $this->placementEloquent = $placementEloquent;
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
        return view('academic::pages.admissions.admission_placement', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'prospect_group_id' => 'required|int',
            'class_id' => 'required|int',
        ]);
        if (empty($request->students))
        {
            $response = $this->getResponse('warning', 'Silahkan pilih Data Calon Santri terlebih dahulu.');
        } else {
            try 
            {
                if ($request->id < 1) 
                {
                    // check quota class
                    $class_quota = $this->academicEloquent->quotaClass($request->class_id);
                    if (!is_null($class_quota) && ($class_quota->capacity == $class_quota->occupied))
                    {
                        throw new Exception('Kuota Kelas sudah terpenuhi, silahkan pilih Kelas lainnya.', 1);
                    } else {
                        $c_quota = Classes::where('id', $request->class_id)->select('capacity',DB::raw('grade_id as grade'))->first();
                        $capacity = !is_null($class_quota) ? $class_quota->capacity : $c_quota->capacity;
                        $occupied = !is_null($class_quota) ? $class_quota->occupied : 0;
                        $remaining = $capacity - $occupied;
                        $department_id = Grade::where('id', $c_quota->grade)->pluck('department_id')->first();
                        //
                        if (count($request->students) > $remaining)
                        {
                            throw new Exception('Jumlah Calon Santri ('.count($request->students).') melebihi batas Kuota Kelas tersisa (' .$remaining.')', 1);
                        } else {
                            for ($i=0; $i < count($request->students); $i++) 
                            {
                                $lastNIS = Students::select('student_no')->orderByDesc('id')->first();
                                $newNIS = $lastNIS != null ? substr(date('Y'), 2) . '00000' . sprintf('%03d',intval(substr($lastNIS->student_no, -3)) + 1) : substr(date('Y'), 2) . '00000' . sprintf('%03d', 1);
                                $studentNo = $request->students[$i]['student_no'] != '-' ? $request->students[$i]['student_no'] : $newNIS;
                                $remark = isset($request->students[$i]['remark']) ? $request->students[$i]['remark'] : '-';
                                DB::select("SELECT academic.sp_students(
                                    ".$request->prospect_group_id.'::int4'.",
                                    ".$request->class_id.'::int4'.",
                                    '".$studentNo."',
                                    ".$request->students[$i]['id'].'::int4'.",
                                    '".$remark."',
                                    1".'::int2'.",
                                    '".auth()->user()->email."'
                                )");
                                $student = Students::select('id','class_id','remark','prospect_student_id')->where('prospect_student_id', $request->students[$i]['id'])->first();
                                AdmissionProspect::where('id', $student->prospect_student_id)->update(['student_id' => $student->id]);
                                DB::table('academic.student_class_histories')->insert([
                                    'student_id' => $student->id,
                                    'class_id' => $request->class_id,
                                    'start_date' => date('Y-m-d'),
                                    'active' => 1,
                                    'status' => 1,
                                    'remark' => $student->remark,
                                    'logged' => auth()->user()->email,
                                    'created_at' => date('Y-m-d H:i:s')
                                ]);
                                DB::table('academic.student_dept_histories')->insert([
                                    'student_id' => $student->id,
                                    'department_id' => $department_id,
                                    'start_date' => date('Y-m-d'),
                                    'active' => 1,
                                    'status' => 0,
                                    'remark' => $student->remark,
                                    'logged' => auth()->user()->email,
                                    'created_at' => date('Y-m-d H:i:s')
                                ]);
                                $before = array(
                                    'student_id' => $student->id,
                                    'class_id' => $request->class_id,
                                    'department_id' => $department_id,
                                    'start_date' => date('Y-m-d'),
                                );
                                $this->logTransaction('#', 'Tambah '. $this->subject, json_encode($before), '{}');
                            }
                            $response = $this->getResponse('store', '', $this->subject);
                        }
                    }
                }
            } catch (\Throwable $e) {
                $response = $this->getResponse('error', $e->getMessage(), $this->subject);
            }
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
                for ($i=0; $i < count($request->data); $i++) 
                { 
                    $this->placementEloquent->destroy($request->data[$i]['id'], $this->subject);
                    AdmissionProspect::where('id', $request->data[$i]['prospect_student_id'])->update(['student_id' => 0]);
                }
            });
            $response = $this->getResponse('destroy','',$this->subject);
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
        return response()->json($this->placementEloquent->data($request));
    }
}
