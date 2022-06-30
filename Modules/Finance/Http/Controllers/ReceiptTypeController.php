<?php

namespace Modules\Finance\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\PdfTrait;
use App\Http\Traits\ReferenceTrait;
use App\Http\Traits\DepartmentTrait;
use Modules\Finance\Entities\ReceiptType;
use Modules\Finance\Repositories\Receipt\ReceiptTypeEloquent;
use Modules\Finance\Repositories\Reference\CodeEloquent;
use Modules\Finance\Http\Requests\ReceiptTypeRequest;
use View;
use Exception;

class ReceiptTypeController extends Controller
{
    use HelperTrait;
    use PdfTrait;
    use ReferenceTrait;
    use DepartmentTrait;

    private $subject = 'Data Jenis Penerimaan';

    function __construct(ReceiptTypeEloquent $receiptTypeEloquent, CodeEloquent $codeEloquent)
    {
        $this->receiptTypeEloquent = $receiptTypeEloquent;
        $this->codeEloquent = $codeEloquent;
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
        $data['categories'] = DB::table('finance.receipt_categories')->select('id','category')->get();
        $data['codes_activa'] = $this->codeEloquent->combobox(1);
        $data['codes_receipt'] = $this->codeEloquent->combobox(4);
        return view('finance::pages.receipts.receipt_type', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(ReceiptTypeRequest $request)
    {
        $validated = $request->validated();
        try 
        {
            $request->merge([
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
                        $this->receiptTypeEloquent->create($request, $this->subject);                           
                    }
                } else {
                    $this->receiptTypeEloquent->create($request, $this->subject);
                }
            } else {
                $this->receiptTypeEloquent->update($request, $this->subject);
            }
            $response = $this->getResponse('store', '', $this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Kombinasi Departemen dan Nama');
        }
        return response()->json($response);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return JSON
     */
    public function show($id)
    {
        return response()->json(ReceiptType::find($id));
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return JSON
     */
    public function data(Request $request)
    {
        return response()->json($this->receiptTypeEloquent->data($request));
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return JSON
     */
    public function destroy($id)
    {
        try 
        {
            $this->receiptTypeEloquent->destroy($id, $this->subject);
            $response = $this->getResponse('destroy', '', $this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), $this->subject);
        }
        return response()->json($response);
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdf(Request $request)
    {
        $idArray = collect(json_decode($request->data))->pluck('id')->toArray();
        $data['receipt_types'] = ReceiptType::whereIn('id', $idArray)->orderBy('id')->get();
        // 
        $view = View::make('finance::pages.receipts.receipt_type_pdf', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake();
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfLandscape($hashfile, $filename);
        echo $filename;
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function combobox($id, $deptid)
    {
        return response()->json($this->receiptTypeEloquent->combobox($id, $deptid));
    }

    /**
     * Show the specified resource.
     * @param Request $request
     * @return Renderable
     */
    public function combogrid(Request $request)
    {
        return response()->json($this->receiptTypeEloquent->combogrid($request));
    }

    /**
     * Show the specified resource.
     * @param Request $request
     * @return Renderable
     */
    public function combogridPayment(Request $request)
    {
        return response()->json($this->receiptTypeEloquent->combogridPayment($request));
    }
}
