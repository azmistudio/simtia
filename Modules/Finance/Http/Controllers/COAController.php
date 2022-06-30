<?php

namespace Modules\Finance\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\PdfTrait;
use App\Http\Traits\ReferenceTrait;
use Modules\Finance\Entities\Code;
use Modules\Finance\Entities\CodeCategory;
use Modules\Finance\Entities\BookYear;
use Modules\Finance\Entities\BeginBalance;
use Modules\Finance\Entities\Journal;
use Modules\Finance\Entities\JournalDetail;
use Modules\Finance\Repositories\Reference\CodeEloquent;
use Modules\Finance\Repositories\Reference\BookYearEloquent;
use Modules\Finance\Repositories\Journal\JournalEloquent;
use Modules\Finance\Http\Requests\CodeRequest;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;
use View;
use Exception;

class COAController extends Controller
{
    use AuditLogTrait;
    use HelperTrait;
    use PdfTrait;
    use ReferenceTrait;

    private $subject = 'Data Kode Akun Perkiraan';
    private $subject_balance = 'Data Saldo Awal Akun';

    function __construct(
        CodeEloquent $codeEloquent,
        BookYearEloquent $bookYearEloquent,
        JournalEloquent $journalEloquent,
    )
    {
        $this->codeEloquent = $codeEloquent;
        $this->bookYearEloquent = $bookYearEloquent;
        $this->journalEloquent = $journalEloquent;
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
        $data['categories'] = CodeCategory::select('id','category')->orderBy('order')->get();
        $data['parents'] = Code::select('id','code','name')->orderBy('code')->get();
        return view('finance::pages.references.account_code', $data);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexBalance(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $data['bookyear'] = $this->getActiveBookYear();
        $data['period'] = $this->formatDate($data['bookyear']->start_date,'local') .' - '. $this->formatDate($data['bookyear']->end_date,'local');
        $data['balance_date'] = date('Y-m-d',(strtotime ('-1 day', strtotime($data['bookyear']->start_date))));
        return view('finance::pages.references.account_code_balance', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(CodeRequest $request)
    {
        $validated = $request->validated();
        try 
        {
            $request->merge([
                'code' => $request->category_id .'-'. $this->formatCode($request->code),
                'balance' => 0,
                'logged' => auth()->user()->email,
            ]);
            if ($request->id < 1) 
            {
                $this->codeEloquent->create($request, $this->subject);
            } else {
                $code = Code::find($request->id);
                if ($code->locked == 1)
                {
                    $request->merge([
                        'category_id' => $code->category_id,
                        'code' => $code->code,
                        'parent' => $code->parent,
                    ]);
                }
                $this->codeEloquent->update($request, $this->subject);
            }
            $response = $this->getResponse('store', '', $this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Kode Akun');
        }
        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function storeBalance(Request $request)
    {
        $validated = $request->validate([
            'bookyear_id'   => 'required|int|gt:0',
            'start_date'    => 'required',
        ]);
        try {
            $request->merge([
                'start_date'=> $this->formatDate($request->start_date,'sys'),
                'logged'    => auth()->user()->email,
            ]);

            $bookyear = BookYear::find($request->bookyear_id);

            // check is transaction running
            if (BookYear::count() > 1)
            {
                throw new Exception('Saldo Awal Tahun Buku ' . $bookyear->book_year . ' tidak dapat diubah.', 1);
            }

            // new balance
            $totalDebit  = 0;
            $totalCredit = 0;
            $rows = array();
            for ($i=0; $i < count($request->activa); $i++) 
            { 
                $totalDebit += $request->activa[$i]['total'];
                $rows[] = $request->activa[$i];
            }
            for ($i=0; $i < count($request->passiva); $i++) 
            { 
                if ($request->passiva[$i]['code'] != '3-199')
                {
                    $totalCredit += $request->passiva[$i]['total'];
                }
                $rows[] = $request->passiva[$i];
            }
            $equity_balance = 0;
            $pos = 'K';
            if ($totalDebit <> $totalCredit)
            {
                $balance = $totalDebit - $totalCredit;
                if ($balance < 0)
                {
                    $pos = 'D';
                } 
                $equity_balance = $balance;
            }

            DB::transaction(function () use ($request, $rows, $equity_balance, $pos, $bookyear) {
                $uuid   = strtotime('now');
                $number = 1;
                $cash_no= sprintf('%06d', $number);

                $journal_exist = Journal::where('bookyear_id', $bookyear->id)->where('source','begin_balance')->first();
                if (empty($journal_exist))
                {
                    // store to journal
                    $journal = $this->journalEloquent->store($request->start_date, 'Saldo awal tahun buku '. $bookyear->book_year, $cash_no, $request->bookyear_id, 'begin_balance', 1);
                    $this->setBeginBalance($request, $rows, $journal->id, $equity_balance, $pos, $uuid, false);
                } else {
                    // update journal      
                    $requestJournal = new Request();
                    $requestJournal->merge([
                        'id'        => $journal_exist->id,
                        'remark'    => 'Ubah saldo awal',
                        'logged'    => $request->logged,
                        'updated_at'=> date('Y-m-d H:i:s')
                    ]);         
                    $this->journalEloquent->update($requestJournal, 'Saldo Awal');
                    $this->setBeginBalance($request, $rows, $journal_exist->id, $equity_balance, $pos, $uuid, true);
                }

                // update equity
                BeginBalance::where('account_id', 34)
                    ->update([
                        'total' => $equity_balance, 
                        'pos' => $pos
                    ]);

                // update bookyear number
                BookYear::where('id', $bookyear->id)->increment('number');

                // log
                $this->logTransaction('#', 'Pengaturan ' . $this->subject_balance, '{}', '{}');
            });
            $response = $this->getResponse('store', '', $this->subject_balance);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), $this->subject_balance);
        }
        return response()->json($response);
    }

    private function setBeginBalance($request, $rows, $journal_id, $equity_balance, $pos, $uuid, $is_update)
    {
        if ($is_update)
        {
            JournalDetail::where('journal_id', $journal_id)->delete();
        }

        for ($i=0; $i < count($rows); $i++) 
        { 
            BeginBalance::upsert([
                'bookyear_id'   => $request->bookyear_id,
                'trans_date'    => $request->start_date,
                'account_id'    => $rows[$i]['id'],
                'total'         => $rows[$i]['total'],
                'pos'           => $this->setPos($rows[$i]['code'], $rows[$i]['total']),
                'logged'        => $request->logged,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],['bookyear_id','trans_date','account_id']);

            // store journal detail
            if ($rows[$i]['total'] > 0)
            {
                if ($rows[$i]['code'] != '3-199')
                {
                    if ($this->setPos($rows[$i]['code'], $rows[$i]['total']) == 'D')
                    {
                        $this->journalEloquent->createDetail($journal_id, $rows[$i]['id'], $rows[$i]['total'], 0, $uuid);
                    } else {
                        $this->journalEloquent->createDetail($journal_id, $rows[$i]['id'], 0, $rows[$i]['total'], $uuid);
                    }
                } 
            } 
        }
        
        if ($equity_balance != 0)
        {
            if ($pos == 'D')
            {
                $this->journalEloquent->createDetail($journal_id, 34, $equity_balance, 0, $uuid);
            } else {
                $this->journalEloquent->createDetail($journal_id, 34, 0, $equity_balance, $uuid);
            }
        }
    }

    private function setPos($code, $total)
    {
        if (Str::contains($code, '1-') || Str::contains($code, '5-'))
        {
            if ($total > 0) 
            {
                $accountPos = 'D';
            } else {
                $accountPos = 'K';
            }
        } else {
            if ($total > 0) 
            {
                $accountPos = 'K';
            } else {
                $accountPos = 'D';
            }
        }
        return $accountPos;
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return response()->json(Code::find($id));
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return Renderable
     */
    public function data(Request $request)
    {
        return response()->json($this->codeEloquent->data($request));
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return Renderable
     */
    public function dataGrid(Request $request)
    {
        return response()->json($this->codeEloquent->dataGrid($request));
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
            $code = Code::find($id);
            if ($code->locked == 1)
            {
                $response = $this->getResponse('warning', 'Akun ' . $code->code .' | '. $code->name . ' tidak dapat dihapus.');
            } else {
                $this->codeEloquent->destroy($id, $this->subject);
                $response = $this->getResponse('destroy', '', $this->subject);
            }
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), $this->subject);
        }
        return response()->json($response);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function combobox($id)
    {
        return response()->json($this->codeEloquent->combobox($id, '', true));
    }

    /**
     * Export resource to Excel Document.
     * @return Excel
     */
    public function toExcel(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(strval(Str::of($this->subject)->snake()));
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        //
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(50);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(100);
        // 
        $sheet->setCellValue('A1', Str::upper($request->session()->get('institute')));
        $sheet->setCellValue('A2', 'DATA KODE AKUN PERKIRAAN - ' . config('app.name'));
        $sheet->setCellValue('A4', 'NO.');
        $sheet->setCellValue('B4', 'KATEGORI');
        $sheet->setCellValue('C4', 'KODE AKUN');
        $sheet->setCellValue('D4', 'NAMA AKUN');
        $sheet->setCellValue('E4', 'SALDO');
        $sheet->setCellValue('F4', 'KETERANGAN');
        //
        $baris = 5;
        $number = 1;
        $query = Code::orderBy('code')->get();
        foreach ($query as $q) 
        {
            $sheet->setCellValue('A'.$baris,$number);
            $sheet->setCellValue('B'.$baris,$q->getCategory->category);
            $sheet->setCellValue('C'.$baris,$q->code);
            $sheet->setCellValue('D'.$baris,$q->name);
            $sheet->setCellValue('E'.$baris,$q->parent > 0 ? $this->codeEloquent->getBalanceSub($q->id, 0, date('Y-m-d')) : $this->codeEloquent->getBalance($q->id, 0, date('Y-m-d')));
            $sheet->setCellValue('F'.$baris,$q->remark);
            $baris++;
            $number++;
        }
        //
        $sheet->getStyle('A1:A2')->applyFromArray($this->PHPExcelCommonStyle()['title']);
        $sheet->getStyle('A4:F4')->applyFromArray($this->PHPExcelCommonStyle()['header']);
        $sheet->getStyle('A5:A'.$baris = $baris - 1)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('B5:B'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('C5:C'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('D5:D'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('E5:E'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
        $sheet->getStyle('E5:E'.$baris)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
        $sheet->getStyle('F5:F'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        //
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake();
        $filename = date('Ymdhis') . '_' . $name . '.xlsx';
        // 
        $writer = new Xlsx($spreadsheet);
        $writer->save('storage/downloads/'.$filename);
        echo $filename;
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdf(Request $request)
    {
        $data['codes'] = Code::orderBy('code')->get();
        // 
        $view = View::make('finance::pages.references.account_code_pdf', $data);
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
    public function combogrid(Request $request)
    {
        return response()->json($this->codeEloquent->combogrid($request));
    }
}
