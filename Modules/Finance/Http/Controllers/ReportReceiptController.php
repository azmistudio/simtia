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
use Modules\Finance\Entities\ReceiptType;
use Modules\Finance\Entities\ReceiptCategory;
use Modules\Finance\Repositories\Journal\JournalEloquent;   
use Modules\Finance\Repositories\Receipt\ReceiptTypeEloquent;
use Modules\Finance\Repositories\Receipt\ReceiptMajorEloquent;
use Modules\Finance\Repositories\Receipt\ReceiptVoluntaryEloquent;
use Modules\Finance\Repositories\Receipt\ReceiptOtherEloquent;
use Modules\Finance\Repositories\Receipt\PaymentMajorEloquent;
use Modules\Finance\Repositories\Reference\CodeEloquent; 
use Modules\Finance\Repositories\Reference\BookYearEloquent; 
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use View;
use Exception;

class ReportReceiptController extends Controller
{
    use DepartmentTrait;
    use ReferenceTrait;
    use HelperTrait;
    use PdfTrait;

    private $subject = 'Laporan Penerimaan';

    function __construct(
        JournalEloquent $journalEloquent, 
        ReceiptTypeEloquent $receiptTypeEloquent, 
        ReceiptMajorEloquent $receiptMajorEloquent, 
        ReceiptVoluntaryEloquent $receiptVoluntaryEloquent, 
        ReceiptOtherEloquent $receiptOtherEloquent, 
        PaymentMajorEloquent $paymentMajorEloquent, 
        CodeEloquent $codeEloquent,
        BookYearEloquent $bookYearEloquent,
    )
    {
        $this->journalEloquent = $journalEloquent;
        $this->receiptTypeEloquent = $receiptTypeEloquent;
        $this->receiptMajorEloquent = $receiptMajorEloquent;
        $this->receiptVoluntaryEloquent = $receiptVoluntaryEloquent;
        $this->receiptOtherEloquent = $receiptOtherEloquent;
        $this->paymentMajorEloquent = $paymentMajorEloquent;
        $this->codeEloquent = $codeEloquent;
        $this->bookYearEloquent = $bookYearEloquent;
    }

