<?php

namespace Modules\Finance\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Traits\DepartmentTrait;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\PdfTrait;
use Modules\Finance\Entities\PaymentMajor;
use Modules\Finance\Entities\ReceiptMajor;
use Modules\Finance\Entities\ReceiptVoluntary;
use Modules\Finance\Entities\ReceiptOther;
use Modules\Finance\Entities\BookYear;
use Modules\Finance\Entities\ReceiptType;
use Modules\Finance\Entities\Journal;
use Modules\Finance\Entities\JournalDetail;
use Modules\Finance\Entities\JournalVoucher;
use Modules\Academic\Entities\Students;
use Modules\Academic\Entities\AdmissionProspect;
use Modules\Finance\Repositories\Journal\JournalEloquent;
use Modules\Finance\Repositories\Journal\JournalVoucherEloquent;
use Modules\Finance\Repositories\Receipt\ReceiptTypeEloquent;
use Modules\Finance\Repositories\Receipt\ReceiptMajorEloquent;
use Modules\Finance\Repositories\Receipt\ReceiptVoluntaryEloquent;
use Modules\Finance\Repositories\Receipt\ReceiptOtherEloquent;
use Modules\Finance\Repositories\Receipt\PaymentMajorEloquent;
use Modules\Finance\Repositories\Reference\CodeEloquent;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use View;
use Exception;

class JournalController extends Controller
{
    use DepartmentTrait;
    use HelperTrait;
    use PdfTrait;

    private $subject = 'Data Transaksi Jurnal Umum';

