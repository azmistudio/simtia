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
use Modules\Finance\Repositories\Journal\JournalEloquent;   
use Modules\Finance\Repositories\Expenditure\ExpenditureEloquent;
use Modules\Finance\Repositories\Reference\CodeEloquent; 
use Modules\Finance\Repositories\Reference\BookYearEloquent; 
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use View;
use Exception;

class ReportExpenseController extends Controller
{
    
    use DepartmentTrait;
    use ReferenceTrait;
    use HelperTrait;
    use PdfTrait;

    private $subject = 'Laporan Pengeluaran';

    function __construct(
        JournalEloquent $journalEloquent, 
        ExpenditureEloquent $expenditureEloquent, 
        CodeEloquent $codeEloquent,
        BookYearEloquent $bookYearEloquent,
    )
    {
        $this->journalEloquent = $journalEloquent;
        $this->expenditureEloquent = $expenditureEloquent;
        $this->codeEloquent = $codeEloquent;
        $this->bookYearEloquent = $bookYearEloquent;
    }

    // transaction

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexExpenseTrans(Request $request)
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
        return view('finance::reports.expenses.transaction', $data);
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return JSON
     */
    public function dataExpenseTrans(Request $request)
    {
        return response()->json($this->expenditureEloquent->dataTransaction($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfExpenseTrans(Request $request)
    {
        $payload = json_decode($request->data);
        $data['requests'] = $payload;
        $data['profile'] = $this->getInstituteProfile();
        // 
        $view = View::make('finance::reports.expenses.transaction_pdf', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_transaksi';
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
    public function toExcelExpenseTrans(Request $request)
    {
        $data['profile'] = $this->getInstituteProfile();
        $data['payloads'] = json_decode($request->data);
        // 
        $view = View::make('finance::reports.expenses.transaction_xlsx', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_transaksi';
        $filename = date('Ymdhis') . '_' . $name . '.xlsx';
        //
        Storage::disk('local')->put('public/downloads/'.$filename, $view->render());
        echo $filename;
    }

    // journal

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexExpenseJournal(Request $request)
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
        return view('finance::reports.expenses.journal', $data);
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return JSON
     */
    public function dataExpenseJournal(Request $request)
    {
        return response()->json($this->expenditureEloquent->dataReceiptJournal($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfExpenseJournal(Request $request)
    {
        $payload = json_decode($request->data);
        $data['requests'] = $payload;
        $data['profile'] = $this->getInstituteProfile();
        // 
        $view = View::make('finance::reports.expenses.journal_pdf', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_journal';
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
    public function toExcelExpenseJournal(Request $request)
    {
        $data['profile'] = $this->getInstituteProfile();
        $data['payloads'] = json_decode($request->data);
        // 
        $view = View::make('finance::reports.expenses.journal_xlsx', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_journal';
        $filename = date('Ymdhis') . '_' . $name . '.xlsx';
        //
        Storage::disk('local')->put('public/downloads/'.$filename, $view->render());
        echo $filename;
    }
}
