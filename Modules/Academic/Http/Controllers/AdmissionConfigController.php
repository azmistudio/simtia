<?php

namespace Modules\Academic\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use App\Http\Traits\HelperTrait;
use Modules\Academic\Entities\Admission;
use Modules\Academic\Entities\AdmissionConfig;
use Modules\Academic\Repositories\Admission\ConfigEloquent;
use View;
use Exception;

class AdmissionConfigController extends Controller
{
    use HelperTrait;

    private $subject = 'Data Konfigurasi Penerimaan';

    function __construct(ConfigEloquent $configEloquent)
    {
        $this->configEloquent = $configEloquent;
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
        return view('academic::pages.admissions.admission_config', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'admission_id' => 'required|int',
        ]);
        try
        {
            if (count($request->configs) > 0)
            {
                $request->merge([
                    'donate_code_1' => $request->configs[0]['code'],
                    'donate_name_1' => $request->configs[0]['name'],
                    'donate_code_2' => $request->configs[1]['code'],
                    'donate_name_2' => $request->configs[1]['name'],
                    'exam_code_01' => $request->configs[2]['code'],
                    'exam_name_01' => $request->configs[2]['name'],
                    'exam_code_02' => $request->configs[3]['code'],
                    'exam_name_02' => $request->configs[3]['name'],
                    'exam_code_03' => $request->configs[4]['code'],
                    'exam_name_03' => $request->configs[4]['name'],
                    'exam_code_04' => $request->configs[5]['code'],
                    'exam_name_04' => $request->configs[5]['name'],
                    'exam_code_05' => $request->configs[6]['code'],
                    'exam_name_05' => $request->configs[6]['name'],
                    'exam_code_06' => $request->configs[7]['code'],
                    'exam_name_06' => $request->configs[7]['name'],
                    'exam_code_07' => $request->configs[8]['code'],
                    'exam_name_07' => $request->configs[8]['name'],
                    'exam_code_08' => $request->configs[9]['code'],
                    'exam_name_08' => $request->configs[9]['name'],
                    'exam_code_09' => $request->configs[10]['code'],
                    'exam_name_09' => $request->configs[10]['name'],
                    'exam_code_10' => $request->configs[11]['code'],
                    'exam_name_10' => $request->configs[11]['name'],
                    'logged' => auth()->user()->email,
                ]);
            }
            if ($request->id < 1)
            {
                $admission = Admission::find($request->admission_id);
                if ($admission->is_active != 1)
                {
                    throw new Exception('Proses Penerimaan dalam kondisi tidak aktif, silahkan pilih proses lainnya.', 1);
                } else {
                    if ($request->has('is_clone'))
                    {
                        $admission_before = AdmissionConfig::where('id','<>',$request->admission_id)->first();
                        if (is_null($admission_before))
                        {
                            throw new Exception('Belum ada data konfigurasi yang dibuat', 1);
                        } else {
                            $request->merge([
                                'donate_code_1' => $admission_before->donate_code_1,
                                'donate_name_1' => $admission_before->donate_name_1,
                                'donate_code_2' => $admission_before->donate_code_2,
                                'donate_name_2' => $admission_before->donate_name_2,
                                'exam_code_01' => $admission_before->exam_code_01,
                                'exam_name_01' => $admission_before->exam_name_01,
                                'exam_code_02' => $admission_before->exam_code_02,
                                'exam_name_02' => $admission_before->exam_name_02,
                                'exam_code_03' => $admission_before->exam_code_03,
                                'exam_name_03' => $admission_before->exam_name_03,
                                'exam_code_04' => $admission_before->exam_code_04,
                                'exam_name_04' => $admission_before->exam_name_04,
                                'exam_code_05' => $admission_before->exam_code_05,
                                'exam_name_05' => $admission_before->exam_name_05,
                                'exam_code_06' => $admission_before->exam_code_06,
                                'exam_name_06' => $admission_before->exam_name_06,
                                'exam_code_07' => $admission_before->exam_code_07,
                                'exam_name_07' => $admission_before->exam_name_07,
                                'exam_code_08' => $admission_before->exam_code_08,
                                'exam_name_08' => $admission_before->exam_name_08,
                                'exam_code_09' => $admission_before->exam_code_09,
                                'exam_name_09' => $admission_before->exam_name_09,
                                'exam_code_10' => $admission_before->exam_code_10,
                                'exam_name_10' => $admission_before->exam_name_10,
                            ]);
                        }
                    }
                    $this->configEloquent->create($request, $this->subject);
                    $response = $this->getResponse('store', '', $this->subject);
                }
            } else {
                $this->configEloquent->update($request, $this->subject);
                $response = $this->getResponse('store', '', $this->subject);
            }
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Proses Penerimaan');
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
        return response()->json(AdmissionConfig::find($id));
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function getByAdmission($id)
    {
        return response()->json(AdmissionConfig::where('admission_id', $id)->first());
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
            $this->configEloquent->destroy($id, $this->subject);
            $response = $this->getResponse('destroy', '', $this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Konfigurasi Penerimaan');
        }
        return response()->json($response);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function data(Request $request)
    {
        return response()->json($this->configEloquent->data($request));
    }
}