    function __construct(
        JournalEloquent $journalEloquent, 
        JournalVoucherEloquent $journalVoucherEloquent, 
        ReceiptTypeEloquent $receiptTypeEloquent, 
        ReceiptMajorEloquent $receiptMajorEloquent, 
        ReceiptVoluntaryEloquent $receiptVoluntaryEloquent, 
        ReceiptOtherEloquent $receiptOtherEloquent, 
        PaymentMajorEloquent $paymentMajorEloquent, 
        CodeEloquent $codeEloquent
    )
    {
        $this->journalEloquent = $journalEloquent;
        $this->journalVoucherEloquent = $journalVoucherEloquent;
        $this->receiptTypeEloquent = $receiptTypeEloquent;
        $this->receiptMajorEloquent = $receiptMajorEloquent;
        $this->receiptVoluntaryEloquent = $receiptVoluntaryEloquent;
        $this->receiptOtherEloquent = $receiptOtherEloquent;
        $this->paymentMajorEloquent = $paymentMajorEloquent;
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
        $data['departments'] = $this->allDepartment();
        $data['bookyear'] = $this->getActiveBookYear();
        if ($data['bookyear']->id > 0)
        {
            return view('finance::pages.journals.journal', $data);
        } else {
            $data['error'] = 'Belum ada <b>Tahun Buku</b> yang dibuat, gunakan menu<br/> Data Master &#8594; Tahun Buku untuk membuat baru.';
            return view('errors.400', $data);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|int',
            'trans_date' => 'required',
            'purpose' => 'required',
        ]);
        $bookyear = BookYear::where('is_active', 1)->first();
        $request->merge([
            'trans_date' => $this->formatDate($request->trans_date,'sys'),
            'logged' => auth()->user()->email,
        ]);
        try 
        {
            if ($request->id < 1) 
            {
                $isValid = true;
                $isContinue = true;
                $totalDebit = 0;
                $totalCredit = 0;
                // check debit-credit
                for ($i=0; $i < count($request->rows); $i++)
                {
                    $debit = isset($request->rows[$i]['debit']) ? $request->rows[$i]['debit'] : 0;
                    $credit = isset($request->rows[$i]['credit']) ? $request->rows[$i]['credit'] : 0;
                    $totalDebit += $debit;
                    $totalCredit += $credit;
                    if ($debit > 0 && $credit > 0) 
                    {
                        $isContinue = false;
                        $response = $this->getResponse('warning', 'Silahkan isi salah satu nilai Debit atau Kredit pada akun ' . $request->rows[$i]['code'] .' | '. $request->rows[$i]['name']);
                    }
                }
                if ($isContinue)
                {
                    if ($totalDebit == 0 && $totalCredit == 0)
                    {
                        $isValid = false;
                        $response = $this->getResponse('warning', 'Total Debit dan Total Kredit tidak boleh 0.');
                    }
                    if ($totalDebit <> $totalCredit)
                    {
                        $isValid = false;
                        $response = $this->getResponse('warning', 'Total Debit dan Total Kredit tidak sama.');
                    } 
                }
                if ($isContinue && $isValid)
                {
                    // create cash no
                    $number = $bookyear->number;
                    $number += 1;
                    $cash_no = sprintf('%06d', $number);
                    // transaction
                    DB::transaction(function () use ($request, $cash_no, $bookyear) {
                        $uuid = strtotime('now');
                        // store to journal
                        $journal = $this->journalEloquent->store($request->trans_date, 'Jurnal Umum untuk '. $request->purpose, $cash_no, $bookyear->id, 'journalvoucher', $request->department_id);
                        for ($i=0; $i < count($request->rows); $i++)
                        {
                            $debit = isset($request->rows[$i]['debit']) ? $request->rows[$i]['debit'] : 0;
                            $credit = isset($request->rows[$i]['credit']) ? $request->rows[$i]['credit'] : 0;
                            // store journal detail
                            $this->journalEloquent->createDetail($journal->id, $request->rows[$i]['id'], $debit, $credit, $uuid);
                        }
                        // store journal voucher
                        $request->merge([
                            'journal_id' => $journal->id,
                        ]);
                        $this->journalVoucherEloquent->create($request, $this->subject);
                        // increment number in bookyear                  
                        BookYear::where('id', $bookyear->id)->increment('number');
                    });
                    $response = $this->getResponse('store', '', $this->subject);
                } 
            } else {
                $isValid = true;
                $isContinue = true;
                $totalDebit = 0;
                $totalCredit = 0;
                if (empty($request->reason))
                {
                    throw new Exception('Alasan Ubah Data wajib diisi.', 1);
                } else {
                    // check debit-credit
                    for ($i=0; $i < count($request->rows); $i++)
                    {
                        $debit = isset($request->rows[$i]['debit']) ? $request->rows[$i]['debit'] : 0;
                        $credit = isset($request->rows[$i]['credit']) ? $request->rows[$i]['credit'] : 0;
                        $totalDebit += $debit;
                        $totalCredit += $credit;
                        if ($debit > 0 && $credit > 0) 
                        {
                            $isContinue = false;
                            $response = $this->getResponse('warning', 'Silahkan isi salah satu nilai Debit atau Kredit pada akun ' . $request->rows[$i]['code'] .' | '. $request->rows[$i]['name']);
                        } 
                    }
                    if ($isContinue)
                    {
                        if ($totalDebit == 0 && $totalCredit == 0)
                        {
                            $isValid = false;
                            $response = $this->getResponse('warning', 'Total Debit dan Total Kredit tidak boleh 0.');
                        }
                        if ($totalDebit <> $totalCredit)
                        {
                            $isValid = false;
                            $response = $this->getResponse('warning', 'Total Debit dan Total Kredit tidak sama.');
                        } 
                    }
                    if ($isContinue && $isValid)
                    {
                        // existing journal
                        $journal = Journal::find($request->journal_id);
                        DB::transaction(function () use ($request, $journal, $bookyear) {
                            $uuid = strtotime('now');
                            // audits
                            $audit_id = DB::table('finance.audits')->insertGetId([
                                'department_id' => $request->department_id,
                                'bookyear_id' => $bookyear->id,
                                'source' => $journal->source,
                                'source_id' => $journal->id,
                                'audit_date' => date('Y-m-d H:i:s'),
                                'employee' => auth()->user()->name,
                                'remark' => $request->reason,
                                'logged' => auth()->user()->email,
                                'created_at' => date('Y-m-d H:i:s')
                            ]);
                            // audit journal
                            DB::table('finance.audit_journals')->insert([
                                'audit_id' => $audit_id,
                                'is_status' => 0,
                                'journal_date' => $journal->journal_date,
                                'transaction' => 'Jurnal Umum untuk '. $request->purpose,
                                'cash_no' => $journal->cash_no,
                                'employee' => auth()->user()->name,
                                'remark' => $request->reason,
                                'logged' => auth()->user()->email,
                                'created_at' => date('Y-m-d H:i:s')
                            ]);
                            // audit journal detail
                            $journal_details = JournalDetail::where('journal_id', $request->journal_id)->get();
                            foreach ($journal_details as $val) 
                            {
                                DB::table('finance.audit_journal_details')->insert([
                                    'audit_id' => $audit_id,
                                    'is_status' => 0,
                                    'journal_id' => $journal->id,
                                    'account_id' => $val->account_id,
                                    'debit' => $val->debit,
                                    'credit' => $val->credit,
                                    'employee' => auth()->user()->name,
                                    'remark' => $request->reason,
                                    'logged' => auth()->user()->email,
                                    'created_at' => date('Y-m-d H:i:s')
                                ]);
                            }
                            // update journal
                            Journal::where('id', $request->journal_id)->update([
                                'journal_date' => $request->trans_date,
                                'transaction' => 'Jurnal Umum untuk '. $request->purpose,
                                'bookyear_id' => $bookyear->id,
                                'employee_id' => auth()->user()->id,
                                'remark' => $request->remark,
                                'logged' => auth()->user()->email,
                            ]);
                            // audit journal success
                            DB::table('finance.audit_journals')->insert([
                                'audit_id' => $audit_id,
                                'is_status' => 1,
                                'journal_date' => $request->trans_date,
                                'transaction' => 'Jurnal Umum untuk '. $request->purpose,
                                'cash_no' => $journal->cash_no,
                                'employee' => auth()->user()->name,
                                'remark' => $request->reason,
                                'logged' => auth()->user()->email,
                                'created_at' => date('Y-m-d H:i:s')
                            ]);
                            // journal detail
                            JournalDetail::where('journal_id', $journal->id)->delete();
                            for ($i=0; $i < count($request->rows); $i++)
                            {
                                $debit = isset($request->rows[$i]['debit']) ? $request->rows[$i]['debit'] : 0;
                                $credit = isset($request->rows[$i]['credit']) ? $request->rows[$i]['credit'] : 0;
                                // store journal detail
                                $this->journalEloquent->createDetail($journal->id, $request->rows[$i]['id'], $debit, $credit, $uuid);
                                // audit journal detail
                                DB::table('finance.audit_journal_details')->insert([
                                    'audit_id' => $audit_id,
                                    'is_status' => 1,
                                    'journal_id' => $journal->id,
                                    'account_id' => $request->rows[$i]['id'],
                                    'debit' => $debit,
                                    'credit' => $credit,
                                    'employee' => auth()->user()->name,
                                    'remark' => $request->reason,
                                    'logged' => auth()->user()->email,
                                    'created_at' => date('Y-m-d H:i:s')
                                ]);
                            }
                            // update journal voucher
                            $request->merge([
                                'journal_id' => $journal->id,
                            ]);
                            $this->journalVoucherEloquent->update($request, $this->subject);
                        });
                        $response = $this->getResponse('store', '', $this->subject);
                    }
                }
            }
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Kode Akun');
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
        return response()->json(JournalVoucher::find($id));
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return JSON
     */
    public function data(Request $request)
    {
        return response()->json($this->journalVoucherEloquent->data($request));
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return JSON
     */
    public function dataDetail(Request $request)
    {
        return response()->json($this->journalEloquent->dataDetail($request));
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return JSON
     */
    public function totalDetail(Request $request)
    {
        return response()->json($this->journalEloquent->totalDetail($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdf(Request $request)
    {
        $idArray = collect(json_decode($request->data)->rows)->pluck('journal_id')->toArray();
        $data['requests'] = json_decode($request->data);
        $data['profile'] = $this->getInstituteProfile();
        $data['departments'] = $this->getDepartments($idArray);
        $data['journals'] = $this->getJournals($idArray);
        $data['journal_details'] = $this->getJournalDetails($idArray);
        // 
        $view = View::make('finance::pages.journals.journals_pdf', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake();
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
    public function toExcel(Request $request)
    {
        $idArray = collect(json_decode($request->data)->rows)->pluck('journal_id')->toArray();
        $data['requests'] = json_decode($request->data);
        $data['profile'] = $this->getInstituteProfile();
        $data['departments'] = $this->getDepartments($idArray);
        $data['journals'] = $this->getJournals($idArray);
        $data['journal_details'] = $this->getJournalDetails($idArray);
        // 
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake();
        $filename = date('Ymdhis') . '_' . $name . '.xlsx';
        $view = View::make('finance::pages.journals.journals_xlsx', $data);
        Storage::disk('local')->put('public/downloads/'.$filename, $view->render());
        echo $filename;
    }

    private function getDepartments($idArray)
    {
        return Journal::select(
                        'finance.journal_vouchers.department_id',
                        DB::raw('UPPER(public.departments.name) as department'),
                        'finance.book_years.book_year',
                    )
                    ->join('finance.book_years','finance.book_years.id','=','finance.journals.bookyear_id')
                    ->join('finance.journal_vouchers','finance.journal_vouchers.journal_id','=','finance.journals.id')
                    ->join('public.departments','public.departments.id','=','finance.journal_vouchers.department_id')
                    ->whereIn('finance.journals.id', $idArray)
                    ->groupBy('finance.journal_vouchers.department_id','public.departments.name','finance.book_years.book_year')
                    ->get();
    }

    private function getJournals($idArray)
    {
        return Journal::select(
                    'finance.journals.id',
                    'finance.journal_vouchers.department_id',
                    'finance.journals.journal_date',
                    'finance.journals.transaction',
                    'finance.journals.cash_no',
                    'finance.journals.bookyear_id',
                    DB::raw('INITCAP(public.employees.name) as employee'),
                    DB::raw("
                        CASE 
                            WHEN finance.journals.source = 'major_jtt' THEN 'Besar Pembayaran Wajib Santri'
                            WHEN finance.journals.source = 'major_jtt_prospect' THEN 'Besar Pembayaran Wajib Calon Santri'
                            WHEN finance.journals.source = 'receipt_jtt' THEN 'Penerimaan Iuran Wajib Santri'
                            WHEN finance.journals.source = 'receipt_jtt_prospect' THEN 'Penerimaan Iuran Wajib Calon Santri'
                            WHEN finance.journals.source = 'receipt_skr' THEN 'Penerimaan Iuran Sukarela Santri'
                            WHEN finance.journals.source = 'receipt_skr_prospect' THEN 'Penerimaan Iuran Sukarela Calon Santri'
                            WHEN finance.journals.source = 'receipt_other' THEN 'Penerimaan Lain-Lain'
                            WHEN finance.journals.source = 'expense' THEN 'Pengeluaran'
                            WHEN finance.journals.source = 'savingdeposit' THEN 'Pengeluaran'
                            WHEN finance.journals.source = 'savingwithdrawal' THEN 'Pengeluaran'
                            WHEN finance.journals.source = 'journalvoucher' THEN 'Jurnal Umum'
                        END source_name
                    ")
                )
                ->join('finance.journal_vouchers','finance.journal_vouchers.journal_id','=','finance.journals.id')
                ->join('finance.book_years','finance.book_years.id','=','finance.journals.bookyear_id')
                ->join('public.employees','public.employees.id','=','finance.journals.employee_id')
                ->whereIn('finance.journals.id', $idArray)
                ->orderBy('finance.journals.id')
                ->get()->map(function($model){
                    $model['date_journal'] = $this->formatDate($model->journal_date,'iso');
                    $model['cash_no'] = $this->getPrefixBookYear($model->bookyear_id) . $model->cash_no;
                    return $model;
                });
    }

    private function getJournalDetails($idArray)
    {
        return JournalDetail::select(
                        'finance.codes.code',
                        DB::raw('finance.codes.name as account_name'),
                        'finance.journal_details.journal_id',
                        'finance.journal_details.debit',
                        'finance.journal_details.credit',
                    )
                    ->join('finance.codes','finance.codes.id','=','finance.journal_details.account_id')
                    ->whereIn('finance.journal_details.journal_id', $idArray)
                    ->get();
    }
}
