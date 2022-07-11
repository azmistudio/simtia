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
use Modules\Academic\Entities\Admission;
use Modules\Academic\Entities\AdmissionProspectGroup;
use Modules\Academic\Repositories\Admission\ProspectGroupEloquent;
use View;
use Exception;

class AdmissionProspectGroupController extends Controller
{
    
    use HelperTrait;
    use PdfTrait;
    use ReferenceTrait;
    use DepartmentTrait;

    private $subject = 'Data Kelompok Proses Penerimaan';

    function __construct(prospectGroupEloquent $prospectGroupEloquent)
    {
        $this->prospectGroupEloquent = $prospectGroupEloquent;
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
        return view('academic::pages.admissions.admission_prospect_group', $data);
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
            'group' => 'required|string',
            'capacity' => 'required|int',
        ]);
        try 
        {
            $request->merge([
                'group' => Str::lower($request->group),
                'logged' => auth()->user()->email,
            ]);
            if ($request->id < 1) 
            {
                $admission = Admission::find($request->admission_id);
                if ($admission->is_active != 1)
                {
                    throw new Exception('Proses dalam kondisi tidak aktif, silahkan pilih proses lainnya.', 1);
                } else {
                    $this->prospectGroupEloquent->create($request, $this->subject);
                    $response = $this->getResponse('store', '', $this->subject);
                }
            } else {
                $this->prospectGroupEloquent->update($request, $this->subject);
                $response = $this->getResponse('store', '', $this->subject);
            }
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Departemen, Proses Penerimaan dan Kelompok');
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
        $query = AdmissionProspectGroup::where('id', $id)->get()->map(function($model){
                        $model['department'] = $model->getAdmission->getDepartment->name;
                        $model['occupied'] = count($model->getAdmissionProspect);
                        return $model;
                    })[0];
        return response()->json($query);
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
            $this->prospectGroupEloquent->destroy($id, $this->subject);
            $response = $this->getResponse('destroy', '', $this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Kelompok Proses Penerimaan');
        }
        return response()->json($response);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function data(Request $request)
    {
        return response()->json($this->prospectGroupEloquent->data($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdf(Request $request)
    {
        $idArray = collect(json_decode($request->data))->pluck('id')->toArray();
        $query['models'] = $this->prospectGroupEloquent->showIn($idArray);
        //
        $view = View::make('academic::pages.admissions.prospective_group_pdf', $query);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake();
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfLandscape($hashfile, $filename);
        echo $filename;
    }

    /**
     * Display a listing of data for combogrid.
     * @return JSON
     */
    public function comboGrid(Request $request)
    {
        return response()->json($this->prospectGroupEloquent->combogrid($request));
    }
}
