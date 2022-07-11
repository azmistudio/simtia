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
use Modules\Academic\Repositories\Admission\AdmissionEloquent;
use View;
use Exception;

class AdmissionController extends Controller
{

    use HelperTrait;
    use PdfTrait;
    use ReferenceTrait;
    use DepartmentTrait;

    private $subject = 'Data Proses Penerimaan';

    function __construct(AdmissionEloquent $admissionEloquent)
    {
        $this->admissionEloquent = $admissionEloquent;
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
        return view('academic::pages.admissions.admission', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @param  int  $id
     * @return Renderable
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|int',
            'name' => 'required|string',
            'prefix' => 'required|string',
        ]);
        try 
        {
            $request->merge([
                'name' => Str::lower($request->name),
                'prefix' => Str::lower($request->prefix),
                'is_active' => $request->is_active ?: 1,
                'logged' => auth()->user()->email,
            ]);
            if ($request->id < 1) 
            {
                $this->admissionEloquent->create($request, $this->subject);
            } else {
                $this->admissionEloquent->update($request, $this->subject);
            }
            $response = $this->getResponse('store', '', $this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Departemen dan Awalan (Prefix)');
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
        return response()->json(Admission::find($id));
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
            $this->admissionEloquent->destroy($id, $this->subject);
            $response = $this->getResponse('destroy', '', $this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Proses Penerimaan');
        }
        return response()->json($response);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function data(Request $request)
    {
        return response()->json($this->admissionEloquent->data($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdf(Request $request)
    {
        $idArray = collect(json_decode($request->data))->pluck('id')->toArray();
        $query['models'] = Admission::whereIn('id', $idArray)
                            ->groupBy('id','department_id','name','prefix','remark','is_active')
                            ->orderBy('id')
                            ->get()->map(function ($model) {
                                $model['is_active'] = $this->getActive()[$model->is_active];
                                return $model;
                            });
        //
        $view = View::make('academic::pages.admissions.admission_pdf', $query);
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
        return response()->json($this->admissionEloquent->combogrid($request));
    }
}
