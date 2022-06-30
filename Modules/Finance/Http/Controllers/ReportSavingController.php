<?php

namespace Modules\Finance\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Reference;
use App\Http\Traits\DepartmentTrait;
use App\Http\Traits\ReferenceTrait;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\PdfTrait;
use Modules\Finance\Entities\BookYear;
use Modules\Finance\Entities\Journal;
use Modules\Finance\Repositories\Saving\SavingTypeEloquent;
use Modules\Finance\Repositories\Saving\SavingEloquent;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use View;
use Exception;

class ReportSavingController extends Controller
{
    use DepartmentTrait;
    use ReferenceTrait;
    use HelperTrait;
    use PdfTrait;

    private $subject = 'Laporan Tabungan';

    function __construct(
        SavingTypeEloquent $savingTypeEloquent, 
        SavingEloquent $savingEloquent, 
    )
    {
        $this->savingTypeEloquent = $savingTypeEloquent;
        $this->savingEloquent = $savingEloquent;
    }

    // class

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexSavingClass(Request $request)
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
        $data['bookyears'] = BookYear::orderByDesc('id')->get();
        return view('finance::reports.savings.class', $data);
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return JSON
     */
    public function dataSavingClass(Request $request)
    {
        $schoolyear = explode('/', $request->schoolyear);
        $request->merge([
            'bookyear_id' => BookYear::where('book_year',$schoolyear[0])->pluck('id')->first(),
        ]);
        return response()->json($this->savingEloquent->dataSavingClass($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfSavingClass(Request $request)
    {
        $payload = json_decode($request->data);
        $data['requests'] = $payload;
        $data['profile'] = $this->getInstituteProfile();
        // 
        $view = View::make('finance::reports.savings.class_pdf', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_kelas';
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfLandscape($hashfile, $filename);
        echo $filename;
    }

    /**
     * Export resource to Excel Document.
     * @return Excel
     */
    public function toExcelSavingClass(Request $request)
    {
        $data['profile'] = $this->getInstituteProfile();
        $data['payloads'] = json_decode($request->data);
        // 
        $view = View::make('finance::reports.savings.class_xlsx', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_kelas';
        $filename = date('Ymdhis') . '_' . $name . '.xlsx';
        //
        Storage::disk('local')->put('public/downloads/'.$filename, $view->render());
        echo $filename;
    }

    // student

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexSavingStudent(Request $request)
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
        $data['bookyears'] = BookYear::orderByDesc('id')->get();
        return view('finance::reports.savings.student', $data);
    }
    
    public function viewSavingStudent(Request $request)
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
        $periods = explode('/', $request->schoolyear);
        $bookyear = BookYear::where('book_year', $periods[0])->first();
        if (!empty($bookyear)) 
        {
            $request->merge([
                'bookyear_id' => $bookyear->id,
            ]);
            $data['requests'] = $request->all();
            $data['savings'] = $this->savingEloquent->dataSavingDetail(0, $request->student_id, $bookyear->id, $request->start_date, $request->end_date);
            return view('finance::reports.savings.student_view', $data);
        } else {
            $data['error'] = 'Belum ada <b>Tahun Buku</b> yang dibuat, gunakan menu<br/> Data Master &#8594; Tahun Buku untuk membuat baru.';
            return view('errors.400', $data);
        }
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfSavingStudent(Request $request)
    {
        $payload = json_decode($request->data);
        $data['requests'] = $payload;
        $data['profile'] = $this->getInstituteProfile();
        $data['savings'] = $this->savingEloquent->dataSavingDetail(0, $payload->student_id, $payload->bookyear_id, $payload->start_date, $payload->end_date);
        // 
        $view = View::make('finance::reports.savings.student_pdf', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_santri';
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortraits($hashfile, $filename);
        echo $filename;
    }

    /**
     * Export resource to Excel Document.
     * @return Excel
     */
    public function toExcelSavingStudent(Request $request)
    {
        $data['profile'] = $this->getInstituteProfile();
        $data['payloads'] = json_decode($request->data);
        $data['savings'] = $this->savingEloquent->dataSavingDetail(0, $data['payloads']->student_id, $data['payloads']->bookyear_id, $data['payloads']->start_date, $data['payloads']->end_date);
        // 
        $view = View::make('finance::reports.savings.student_xlsx', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_santri';
        $filename = date('Ymdhis') . '_' . $name . '.xlsx';
        //
        Storage::disk('local')->put('public/downloads/'.$filename, $view->render());
        echo $filename;
    }

    // student recap

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexSavingStudentRecap(Request $request)
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
        $data['employees'] = Journal::select('employee_id')->groupBy('employee_id')->orderBy('employee_id')->get()->map(function($model){
            $model['employee'] = $this->getEmployeeName($model->employee_id);
            return $model;
        });  
        return view('finance::reports.savings.student_recap', $data);
    }

    public function viewSavingStudentRecap(Request $request)
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
        $data['requests'] = $request->all();
        $data['savings'] = $this->savingEloquent->dataSavingRecap(0, $request->department_id, $request->employee_id, $request->start_date, $request->end_date);
        return view('finance::reports.savings.student_recap_view', $data);
    }

    public function detailSavingStudentRecap(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $data['requests'] = $request->all();
        $data['details'] = $this->savingEloquent->dataSavingDetailTrx(0, $request->department_id, $request->saving_id, $request->employee_id, $request->start_date, $request->end_date, $request->type);
        return view('finance::reports.savings.student_recap_detail', $data);
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfSavingStudentRecap(Request $request)
    {
        $payload = json_decode($request->data);
        $data['requests'] = $payload;
        $data['profile'] = $this->getInstituteProfile();
        $data['savings'] = $this->savingEloquent->dataSavingRecap(0, $payload->department_id, $payload->employee_id, $payload->start_date, $payload->end_date);
        // 
        $view = View::make('finance::reports.savings.student_recap_pdf', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_rekapitulasi_santri';
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortraits($hashfile, $filename);
        echo $filename;
    }

    /**
     * Export resource to Excel Document.
     * @return Excel
     */
    public function toExcelSavingStudentRecap(Request $request)
    {
        $data['profile'] = $this->getInstituteProfile();
        $data['payloads'] = json_decode($request->data);
        $data['savings'] = $this->savingEloquent->dataSavingRecap(0, $data['payloads']->department_id, $data['payloads']->employee_id, $data['payloads']->start_date, $data['payloads']->end_date);
        // 
        $view = View::make('finance::reports.savings.student_recap_xlsx', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_rekapitulasi_santri';
        $filename = date('Ymdhis') . '_' . $name . '.xlsx';
        //
        Storage::disk('local')->put('public/downloads/'.$filename, $view->render());
        echo $filename;
    }

    // employee

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexSavingEmployee(Request $request)
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
        return view('finance::reports.savings.employee', $data);
    }
    
    public function viewSavingEmployee(Request $request)
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
        $years = explode("/", $request->start_date);
        $bookyear = BookYear::where('book_year', $years[2])->first();
        if (!empty($bookyear)) 
        {
            $request->merge([
                'bookyear_id' => $bookyear->id,
            ]);
            $data['requests'] = $request->all();
            $data['savings'] = $this->savingEloquent->dataSavingDetail(1, $request->employee_id, $bookyear->id, $request->start_date, $request->end_date);
            return view('finance::reports.savings.employee_view', $data);
        } else {
            $data['error'] = 'Belum ada <b>Tahun Buku</b> yang dibuat, gunakan menu<br/> Data Master &#8594; Tahun Buku untuk membuat baru.';
            return view('errors.400', $data);
        }
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfSavingEmployee(Request $request)
    {
        $payload = json_decode($request->data);
        $data['requests'] = $payload;
        $data['profile'] = $this->getInstituteProfile();
        $data['savings'] = $this->savingEloquent->dataSavingDetail(1, $payload->employee_id, $payload->bookyear_id, $payload->start_date, $payload->end_date);
        // 
        $view = View::make('finance::reports.savings.employee_pdf', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_pegawai';
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortraits($hashfile, $filename);
        echo $filename;
    }

    /**
     * Export resource to Excel Document.
     * @return Excel
     */
    public function toExcelSavingEmployee(Request $request)
    {
        $data['profile'] = $this->getInstituteProfile();
        $data['payloads'] = json_decode($request->data);
        $data['savings'] = $this->savingEloquent->dataSavingDetail(1, $data['payloads']->employee_id, $data['payloads']->bookyear_id, $data['payloads']->start_date, $data['payloads']->end_date);
        // 
        $view = View::make('finance::reports.savings.employee_xlsx', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_pegawai';
        $filename = date('Ymdhis') . '_' . $name . '.xlsx';
        //
        Storage::disk('local')->put('public/downloads/'.$filename, $view->render());
        echo $filename;
    }

    // employee recap

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexSavingEmployeeRecap(Request $request)
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
        $data['employees'] = Journal::select('employee_id')->groupBy('employee_id')->orderBy('employee_id')->get()->map(function($model){
            $model['employee'] = $this->getEmployeeName($model->employee_id);
            return $model;
        });
        return view('finance::reports.savings.employee_recap', $data);
    }

    public function viewSavingEmployeeRecap(Request $request)
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
        $data['requests'] = $request->all();
        $data['savings'] = $this->savingEloquent->dataSavingRecap(1, 1, $request->employee_id, $request->start_date, $request->end_date);
        return view('finance::reports.savings.employee_recap_view', $data);
    }

    public function detailSavingEmployeeRecap(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $data['requests'] = $request->all();
        $data['details'] = $this->savingEloquent->dataSavingDetailTrx(1, 1, $request->saving_id, $request->employee_id, $request->start_date, $request->end_date, $request->type);
        return view('finance::reports.savings.employee_recap_detail', $data);
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfSavingEmployeeRecap(Request $request)
    {
        $payload = json_decode($request->data);
        $data['requests'] = $payload;
        $data['profile'] = $this->getInstituteProfile();
        $data['savings'] = $this->savingEloquent->dataSavingRecap(1, 1, $payload->employee_id, $payload->start_date, $payload->end_date);
        // 
        $view = View::make('finance::reports.savings.employee_recap_pdf', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_rekapitulasi_pegawai';
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortraits($hashfile, $filename);
        echo $filename;
    }

    /**
     * Export resource to Excel Document.
     * @return Excel
     */
    public function toExcelSavingEmployeeRecap(Request $request)
    {
        $data['profile'] = $this->getInstituteProfile();
        $data['payloads'] = json_decode($request->data);
        $data['savings'] = $this->savingEloquent->dataSavingRecap(1, 1, $data['payloads']->employee_id, $data['payloads']->start_date, $data['payloads']->end_date);
        // 
        $view = View::make('finance::reports.savings.employee_recap_xlsx', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_rekapitulasi_pegawai';
        $filename = date('Ymdhis') . '_' . $name . '.xlsx';
        //
        Storage::disk('local')->put('public/downloads/'.$filename, $view->render());
        echo $filename;
    }

}
