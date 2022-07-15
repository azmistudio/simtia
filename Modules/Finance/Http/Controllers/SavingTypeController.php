<?php

namespace Modules\Finance\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Traits\DepartmentTrait;
use App\Http\Traits\ReferenceTrait;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\PdfTrait;
use Modules\Finance\Entities\SavingType;
use Modules\Finance\Repositories\Saving\SavingTypeEloquent;
use Modules\Finance\Repositories\Reference\CodeEloquent;
use Modules\Finance\Http\Requests\SavingTypeRequest;
use View;
use Exception;

class SavingTypeController extends Controller
{
    use DepartmentTrait;
    use HelperTrait;
    use PdfTrait;

    private $subject_student = 'Data Jenis Tabungan Santri';
    private $subject_employee = 'Data Jenis Tabungan Pegawai';

    function __construct(SavingTypeEloquent $savingTypeEloquent, CodeEloquent $codeEloquent)
    {
        $this->savingTypeEloquent = $savingTypeEloquent;
        $this->codeEloquent = $codeEloquent;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexStudent(Request $request)
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
        $data['codes_cash'] = $this->codeEloquent->combobox(1,'1-1');
        $data['codes_credit'] = $this->codeEloquent->combobox(2);
        return view('finance::pages.savings.student_saving_type', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function storeStudent(SavingTypeRequest $request)
    {
        $validated = $request->validated();
        try 
        {
            $request->merge([
                'is_employee' => 0,
                'is_active' => $request->is_active ?: 1,
                'logged' => auth()->user()->email,
            ]);
            if ($request->id < 1) 
            {
                if ($request->has('is_all')) 
                {
                    foreach ($this->listDepartment() as $department) 
                    {
                        $request->merge([
                            'department_id' => $department->id,
                        ]);  
                        $this->savingTypeEloquent->create($request, $this->subject_student);                           
                    }
                } else {
                    $this->savingTypeEloquent->create($request, $this->subject_student);
                }
            } else {
                $this->savingTypeEloquent->update($request, $this->subject_student);
            }
            $response = $this->getResponse('store', '', $this->subject_student);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Kombinasi Departemen, Nama, Rekening Kas dan Rekening Utang');
        }
        return response()->json($response);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return JSON
     */
    public function showStudent($id)
    {
        return response()->json(SavingType::find($id));
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return JSON
     */
    public function dataStudent(Request $request)
    {
        return response()->json($this->savingTypeEloquent->data($request, 0));
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return JSON
     */
    public function destroyStudent($id)
    {
        try 
        {
            $this->savingTypeEloquent->destroy($id, $this->subject_student);
            $response = $this->getResponse('destroy', '', $this->subject_student);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), $this->subject_student);
        }
        return response()->json($response);
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfStudent(Request $request)
    {
        $idArray = collect(json_decode($request->data))->pluck('id')->toArray();
        $data['saving_types'] = SavingType::whereIn('id', $idArray)->orderBy('id')->get();
        $data['type'] = 'Santri';
        // 
        $view = View::make('finance::pages.savings.saving_type_pdf', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject_student)->snake();
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfLandscape($hashfile, $filename);
        echo $filename;
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return JSON
     */
    public function combogrid(Request $request)
    {
        return response()->json($this->savingTypeEloquent->combogrid($request));
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return JSON
     */
    public function combobox(Request $request)
    {
        return response()->json($this->savingTypeEloquent->combobox($request));
    }

    // 

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexEmployee(Request $request)
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
        $data['departments'] = $this->allDepartment();
        $data['codes_cash'] = $this->codeEloquent->combobox(1,'1-1');
        $data['codes_credit'] = $this->codeEloquent->combobox(2);
        return view('finance::pages.savings.employee_saving_type', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function storeEmployee(SavingTypeRequest $request)
    {
        $validated = $request->validated();
        try 
        {
            $request->merge([
                'is_employee' => 1,
                'is_active' => $request->is_active ?: 1,
                'logged' => auth()->user()->email,
            ]);
            if ($request->id < 1) 
            {
                if ($request->has('is_all')) 
                {
                    foreach ($this->listDepartment() as $department) 
                    {
                        $request->merge([
                            'department_id' => $department->id,
                        ]);  
                        $this->savingTypeEloquent->create($request, $this->subject_employee);                           
                    }
                } else {
                    $this->savingTypeEloquent->create($request, $this->subject_employee);
                }
            } else {
                $this->savingTypeEloquent->update($request, $this->subject_employee);
            }
            $response = $this->getResponse('store', '', $this->subject_employee);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Kombinasi Departemen, Nama, Rekening Kas dan Rekening Utang');
        }
        return response()->json($response);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return JSON
     */
    public function showEmployee($id)
    {
        return response()->json(SavingType::find($id));
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return JSON
     */
    public function dataEmployee(Request $request)
    {
        return response()->json($this->savingTypeEloquent->data($request, 1));
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return JSON
     */
    public function destroyEmployee($id)
    {
        try 
        {
            $this->savingTypeEloquent->destroy($id, $this->subject_employee);
            $response = $this->getResponse('destroy', '', $this->subject_employee);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), $this->subject_employee);
        }
        return response()->json($response);
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfEmployee(Request $request)
    {
        $idArray = collect(json_decode($request->data))->pluck('id')->toArray();
        $data['saving_types'] = SavingType::whereIn('id', $idArray)->orderBy('id')->get();
        $data['type'] = 'Pegawai';
        // 
        $view = View::make('finance::pages.savings.saving_type_pdf', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject_employee)->snake();
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfLandscape($hashfile, $filename);
        echo $filename;
    }
}