    // Class Payment

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexReceiptClass(Request $request)
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
        $data['categories'] = ReceiptCategory::whereIn('code',['JTT','SKR'])->get();
        return view('finance::reports.receipts.class', $data);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexReceiptClassView(Request $request)
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
        $request->merge([
            'bookyear_id' => $bookyear->id,
        ]);
        $data['requests'] = $request->all();
        $category = ReceiptCategory::find($request->category_id);
        if ($category->code == 'JTT')
        {
            return view('finance::reports.receipts.class_mandatory', $data);
        } else {
            return view('finance::reports.receipts.class_voluntary', $data);
        }
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return JSON
     */
    public function dataReceiptClass(Request $request)
    {
        if ($request->status > -2)
        {
            return response()->json($this->receiptMajorEloquent->dataPaymentClass($request));
        } else {
            return response()->json($this->receiptVoluntaryEloquent->dataPaymentClass($request));
        }
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfReceiptClass(Request $request)
    {
        $payload = json_decode($request->data);
        $data['requests'] = $payload;
        $data['period'] = $this->getPeriodName($payload->period);
        $request->merge([
            'bookyear_id' => $payload->bookyear_id,
            'class_id' => $payload->class_id,
            'department_id' => $payload->department_id,
            'is_prospect' => $payload->is_prospect,
            'period' => $payload->period,
        ]);
        $data['profile'] = $this->getInstituteProfile();
        if ($data['requests']->status > -2)
        {
            $data['max_installment'] = $this->receiptMajorEloquent->maxInstallment($request);
            $data['payments'] = $this->receiptMajorEloquent->paymentClass($payload->bookyear_id, $payload->class_id, $payload->status, $payload->period)->get();
            $view = View::make('finance::reports.receipts.class_mandatory_pdf', $data);
            $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_pembayaran_kelas';
        } else {
            $data['max_installment'] = $this->receiptVoluntaryEloquent->maxInstallment($request)->pluck('total_trx')->max();
            $data['payments'] = $this->receiptVoluntaryEloquent->paymentClass($payload->bookyear_id, $payload->department_id, $payload->class_id, $payload->is_prospect)->get();
            $view = View::make('finance::reports.receipts.class_voluntary_pdf', $data);
            $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_pembayaran_kelas_sukarela';
        }
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
    public function toExcelReceiptClass(Request $request)
    {
        $data['profile'] = $this->getInstituteProfile();
        $data['payloads'] = json_decode($request->data);
        $data['period'] = $this->getPeriodName($data['payloads']->period);
        $request->merge([
            'bookyear_id' => $data['payloads']->bookyear_id,
            'class_id' => $data['payloads']->class_id,
            'department_id' => $data['payloads']->department_id,
            'is_prospect' => $data['payloads']->is_prospect,
            'period' => $data['payloads']->period,
        ]);
        if ($data['payloads']->status > -2)
        {
            $data['max_installment'] = $this->receiptMajorEloquent->maxInstallment($request);
            $data['payments'] = $this->receiptMajorEloquent->paymentClass($data['payloads']->bookyear_id, $data['payloads']->class_id, $data['payloads']->status, $data['payloads']->period)->get();
            $view = View::make('finance::reports.receipts.class_mandatory_xlsx', $data);
            $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_pembayaran_kelas';
        } else {
            $data['max_installment'] = $this->receiptVoluntaryEloquent->maxInstallment($request)->pluck('total_trx')->max();
            $data['payments'] = $this->receiptVoluntaryEloquent->paymentClass($data['payloads']->bookyear_id, $data['payloads']->department_id, $data['payloads']->class_id, $data['payloads']->is_prospect)->get();
            $view = View::make('finance::reports.receipts.class_voluntary_xlsx', $data);
            $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_pembayaran_kelas_sukarela';
        }
        $filename = date('Ymdhis') . '_' . $name . '.xlsx';
        Storage::disk('local')->put('public/downloads/'.$filename, $view->render());
        echo $filename;
    }

    // Student Payment

    public function indexReceiptStudent(Request $request)
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
        return view('finance::reports.receipts.student', $data);
    }

    public function indexReceiptStudentMandatory(Request $request)
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
            $data['mandatories'] = $this->paymentMajorEloquent->dataPaymentStudent($request);
            $data['voluntaries'] = $this->receiptVoluntaryEloquent->dataPaymentStudent($request);
            return view('finance::reports.receipts.student_mandatory', $data);
        } else {
            $data['error'] = 'Belum ada <b>Tahun Buku</b> yang dibuat, gunakan menu<br/> Data Master &#8594; Tahun Buku untuk membuat baru.';
            return view('errors.400', $data);
        }
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfReceiptStudent(Request $request)
    {
        $data['requests'] = json_decode($request->data);
        $data['profile'] = $this->getInstituteProfile();
        $request->merge([
            'bookyear_id' => $data['requests']->bookyear_id,
            'is_prospect' => $data['requests']->is_prospect,
            'student_id' => $data['requests']->student_id,
            'start_date' => $data['requests']->start_date,
            'end_date' => $data['requests']->end_date,
        ]);
        $data['mandatories'] = $this->paymentMajorEloquent->dataPaymentStudent($request);
        $data['voluntaries'] = $this->receiptVoluntaryEloquent->dataPaymentStudent($request);
        // 
        $view = View::make('finance::reports.receipts.student_mandatory_pdf', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_pembayaran_santri';
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
    public function toExcelReceiptStudent(Request $request)
    {
        $data['profile'] = $this->getInstituteProfile();
        $data['payloads'] = json_decode($request->data);
        $request->merge([
            'bookyear_id' => $data['payloads']->bookyear_id,
            'is_prospect' => $data['payloads']->is_prospect,
            'student_id' => $data['payloads']->student_id,
            'start_date' => $data['payloads']->start_date,
            'end_date' => $data['payloads']->end_date,
        ]);
        $data['mandatories'] = $this->paymentMajorEloquent->dataPaymentStudent($request);
        $data['voluntaries'] = $this->receiptVoluntaryEloquent->dataPaymentStudent($request);
        // 
        $view = View::make('finance::reports.receipts.student_mandatory_xlsx', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_pembayaran_santri';
        $filename = date('Ymdhis') . '_' . $name . '.xlsx';
        //
        Storage::disk('local')->put('public/downloads/'.$filename, $view->render());
        echo $filename;
    }

    // Student Payment Unpaid

    public function indexReceiptStudentArrear(Request $request)
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
        return view('finance::reports.receipts.student_arrear', $data);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexReceiptStudentArrearView(Request $request)
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
        $request->merge([
            'bookyear_id' => $bookyear->id,
        ]);
        $data['requests'] = $request->all();
        return view('finance::reports.receipts.student_arrear_view', $data);
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return JSON
     */
    public function dataReceiptStudentArrear(Request $request)
    {
        return response()->json($this->receiptMajorEloquent->dataPaymentClassArrear($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfReceiptStudentArrear(Request $request)
    {
        $payload = json_decode($request->data);
        $data['requests'] = $payload;
        $data['period'] = $this->getPeriodName($payload->period);
        $data['max_installment'] = $this->receiptMajorEloquent->paymentClassDelay($payload->bookyear_id, $payload->payment_id, $payload->duration, $payload->date_delay,0,$payload->period)->get()->pluck('count_trx')->max();
        $data['payments'] = $this->receiptMajorEloquent->paymentClassArrear($payload->bookyear_id, $payload->payment_id, $payload->duration, $payload->date_delay,$payload->period)->get();
        $data['profile'] = $this->getInstituteProfile();
        // 
        $view = View::make('finance::reports.receipts.student_arrear_pdf', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_pembayaran_santri_tunggak';
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
    public function toExcelReceiptStudentArrear(Request $request)
    {
        $data['profile'] = $this->getInstituteProfile();
        $data['payloads'] = json_decode($request->data);
        $data['period'] = $this->getPeriodName($data['payloads']->period);
        $data['max_installment'] = $this->receiptMajorEloquent->paymentClassDelay($data['payloads']->bookyear_id, $data['payloads']->payment_id, $data['payloads']->duration, $data['payloads']->date_delay,0,$data['payloads']->period)->get()->pluck('count_trx')->max();
        $data['payments'] = $this->receiptMajorEloquent->paymentClassArrear($data['payloads']->bookyear_id, $data['payloads']->payment_id, $data['payloads']->duration, $data['payloads']->date_delay,$data['payloads']->period)->get();
        // 
        $view = View::make('finance::reports.receipts.student_arrear_xlsx', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_pembayaran_santri_tunggak';
        $filename = date('Ymdhis') . '_' . $name . '.xlsx';
        //
        Storage::disk('local')->put('public/downloads/'.$filename, $view->render());
        echo $filename;
    }

    // Prospect Group Student Payment

    public function indexReceiptStudentProspectGroup(Request $request)
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
        $data['categories'] = ReceiptCategory::whereIn('code',['CSWJB','CSSKR'])->get();
        return view('finance::reports.receipts.student_prospect_group', $data);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexStudentProspectGroupView(Request $request)
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
        $category = ReceiptCategory::find($request->category_id);
        if ($category->code == 'CSWJB')
        {
            return view('finance::reports.receipts.student_prospect_group_mandatory', $data);
        } else {
            return view('finance::reports.receipts.student_prospect_group_voluntary', $data);
        }
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return JSON
     */
    public function dataStudentProspectGroup(Request $request)
    {
        if ($request->status > -2)
        {
            return response()->json($this->receiptMajorEloquent->dataPaymentProspectGroup($request));
        } else {
            return response()->json($this->receiptVoluntaryEloquent->dataPaymentProspectGroup($request));
        }
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfStudentProspectGroup(Request $request)
    {
        $payload = json_decode($request->data);
        $data['requests'] = $payload;
        $request->merge([
            'prospect_group_id' => $payload->prospect_group_id,
            'department_id' => $payload->department_id,
            'category' => $payload->category,
            'is_prospect' => $payload->is_prospect,
        ]);
        $data['profile'] = $this->getInstituteProfile();
        if ($payload->status > -2)
        {
            $data['max_installment'] = $this->receiptMajorEloquent->maxInstallmentProspectGroup($request)->pluck('total_trx')->max();
            $data['payments'] = $this->receiptMajorEloquent->paymentProspectGroup($payload->department_id, $payload->category, $payload->prospect_group_id, $payload->status)->get();
            $view = View::make('finance::reports.receipts.student_prospect_group_mandatory_pdf', $data);
            $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_pembayaran_kelas';
        } else {
            $data['max_installment'] = $this->receiptVoluntaryEloquent->maxInstallmentProspectGroup($request)->pluck('total_trx')->max();
            $data['payments'] = $this->receiptVoluntaryEloquent->paymentProspectGroup($payload->department_id, $payload->prospect_group_id)->get();
            $view = View::make('finance::reports.receipts.student_prospect_group_voluntary_pdf', $data);
            $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_pembayaran_kelas_sukarela';
        }
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
    public function toExcelStudentProspectGroup(Request $request)
    {
        $data['profile'] = $this->getInstituteProfile();
        $data['payloads'] = json_decode($request->data);
        $request->merge([
            'prospect_group_id' => $data['payloads']->prospect_group_id,
            'department_id' => $data['payloads']->department_id,
            'category' => $data['payloads']->category,
            'is_prospect' => $data['payloads']->is_prospect,
        ]);
        if ($data['payloads']->status > -2)
        {
            $data['max_installment'] = $this->receiptMajorEloquent->maxInstallmentProspectGroup($request)->pluck('total_trx')->max();
            $data['payments'] = $this->receiptMajorEloquent->paymentProspectGroup($data['payloads']->department_id, $data['payloads']->category, $data['payloads']->prospect_group_id, $data['payloads']->status)->get();
            $view = View::make('finance::reports.receipts.student_prospect_group_mandatory_xlsx', $data);
            $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_pembayaran_kelompok_calon_santri';
        } else {
            $data['max_installment'] = $this->receiptVoluntaryEloquent->maxInstallmentProspectGroup($request)->pluck('total_trx')->max();
            $data['payments'] = $this->receiptVoluntaryEloquent->paymentProspectGroup($data['payloads']->department_id, $data['payloads']->prospect_group_id)->get();
            $view = View::make('finance::reports.receipts.student_prospect_group_voluntary_xlsx', $data);
            $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_pembayaran_kelompok_calon_santri_sukarela';
        }
        $filename = date('Ymdhis') . '_' . $name . '.xlsx';
        Storage::disk('local')->put('public/downloads/'.$filename, $view->render());
        echo $filename;
    }

    // Prospect Student Payment

    public function indexReceiptProspect(Request $request)
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
        return view('finance::reports.receipts.student_prospect', $data);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexReceiptProspectView(Request $request)
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
        $periods = explode('/', $request->start_date);
        $bookyear = BookYear::where('book_year', $periods[2])->first();
        if (!empty($bookyear)) 
        {
            $request->merge([
                'bookyear_id' => $bookyear->id,
            ]);
            $data['requests'] = $request->all();
            $data['mandatories'] = $this->paymentMajorEloquent->dataPaymentStudent($request);
            $data['voluntaries'] = $this->receiptVoluntaryEloquent->dataPaymentStudent($request);
            return view('finance::reports.receipts.student_prospect_view', $data);
        } else {
            $data['error'] = 'Belum ada <b>Tahun Buku</b> yang dibuat, gunakan menu<br/> Data Master &#8594; Tahun Buku untuk membuat baru.';
            return view('errors.400', $data);
        }
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfReceiptProspect(Request $request)
    {
        $data['requests'] = json_decode($request->data);
        $data['profile'] = $this->getInstituteProfile();
        $request->merge([
            'bookyear_id' => $data['requests']->bookyear_id,
            'is_prospect' => $data['requests']->is_prospect,
            'student_id' => $data['requests']->student_id,
            'start_date' => $data['requests']->start_date,
            'end_date' => $data['requests']->end_date,
        ]);
        $data['mandatories'] = $this->paymentMajorEloquent->dataPaymentStudent($request);
        $data['voluntaries'] = $this->receiptVoluntaryEloquent->dataPaymentStudent($request);
        // 
        $view = View::make('finance::reports.receipts.student_prospect_pdf', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_pembayaran_calon_santri';
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
    public function toExcelReceiptProspect(Request $request)
    {
        $data['profile'] = $this->getInstituteProfile();
        $data['payloads'] = json_decode($request->data);
        $request->merge([
            'bookyear_id' => $data['payloads']->bookyear_id,
            'is_prospect' => $data['payloads']->is_prospect,
            'student_id' => $data['payloads']->student_id,
            'start_date' => $data['payloads']->start_date,
            'end_date' => $data['payloads']->end_date,
        ]);
        $data['mandatories'] = $this->paymentMajorEloquent->dataPaymentStudent($request);
        $data['voluntaries'] = $this->receiptVoluntaryEloquent->dataPaymentStudent($request);
        // 
        $view = View::make('finance::reports.receipts.student_prospect_xlsx', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_pembayaran_calon_santri';
        $filename = date('Ymdhis') . '_' . $name . '.xlsx';
        //
        Storage::disk('local')->put('public/downloads/'.$filename, $view->render());
        echo $filename;
    }

    // Prospect Student Payment Unpaid

    public function indexReceiptProspectArrear(Request $request)
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
        return view('finance::reports.receipts.student_prospect_arrear', $data);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexReceiptProspectArrearView(Request $request)
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
        return view('finance::reports.receipts.student_prospect_arrear_view', $data);
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return JSON
     */
    public function dataReceiptProspectArrear(Request $request)
    {
        $category = ReceiptCategory::where('code',$request->category)->first();
        $request->merge([
            'category_id' => $category->id,
        ]);
        return response()->json($this->receiptMajorEloquent->dataPaymentProspectArrear($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfReceiptProspectArrear(Request $request)
    {
        $payload = json_decode($request->data);
        $data['requests'] = $payload;
        $data['category'] = ReceiptCategory::where('code',$payload->category)->first();
        $data['max_installment'] = $this->receiptMajorEloquent->paymentProspectDelay($payload->department_id, $data['category']->id, $payload->payment_id, $payload->duration, $payload->date_delay, 0)->get()->pluck('count_trx')->max();
        $data['payments'] = $this->receiptMajorEloquent->paymentProspectArrear($payload->department_id, $data['category']->id, $payload->payment_id, $payload->duration, $payload->date_delay)->get();
        $data['profile'] = $this->getInstituteProfile();
        // 
        $view = View::make('finance::reports.receipts.student_prospect_arrear_pdf', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_pembayaran_calon_santri_tunggak';
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
    public function toExcelReceiptProspectArrear(Request $request)
    {
        $data['profile'] = $this->getInstituteProfile();
        $data['payloads'] = json_decode($request->data);
        $data['category'] = ReceiptCategory::where('code',$data['payloads']->category)->first();
        $data['max_installment'] = $this->receiptMajorEloquent->paymentProspectDelay($data['payloads']->department_id, $data['category']->id, $data['payloads']->payment_id, $data['payloads']->duration, $data['payloads']->date_delay, 0)->get()->pluck('count_trx')->max();
        $data['payments'] = $this->receiptMajorEloquent->paymentProspectArrear($data['payloads']->department_id, $data['category']->id, $data['payloads']->payment_id, $data['payloads']->duration, $data['payloads']->date_delay)->get();
        // 
        $view = View::make('finance::reports.receipts.student_prospect_arrear_xlsx', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_pembayaran_calon_santri_tunggak';
        $filename = date('Ymdhis') . '_' . $name . '.xlsx';
        //
        Storage::disk('local')->put('public/downloads/'.$filename, $view->render());
        echo $filename;
    }

    // Recap

    public function indexReceiptRecap(Request $request)
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
        $data['bookyears'] = BookYear::orderByDesc('id')->get();        
        $data['departments'] = $this->listDepartment();     
        $data['categories'] = ReceiptCategory::get(); 
        $data['employees'] = Journal::select('employee_id')->groupBy('employee_id')->orderBy('employee_id')->get()->map(function($model){
            $model['employee'] = $this->getEmployeeName($model->employee_id);
            return $model;
        });  
        return view('finance::reports.receipts.recap', $data);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexReceiptRecapView(Request $request)
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
        $departmentArray = [];
        if ($request->department_id > 1)
        {
            array_push($departmentArray, (object)['id' => $request->department_id, 'name' => $request->department]);
            $data['departments'] = $departmentArray;
        } else {
            $data['departments'] = $this->listDepartment();
        }
        $data['requests'] = $request->all();
        if ($request->type_id == "total")
        {
            return view('finance::reports.receipts.recap_total_view', $data);
        } else {
            return view('finance::reports.receipts.recap_daily_view', $data);
        }
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexReceiptRecapDetail(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $data['requests'] = $request->all();
        if ($request->type_id == "total")
        {
            if ($request->receipt_category_id == 'JTT' || $request->receipt_category_id == 'CSWJB')
            {
                $data['details'] = $this->receiptMajorEloquent->dataRecapTotalDetail($request->bookyear_id, $request->department_id, $request->receipt_category_id, $request->start_date, $request->end_date, $request->employee_id);
            } elseif ($request->receipt_category_id == 'SKR' || $request->receipt_category_id == 'CSSKR') {
                $data['details'] = $this->receiptVoluntaryEloquent->dataRecapTotalDetail($request->bookyear_id, $request->department_id, $request->receipt_category_id, $request->start_date, $request->end_date, $request->employee_id);
            } else {
                $data['details'] = $this->receiptOtherEloquent->dataRecapTotalDetail($request->bookyear_id, $request->department_id, $request->receipt_category_id, $request->start_date, $request->end_date, $request->employee_id);
            }
            return view('finance::reports.receipts.recap_total_view_detail', $data);
        } else {
            if ($request->receipt_category_id == 'JTT' || $request->receipt_category_id == 'CSWJB')
            {
                $data['details'] = $this->receiptMajorEloquent->dataRecapTransDetail($request->bookyear_id, $request->department_id, $request->receipt_category_id, $request->trans_date, $request->type_id, $request->employee_id);
            } elseif ($request->receipt_category_id == 'SKR' || $request->receipt_category_id == 'CSSKR') {
                $data['details'] = $this->receiptVoluntaryEloquent->dataRecapTransDetail($request->bookyear_id, $request->department_id, $request->receipt_category_id, $request->trans_date, $request->type_id, $request->employee_id);
            } else {
                $data['details'] = $this->receiptOtherEloquent->dataRecapTransDetail($request->bookyear_id, $request->department_id, $request->receipt_category_id, $request->trans_date, $request->type_id, $request->employee_id);
            }
            return view('finance::reports.receipts.recap_daily_view_detail', $data);
        }
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfReceiptRecap(Request $request)
    {
        $payload = json_decode($request->data);
        $data['requests'] = $payload;
        $data['profile'] = $this->getInstituteProfile();
        $departmentArray = [];
        if ($payload->department_id > 1)
        {
            array_push($departmentArray, (object)['id' => $payload->department_id, 'name' => $payload->department]);
            $data['departments'] = $departmentArray;
        } else {
            $data['departments'] = $this->listDepartment();
        }
        if ($payload->type_id == "total")
        {
            $view = View::make('finance::reports.receipts.recap_total_view_pdf', $data);
        } else {
            $view = View::make('finance::reports.receipts.recap_daily_view_pdf', $data);
        }
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_rekapitulasi_penerimaan';
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
    public function toExcelReceiptRecap(Request $request)
    {
        $data['profile'] = $this->getInstituteProfile();
        $data['payloads'] = json_decode($request->data);
        // 
        $departmentArray = [];
        if ($data['payloads']->department_id > 1)
        {
            array_push($departmentArray, (object)['id' => $data['payloads']->department_id, 'name' => $data['payloads']->department]);
            $data['departments'] = $departmentArray;
        } else {
            $data['departments'] = $this->listDepartment();
        }
        if ($data['payloads']->type_id == "total")
        {
            $view = View::make('finance::reports.receipts.recap_total_view_xlsx', $data);
        } else {
            $view = View::make('finance::reports.receipts.recap_daily_view_xlsx', $data);
        }
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_rekapitulasi_penerimaan';
        $filename = date('Ymdhis') . '_' . $name . '.xlsx';
        //
        Storage::disk('local')->put('public/downloads/'.$filename, $view->render());
        echo $filename;
    }

    // Recap Unpaid

    public function indexReceiptRecapArrear(Request $request)
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
        return view('finance::reports.receipts.recap_arrear', $data);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexReceiptRecapArrearView(Request $request)
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
        $schoolyears = explode('/', $request->schoolyear);
        $data['bookyear'] = BookYear::where('book_year', $schoolyears[0])->first();
        $data['requests'] = $request->all();
        $data['receipt_types'] = $this->receiptTypeEloquent->search('JTT', $request->department_id);
        $data['students'] = $this->paymentMajorEloquent->dataRecapStudent($data['bookyear']->id, $request->department_id, $request->grade_id, $request->class_id);        
        return view('finance::reports.receipts.recap_arrear_view', $data);
    }

    /**
     * Export resource to Excel Document.
     * @return Excel
     */
    public function toExcelReceiptRecapArrear(Request $request)
    {
        $data['profile'] = $this->getInstituteProfile();
        $data['payloads'] = json_decode($request->data);
        $schoolyears = explode('/', $data['payloads']->schoolyear);
        $data['bookyear'] = BookYear::where('book_year', $schoolyears[0])->first();
        $data['receipt_types'] = $this->receiptTypeEloquent->search('JTT', $data['payloads']->department_id);
        $data['students'] = $this->paymentMajorEloquent->dataRecapStudent($data['bookyear']->id, $data['payloads']->department_id, $data['payloads']->grade_id, $data['payloads']->class_id);  
        // 
        $view = View::make('finance::reports.receipts.recap_arrear_xlsx', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_rekapitulasi_santri_tunggak';
        $filename = date('Ymdhis') . '_' . $name . '.xlsx';
        //
        Storage::disk('local')->put('public/downloads/'.$filename, $view->render());
        echo $filename;
    }

    // Other

    public function indexReceiptOther(Request $request)
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
        $data['bookyears'] = BookYear::orderByDesc('id')->get();
        $data['category_id'] = ReceiptCategory::where('code','LNN')->pluck('id')->first();
        return view('finance::reports.receipts.other', $data);
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return JSON
     */
    public function dataReceiptOther(Request $request)
    {
        return response()->json($this->receiptOtherEloquent->dataReceipt($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfReceiptOther(Request $request)
    {
        $payload = json_decode($request->data);
        $data['requests'] = $payload;
        $data['profile'] = $this->getInstituteProfile();
        // 
        $view = View::make('finance::reports.receipts.other_pdf', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_penerimaan_lain';
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
    public function toExcelReceiptOther(Request $request)
    {
        $data['profile'] = $this->getInstituteProfile();
        $data['payloads'] = json_decode($request->data);
        // 
        $view = View::make('finance::reports.receipts.other_xlsx', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_penerimaan_lain';
        $filename = date('Ymdhis') . '_' . $name . '.xlsx';
        //
        Storage::disk('local')->put('public/downloads/'.$filename, $view->render());
        echo $filename;
    }

    // Journal

    public function indexReceiptJournal(Request $request)
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
        $data['bookyears'] = BookYear::orderByDesc('id')->get();
        return view('finance::reports.receipts.journal', $data);
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return JSON
     */
    public function dataReceiptJournal(Request $request)
    {
        $category = ReceiptCategory::find(ReceiptType::find($request->receipt_type_id)->category_id);
        $request->merge([
            'category_id' => $category->id,
        ]);
        if ($category->code == 'JTT' || $category->code == 'CSWJB')
        {
            return response()->json($this->receiptMajorEloquent->dataReceiptJournal($request));
        } elseif ($category->code == 'SKR' || $category->code == 'CSSKR') {
            return response()->json($this->receiptVoluntaryEloquent->dataReceiptJournal($request));
        } else {
            return response()->json($this->receiptOtherEloquent->dataReceiptJournal($request));
        }
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfReceiptJournal(Request $request)
    {
        $payload = json_decode($request->data);
        $data['requests'] = $payload;
        $data['profile'] = $this->getInstituteProfile();
        // 
        $view = View::make('finance::reports.receipts.journal_pdf', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_jurnal';
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
    public function toExcelReceiptJournal(Request $request)
    {
        $data['profile'] = $this->getInstituteProfile();
        $data['payloads'] = json_decode($request->data);
        // 
        $view = View::make('finance::reports.receipts.journal_xlsx', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_journal';
        $filename = date('Ymdhis') . '_' . $name . '.xlsx';
        //
        Storage::disk('local')->put('public/downloads/'.$filename, $view->render());
        echo $filename;
    }
}
