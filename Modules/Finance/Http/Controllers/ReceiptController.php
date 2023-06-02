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
use Modules\Academic\Entities\Students;
use Modules\Academic\Entities\AdmissionProspect;
use Modules\Finance\Entities\PaymentMajor;
use Modules\Finance\Entities\ReceiptMajor;
use Modules\Finance\Entities\ReceiptVoluntary;
use Modules\Finance\Entities\ReceiptOther;
use Modules\Finance\Entities\BookYear;
use Modules\Finance\Entities\ReceiptType;
use Modules\Finance\Entities\Journal;
use Modules\Finance\Entities\JournalDetail;
use Modules\Finance\Repositories\Journal\JournalEloquent;
use Modules\Finance\Repositories\Receipt\ReceiptTypeEloquent;
use Modules\Finance\Repositories\Receipt\ReceiptMajorEloquent;
use Modules\Finance\Repositories\Receipt\ReceiptVoluntaryEloquent;
use Modules\Finance\Repositories\Receipt\ReceiptOtherEloquent;
use Modules\Finance\Repositories\Receipt\PaymentMajorEloquent;
use Modules\Finance\Repositories\Reference\CodeEloquent;
use Carbon\Carbon;
use View;
use Exception;

class ReceiptController extends Controller
{
    use DepartmentTrait;
    use ReferenceTrait;
    use HelperTrait;
    use PdfTrait;

    private $subject = 'Data Transaksi Penerimaan';

    function __construct(
        JournalEloquent $journalEloquent,
        ReceiptTypeEloquent $receiptTypeEloquent,
        ReceiptMajorEloquent $receiptMajorEloquent,
        ReceiptVoluntaryEloquent $receiptVoluntaryEloquent,
        ReceiptOtherEloquent $receiptOtherEloquent,
        PaymentMajorEloquent $paymentMajorEloquent,
        CodeEloquent $codeEloquent
    )
    {
        $this->journalEloquent = $journalEloquent;
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
        $data['departments'] = $this->listDepartment();
        $data['bookyear'] = $this->getActiveBookYear();
        if ($data['bookyear']->id > 0)
        {
            return view('finance::pages.receipts.receipt_trans', $data);
        } else {
            $data['error'] = 'Belum ada <b>Tahun Buku</b> yang dibuat, gunakan menu<br/> Data Master &#8594; Tahun Buku untuk membuat baru.';
            return view('errors.400', $data);
        }
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexMandatory(Request $request)
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
        $data['categories'] = DB::table('finance.receipt_categories')->select('id','code','category')->whereIn('code', ['JTT','CSWJB'])->orderBy('order')->get();
        $data['codes_cash'] = $this->codeEloquent->combobox(1, '1-1');
        $data['book_year'] = BookYear::find($request->bookyear_id);
        $data['payload'] = $request->all();
        return view('finance::pages.receipts.receipt_trans_mandatory', $data);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexVoluntary(Request $request)
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
        $data['categories'] = DB::table('finance.receipt_categories')->select('id','code','category')->whereIn('code', ['SKR','CSSKR'])->orderBy('order')->get();
        $data['codes_cash'] = $this->codeEloquent->combobox(1, '1-1');
        $data['book_year'] = BookYear::where('is_active', 1)->first();
        $data['payload'] = $request->all();
        return view('finance::pages.receipts.receipt_trans_voluntary', $data);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexOther(Request $request)
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
        $data['categories'] = DB::table('finance.receipt_categories')->select('id','code','category')->where('code', 'LNN')->orderBy('order')->get();
        $data['codes_cash'] = $this->codeEloquent->combobox(1, '1-1');
        $data['book_year'] = BookYear::where('is_active', 1)->first();
        $data['payload'] = $request->all();
        return view('finance::pages.receipts.receipt_trans_other', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        switch ($request->category_id)
        {
            case 2:
                // SKR
                $validated = $request->validate([
                    'student_id' => 'required|int',
                    'department_id' => 'required|int',
                    'receipt_id' => 'required|int',
                    'bookyear_id' => 'required|int',
                    'is_prospect' => 'required|int',
                    'amount' => 'required|gt:0',
                    'cash_account' => 'required',
                    'journal_date' => 'required'
                ]);
                try {
                    if ($request->id < 1)
                    {
                        // get receipt type
                        $receipt_type = ReceiptType::find($request->receipt_id);
                        // fetch prefix and number from bookyear for cash no.
                        $bookyear = BookYear::find($request->bookyear_id);
                        $number = $bookyear->number;
                        $number += 1;
                        $cash_no = sprintf('%06d', $number);
                        //
                        DB::transaction(function () use ($request, $receipt_type, $cash_no) {
                            $uuid = strtotime('now');
                            $remark_journal = 'Pembayaran ' . $receipt_type->name . ' tanggal ' . date('d/m/Y') . ' santri '. $request->student_name .' ('. $request->student_no .')';
                            // store to journal
                            $journal = $this->journalEloquent->store($this->formatDate($request->journal_date,'sys'), $remark_journal, $cash_no, $request->bookyear_id, 'receipt_skr', $request->department_id);
                            // store journal detail
                            $this->journalEloquent->createDetail($journal->id, $request->cash_account, $request->amount, 0, $uuid);
                            $this->journalEloquent->createDetail($journal->id, $receipt_type->receipt_account, 0, $request->amount, $uuid);
                            // increment number in bookyear
                            BookYear::where('id', $request->bookyear_id)->increment('number');
                            // store receipt voluntary
                            $receiptVoluntaryRequest = new Request();
                            $receiptVoluntaryRequest->merge([
                                'receipt_id' => $receipt_type->id,
                                'journal_id' => $journal->id,
                                'student_id' => $request->student_id,
                                'trans_date' => date('Y-m-d'),
                                'total' => $request->amount,
                                'employee' => auth()->user()->name,
                                'is_prospect' => 0,
                                'bookyear_id' => $request->bookyear_id,
                                'department_id' => $request->department_id,
                                'remark' => $request->remark,
                                'logged' => auth()->user()->email
                            ]);
                            $this->receiptVoluntaryEloquent->create($receiptVoluntaryRequest, $this->subject . ' Iuran Sukarela Santri');
                        });
                        $response = $this->getResponse('store', '', $this->subject .' Iuran Sukarela Santri');
                    } else {
                        if (empty($request->reason))
                        {
                            $response = $this->getResponse('warning', 'Alasan Ubah Data wajib diisi.');
                        } else {
                            DB::transaction(function () use ($request) {
                                $uuid = strtotime('now');
                                $journal_id = ReceiptVoluntary::find($request->id)->journal_id;
                                // get existing transaction
                                $receipt_voluntary = ReceiptVoluntary::find($request->id);
                                $receipt_type = ReceiptType::where('category_id', $request->category_id)->where('department_id', $request->department_id)->first();
                                if ($request->amount == $receipt_voluntary->amount)
                                {
                                    // only update info
                                    $receiptVoluntaryRequest = new Request();
                                    $receiptVoluntaryRequest->merge([
                                        'id' => $receipt_voluntary->id,
                                        'trans_date' => $this->formatDate($request->journal_date,'sys'),
                                        'remark' => $request->remark,
                                        'reason' => $request->reason,
                                        'employee' => auth()->user()->name,
                                        'logged' => auth()->user()->email
                                    ]);
                                    $this->receiptVoluntaryEloquent->update($receiptVoluntaryRequest, $this->subject .' Iuran Sukarela Santri');
                                    // update journal
                                    Journal::where('id', $journal_id)->update([
                                            'journal_date' => $this->formatDate($request->journal_date,'sys'),
                                            'remark' => $request->reason,
                                            'logged' => auth()->user()->email
                                        ]);
                                    // get coa from journal
                                    $account_id = $this->journalEloquent->getAccount($receipt_voluntary->journal_id, 'KAS', $receipt_type->id);
                                    if ($account_id != $request->cash_account)
                                    {
                                        // update journal detail
                                        JournalDetail::where('journal_id', $receipt_voluntary->journal_id)
                                            ->where('account_id', $account_id)
                                            ->where('credit',0)
                                            ->update([
                                                'account_id' => $request->cash_account,
                                                'uuid' => $uuid,
                                                'logged' => auth()->user()->email
                                            ]);
                                    }
                                } else {
                                    // update total
                                    $receiptVoluntaryRequest = new Request();
                                    $receiptVoluntaryRequest->merge([
                                        'id' => $request->id,
                                        'trans_date' => $this->formatDate($request->journal_date,'sys'),
                                        'total' => $request->amount,
                                        'remark' => $request->remark,
                                        'reason' => $request->reason,
                                        'employee' => auth()->user()->name,
                                        'logged' => auth()->user()->email
                                    ]);
                                    $this->receiptVoluntaryEloquent->update($receiptVoluntaryRequest, $this->subject .' Iuran Sukarela Santri');
                                    // get accounts
                                    $cash_account_id = $this->journalEloquent->getAccount($receipt_voluntary->journal_id, 'KAS', $receipt_type->id);
                                    $receipt_account_id = $this->journalEloquent->getAccount($receipt_voluntary->journal_id, 'PENDAPATAN', $receipt_type->id);
                                    // update journal
                                    Journal::where('id', $journal_id)->update([
                                            'journal_date' => $this->formatDate($request->journal_date,'sys'),
                                            'remark' => $request->reason,
                                            'logged' => auth()->user()->email
                                        ]);
                                    // update journal detail
                                    JournalDetail::where('journal_id', $receipt_voluntary->journal_id)
                                        ->where('account_id', $cash_account_id)
                                        ->where('credit',0)
                                        ->update([
                                            'debit' => $request->amount,
                                            'uuid' => $uuid,
                                            'logged' => auth()->user()->email
                                        ]);
                                    //
                                    if ($cash_account_id != $request->cash_account)
                                    {
                                        // update journal detail
                                        JournalDetail::where('journal_id', $receipt_voluntary->journal_id)
                                            ->where('account_id', $cash_account_id)
                                            ->where('credit',0)
                                            ->update([
                                                'account_id' => $request->cash_account,
                                                'uuid' => $uuid,
                                                'logged' => auth()->user()->email
                                            ]);
                                    }
                                    JournalDetail::where('journal_id', $receipt_voluntary->journal_id)
                                        ->where('account_id', $receipt_account_id)
                                        ->where('debit',0)
                                        ->update([
                                            'credit' => $request->amount,
                                            'uuid' => $uuid,
                                            'logged' => auth()->user()->email
                                        ]);
                                }
                            });
                            $response = $this->getResponse('store', '', $this->subject .' Iuran Sukarela Santri');
                        }
                    }
                } catch (\Throwable $e) {
                    $response = $this->getResponse('error', $e->getMessage(), $this->subject .' Iuran Sukarela Santri');
                }
                break;
            case 3:
                // CSWJB
                $validated = $request->validate([
                    'student_id' => 'required|int',
                    'department_id' => 'required|int',
                    'receipt_id' => 'required|int',
                    'bookyear_id' => 'required|int',
                    'is_prospect' => 'required|int',
                    'instalment' => 'required|gt:0',
                    'discount' => 'required|gt:-1',
                    'cash_account' => 'required',
                    'journal_date' => 'required'
                ]);
                try
                {
                    if ($request->id < 1)
                    {
                        // get receipt type
                        $receipt_type = ReceiptType::find($request->receipt_id);
                        // find pay amount
                        $payment_majors = PaymentMajor::where('prospect_student_id', $request->student_id)
                                            ->where('receipt_id', $request->receipt_id)
                                            ->where('bookyear_id', $request->bookyear_id)
                                            ->first();
                        // check is_paid
                        if ($payment_majors->is_paid == 1)
                        {
                            throw new Exception('Pembayaran ' .$receipt_type->name. ' santri '  . $request->student_name . ' (' . $request->student_no . ') periode '. $payment_majors->period_year .' sudah lunas.', 1);
                        }
                        // get instalment and discount
                        $total_receipt = ReceiptMajor::select(DB::raw('SUM(total) AS total'), DB::raw('SUM(discount_amount) AS instalment'))->where('major_id', $payment_majors->id)->first();
                        // check instalment amount with payment major must have paid
                        $remark_journal = '';
                        $is_paid = 0;
                        if (($total_receipt->total + $total_receipt->instalment + ($request->instalment - $request->discount) + $request->discount) > $payment_majors->amount)
                        {
                            throw new Exception('Maaf, pembayaran tidak dapat dilakukan! Jumlah bayaran cicilan lebih besar daripada pembayaran yang harus dilunasi.', 1);
                        } else {
                            $is_paid = 0;
                            $remark_journal = '';
                            if (($total_receipt->total + $total_receipt->instalment + ($request->instalment - $request->discount) + $request->discount) == $payment_majors->amount)
                            {
                                $remark_journal = 'Pelunasan '. $receipt_type->name .' calon santri '. $request->student_name .' ('. $request->student_no .') periode '. $payment_majors->period_year;
                                $is_paid = 1;
                            } else {
                                $instalment_count = ReceiptMajor::where('major_id', $payment_majors->id)->count() + 1;
                                $remark_journal = 'Pembayaran ke-'. $instalment_count . ' ' . $receipt_type->name . ' calon santri '. $request->student_name .' ('. $request->student_no .') periode '. $payment_majors->period_year;
                                $is_paid = 0;
                            }
                            // fetch prefix and number from bookyear for cash no.
                            $bookyear = BookYear::find($request->bookyear_id);
                            $number = $bookyear->number;
                            $number += 1;
                            $cash_no = sprintf('%06d', $number);
                            // db transaction
                            DB::transaction(function () use ($request, $remark_journal, $receipt_type, $cash_no, $payment_majors, $is_paid) {
                                $uuid = strtotime('now');
                                // store to journal
                                $journal = $this->journalEloquent->store($this->formatDate($request->journal_date,'sys'), $remark_journal, $cash_no, $request->bookyear_id, 'receipt_jtt_prospect', $request->department_id);
                                // store journal detail
                                $this->journalEloquent->createDetail($journal->id, $request->cash_account, $request->instalment - $request->discount, 0, $uuid);
                                $this->journalEloquent->createDetail($journal->id, $receipt_type->receivable_account, 0, $request->instalment, $uuid);
                                if ($request->discount > 0)
                                {
                                    $this->journalEloquent->createDetail($journal->id, $receipt_type->discount_account, $request->discount, 0, $uuid);
                                }
                                // increment number in bookyear
                                BookYear::where('id', $request->bookyear_id)->increment('number');
                                // store to receipt major
                                $receiptMajorRequest = new Request();
                                $receiptMajorRequest->merge([
                                    'trans_date' => $this->formatDate($request->journal_date,'sys'),
                                    'major_id' => $payment_majors->id,
                                    'journal_id' => $journal->id,
                                    'total' => $request->instalment - $request->discount,
                                    'employee_id' => auth()->user()->id,
                                    'first_instalment' => 1,
                                    'is_prospect' => 1,
                                    'discount_amount' => $request->discount,
                                    'logged' => auth()->user()->email,
                                ]);
                                $this->receiptMajorEloquent->create($receiptMajorRequest, $this->subject .' Iuran Wajib Calon Santri');
                                // update is_paid
                                if ($is_paid > 0)
                                {
                                    $paymentMajorRequest = new Request();
                                    $paymentMajorRequest->merge([
                                        'id' => $payment_majors->id,
                                        'is_paid' => 1,
                                        'logged' => auth()->user()->email,
                                    ]);
                                    $this->paymentMajorEloquent->update($paymentMajorRequest, $this->subject .' Iuran Wajib Calon Santri');
                                }
                            });
                            $response = $this->getResponse('store', '', $this->subject .' Iuran Wajib Calon Santri', $is_paid);
                        }
                    } else {
                        if (empty($request->reason))
                        {
                            throw new Exception('Alasan Ubah Data wajib diisi.', 1);
                        } else {
                            // get receipt type
                            $receipt_type = ReceiptType::find($request->receipt_id);
                            // get old receipt major
                            $receipt_majors = ReceiptMajor::find($request->id);
                            // only update payment info
                            if ($request->instalment == $receipt_majors->total && $request->discount == $receipt_majors->discount_amount)
                            {
                                DB::transaction(function () use ($request, $receipt_type, $receipt_majors) {
                                    $receiptMajorRequest = new Request();
                                    $receiptMajorRequest->merge([
                                        'id' => $request->id,
                                        'trans_date' => $this->formatDate($request->journal_date,'sys'),
                                        'remark' => $request->remark,
                                        'reason' => $request->reason,
                                        'employee_id' => auth()->user()->id,
                                        'logged' => auth()->user()->email
                                    ]);
                                    $this->receiptMajorEloquent->update($receiptMajorRequest, $this->subject .' Iuran Wajib Calon Santri');
                                    // update journal
                                    Journal::where('id', $receipt_majors->journal_id)->update([
                                            'journal_date' => $this->formatDate($request->journal_date,'sys'),
                                            'remark' => $request->reason,
                                            'logged' => auth()->user()->email
                                        ]);
                                    // get COA from journal
                                    $account_id = $this->journalEloquent->getAccount($receipt_majors->journal_id, 'KAS', $request->id);
                                    if ($account_id != $request->cash_account)
                                    {
                                        // update journal detail
                                        JournalDetail::where('journal_id', $receipt_majors->journal_id)
                                            ->where('account_id', $account_id)
                                            ->where('credit',0)
                                            ->update([
                                                'account_id' => $request->cash_account,
                                                'logged' => auth()->user()->email
                                            ]);
                                    }
                                });
                                $response = $this->getResponse('store', '', $this->subject .' Iuran Wajib Calon Santri');
                            } else {
                                $payment_majors = PaymentMajor::find($request->major_id);
                                $total_receipt = ReceiptMajor::select(DB::raw('SUM(total) AS total'), DB::raw('SUM(discount_amount) AS discount'))
                                                    ->where('major_id', $request->major_id)
                                                    ->where('id','<>',$request->id)
                                                    ->first();
                                if (($total_receipt->total + $total_receipt->discount + ($request->instalment - $request->discount) + $request->discount) > $payment_majors->amount)
                                {
                                    $response = $this->getResponse('warning', 'Maaf, pembayaran tidak dapat dilakukan, jumlah pembayaran cicilan lebih besar daripada bayaran yang harus dilunasi.');
                                } else {
                                    $remark_journal = '';
                                    $is_paid = 0;
                                    if (($total_receipt->total + $total_receipt->discount + ($request->instalment - $request->discount) + $request->discount) == $receipt_majors->total)
                                    {
                                        $remark_journal = 'Pelunasan '. $receipt_type->name .' calon santri '. $request->student_name .' ('. $request->student_no .')';
                                        $is_paid = 1;
                                    } else {
                                        $instalment_count = 0;
                                        $receipt_major = ReceiptMajor::where('major_id', $request->major_id)->orderBy('trans_date')->get();
                                        foreach ($receipt_major as $val)
                                        {
                                            $instalment_count++;
                                            if ($val->id == $request->receipt_id)
                                                break;
                                        }
                                        $remark_journal = 'Pembayaran ke-'. $instalment_count . ' ' . $receipt_type->name . ' calon santri '. $request->student_name .' ('. $request->student_no .')';
                                        $is_paid = 0;
                                    }
                                    $cash_account_id = $this->journalEloquent->getAccount($receipt_majors->journal_id, 'KAS', $request->id);
                                    $receivable_account_id = $this->journalEloquent->getAccount($receipt_majors->journal_id, 'PIUTANG', $request->id);
                                    $discount_account_id = $this->journalEloquent->getAccount($receipt_majors->journal_id, 'DISKON', $request->id);
                                    // start transaction
                                    DB::transaction(function () use ($request, $receipt_majors, $cash_account_id, $receivable_account_id, $discount_account_id, $remark_journal, $is_paid) {
                                        $uuid = strtotime('now');
                                        $receiptMajorRequest = new Request();
                                        $receiptMajorRequest->merge([
                                            'id' => $request->id,
                                            'total' => $request->instalment,
                                            'trans_date' => $this->formatDate($request->journal_date,'sys'),
                                            'remark' => $request->remark,
                                            'reason' => $request->reason,
                                            'discount_amount' => $request->discount,
                                            'employee_id' => auth()->user()->id,
                                            'logged' => auth()->user()->email
                                        ]);
                                        $this->receiptMajorEloquent->update($receiptMajorRequest, $this->subject .' Iuran Wajib Calon Santri');
                                        // get journal_id
                                        $journal_id = ReceiptMajor::find($request->id)->journal_id;
                                        // update journal
                                        Journal::where('id', $journal_id)->update([
                                            'journal_date' => $this->formatDate($request->journal_date,'sys'),
                                            'transaction' => $remark_journal,
                                            'remark' => $request->reason,
                                            'logged' => auth()->user()->email
                                        ]);
                                        // update journal_detail
                                        JournalDetail::where('journal_id', $receipt_majors->journal_id)
                                            ->where('account_id', $cash_account_id)
                                            ->where('credit',0)
                                            ->update([
                                                'debit' => ($request->instalment - $request->discount),
                                                'uuid' => $uuid,
                                                'logged' => auth()->user()->email
                                            ]);
                                        if ($request->cash_account != $cash_account_id)
                                        {
                                            JournalDetail::where('journal_id', $receipt_majors->journal_id)
                                                ->where('account_id', $cash_account_id)
                                                ->where('credit',0)
                                                ->update([
                                                    'account_id' => $request->cash_account,
                                                    'uuid' => $uuid,
                                                    'logged' => auth()->user()->email
                                                ]);
                                        }
                                        JournalDetail::where('journal_id', $receipt_majors->journal_id)
                                            ->where('account_id', $receivable_account_id)
                                            ->where('debit',0)
                                            ->update([
                                                'credit' => $request->instalment,
                                                'uuid' => $uuid,
                                                'logged' => auth()->user()->email
                                            ]);
                                        $count_journal_detail = JournalDetail::select(DB::raw('COUNT(id) as count_id'))->where('journal_id', $journal_id)->where('account_id', $discount_account_id)->first()->count_id;
                                        if ($count_journal_detail == 0 && $request->discount > 0)
                                        {
                                            $this->journalEloquent->createDetail($journal->id, $discount_account_id, $request->discount, 0, $uuid);
                                        } else {
                                            JournalDetail::where('journal_id', $receipt_majors->journal_id)
                                                ->where('account_id', $discount_account_id)
                                                ->update([
                                                    'debit' => $request->discount,
                                                    'credit' => 0,
                                                    'uuid' => $uuid,
                                                    'logged' => auth()->user()->email
                                                ]);
                                        }
                                        // update payment majors
                                        $paymentMajorRequest = new Request();
                                        $paymentMajorRequest->merge([
                                            'id' => $request->major_id,
                                            'is_paid' => $is_paid,
                                            'logged' => auth()->user()->email,
                                        ]);
                                        $this->paymentMajorEloquent->update($paymentMajorRequest, $this->subject .' Iuran Wajib Calon Santri');
                                    });
                                    $response = $this->getResponse('store', '', $this->subject .' Iuran Wajib Calon Santri');
                                }
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    $response = $this->getResponse('error', $e->getMessage(), $this->subject .' Iuran Wajib Calon Santri');
                }
                break;
            case 4:
                // CSSKR
                $validated = $request->validate([
                    'student_id' => 'required|int',
                    'department_id' => 'required|int',
                    'receipt_id' => 'required|int',
                    'bookyear_id' => 'required|int',
                    'is_prospect' => 'required|int',
                    'amount' => 'required|gt:0',
                    'cash_account' => 'required',
                    'journal_date' => 'required'
                ]);
                try {
                    if ($request->id < 1)
                    {
                        // get receipt type
                        $receipt_type = ReceiptType::find($request->receipt_id);
                        // fetch prefix and number from bookyear for cash no.
                        $bookyear = BookYear::find($request->bookyear_id);
                        $number = $bookyear->number;
                        $number += 1;
                        $cash_no = sprintf('%06d', $number);
                        //
                        DB::transaction(function () use ($request, $receipt_type, $cash_no) {
                            $uuid = strtotime('now');
                            $remark_journal = 'Pembayaran ' . $receipt_type->name . ' tanggal ' . date('d/m/Y') . ' calon santri '. $request->student_name .' ('. $request->student_no .')';
                            // store to journal
                            $journal = $this->journalEloquent->store($this->formatDate($request->journal_date,'sys'), $remark_journal, $cash_no, $request->bookyear_id, 'receipt_skr_prospect', $request->department_id);
                            // store journal detail
                            $this->journalEloquent->createDetail($journal->id, $request->cash_account, $request->amount, 0, $uuid);
                            $this->journalEloquent->createDetail($journal->id, $receipt_type->receipt_account, 0, $request->amount, $uuid);
                            // increment number in bookyear
                            BookYear::where('id', $request->bookyear_id)->increment('number');
                            // store receipt voluntary
                            $receiptVoluntaryRequest = new Request();
                            $receiptVoluntaryRequest->merge([
                                'receipt_id' => $receipt_type->id,
                                'journal_id' => $journal->id,
                                'prospect_student_id' => $request->student_id,
                                'trans_date' => date('Y-m-d'),
                                'total' => $request->amount,
                                'employee' => auth()->user()->name,
                                'is_prospect' => 1,
                                'bookyear_id' => $request->bookyear_id,
                                'department_id' => $request->department_id,
                                'remark' => $request->remark,
                                'logged' => auth()->user()->email
                            ]);
                            $this->receiptVoluntaryEloquent->create($receiptVoluntaryRequest, $this->subject . ' Iuran Sukarela Calon Santri');
                        });
                        $response = $this->getResponse('store', '', $this->subject .' Iuran Sukarela Calon Santri');
                    } else {
                        if (empty($request->reason))
                        {
                            throw new Exception('Alasan Ubah Data wajib diisi.', 1);
                        } else {
                            DB::transaction(function () use ($request) {
                                $uuid = strtotime('now');
                                // get existing transaction
                                $receipt_voluntary = ReceiptVoluntary::find($request->id);
                                $receipt_type = ReceiptType::where('category_id', $request->category_id)->where('department_id', $request->department_id)->first();
                                if ($request->amount == $receipt_voluntary->amount)
                                {
                                    // only update info
                                    $receiptVoluntaryRequest = new Request();
                                    $receiptVoluntaryRequest->merge([
                                        'id' => $receipt_voluntary->id,
                                        'trans_date' => $this->formatDate($request->journal_date,'sys'),
                                        'remark' => $request->remark,
                                        'reason' => $request->reason,
                                        'employee' => auth()->user()->name,
                                        'logged' => auth()->user()->email
                                    ]);
                                    $this->receiptVoluntaryEloquent->update($receiptVoluntaryRequest, $this->subject . ' Iuran Sukarela Calon Santri');
                                    // update journal
                                    Journal::where('id', $receipt_voluntary->journal_id)->update([
                                        'journal_date' => $this->formatDate($request->journal_date,'sys'),
                                        'remark' => $request->reason,
                                        'logged' => auth()->user()->email
                                    ]);
                                    // get coa from journal
                                    $account_id = $this->journalEloquent->getAccount($receipt_voluntary->journal_id, 'KAS', $receipt_type->id);
                                    if ($account_id != $request->cash_account)
                                    {
                                        // update journal detail
                                        JournalDetail::where('journal_id', $receipt_voluntary->journal_id)
                                            ->where('account_id', $account_id)
                                            ->where('credit',0)
                                            ->update([
                                                'account_id' => $request->cash_account,
                                                'logged' => auth()->user()->email
                                            ]);
                                    }
                                } else {
                                    // update total
                                    $receiptVoluntaryRequest = new Request();
                                    $receiptVoluntaryRequest->merge([
                                        'id' => $request->id,
                                        'trans_date' => $this->formatDate($request->journal_date,'sys'),
                                        'total' => $request->amount,
                                        'remark' => $request->remark,
                                        'reason' => $request->reason,
                                        'employee' => auth()->user()->name,
                                        'logged' => auth()->user()->email
                                    ]);
                                    $this->receiptVoluntaryEloquent->update($receiptVoluntaryRequest, $this->subject . ' Iuran Sukarela Calon Santri');
                                    // get accounts
                                    $cash_account_id = $this->journalEloquent->getAccount($receipt_voluntary->journal_id, 'KAS', $receipt_type->id);
                                    $receipt_account_id = $this->journalEloquent->getAccount($receipt_voluntary->journal_id, 'PENDAPATAN', $receipt_type->id);
                                    // update journal
                                    Journal::where('id', $receipt_voluntary->journal_id)->update([
                                        'journal_date' => $this->formatDate($request->journal_date,'sys'),
                                        'remark' => $request->reason,
                                        'logged' => auth()->user()->email
                                    ]);
                                    // update journal detail
                                    JournalDetail::where('journal_id', $receipt_voluntary->journal_id)
                                        ->where('account_id', $cash_account_id)
                                        ->where('credit',0)
                                        ->update([
                                            'debit' => $request->amount,
                                            'uuid' => $uuid,
                                            'logged' => auth()->user()->email
                                        ]);
                                    //
                                    if ($cash_account_id != $request->cash_account)
                                    {
                                        // update journal detail
                                        JournalDetail::where('journal_id', $receipt_voluntary->journal_id)
                                            ->where('account_id', $cash_account_id)
                                            ->where('credit',0)
                                            ->update([
                                                'account_id' => $request->cash_account,
                                                'logged' => auth()->user()->email
                                            ]);
                                    }
                                    JournalDetail::where('journal_id', $receipt_voluntary->journal_id)
                                        ->where('account_id', $receipt_account_id)
                                        ->where('debit',0)
                                        ->update([
                                            'credit' => $request->amount,
                                            'uuid' => $uuid,
                                            'logged' => auth()->user()->email
                                        ]);
                                }
                            });
                            $response = $this->getResponse('store', '', $this->subject .' Iuran Sukarela Calon Santri');
                        }
                    }
                } catch (\Throwable $e) {
                    $response = $this->getResponse('error', $e->getMessage(), $this->subject .' Iuran Sukarela Calon Santri');
                }
                break;
            case 5:
                // LNN
                $validated = $request->validate([
                    'department_id' => 'required|int',
                    'receipt_id' => 'required|int',
                    'bookyear_id' => 'required|int',
                    'source' => 'required',
                    'amount' => 'required|gt:0',
                    'cash_account' => 'required',
                    'journal_date' => 'required'
                ]);
                try
                {
                    if ($request->id < 1)
                    {
                        // get receipt type
                        $receipt_type = ReceiptType::find($request->receipt_id);
                        $bookyear = BookYear::find($request->bookyear_id);
                        $number = $bookyear->number;
                        $number += 1;
                        $cash_no = sprintf('%06d', $number);
                        DB::transaction(function () use ($request, $cash_no, $receipt_type) {
                            $uuid = strtotime('now');
                            $remark_journal = 'Data ' . $receipt_type->name . ' tanggal ' . $request->journal_date . ' dari ' . $request->source;
                            // store to journal
                            $journal = $this->journalEloquent->store($this->formatDate($request->journal_date,'sys'), $remark_journal, $cash_no, $request->bookyear_id, 'receipt_others', $request->department_id);
                            // store journal detail
                            $this->journalEloquent->createDetail($journal->id, $request->cash_account, $request->amount, 0, $uuid);
                            $this->journalEloquent->createDetail($journal->id, $receipt_type->receipt_account, 0, $request->amount, $uuid);
                            // increment number in bookyear
                            BookYear::where('id', $request->bookyear_id)->increment('number');
                            // store to receipt other
                            $receiptOtherRequest = new Request();
                            $receiptOtherRequest->merge([
                                'receipt_id' => $request->receipt_id,
                                'journal_id' => $journal->id,
                                'trans_date' => date('Y-m-d'),
                                'total' => $request->amount,
                                'source' => $request->source,
                                'employee' => auth()->user()->name,
                                'bookyear_id' => $request->bookyear_id,
                                'department_id' => $request->department_id,
                                'remark' => $request->remark,
                                'logged' => auth()->user()->email,
                            ]);
                            $this->receiptOtherEloquent->create($receiptOtherRequest, $this->subject .' Sumbangan Lainnya');
                        });
                        $response = $this->getResponse('store', '', $this->subject .' Sumbangan Lainnya');
                    } else {
                        if (empty($request->reason))
                        {
                            throw new Exception('Alasan Ubah Data wajib diisi.', 1);
                        } else {
                            // get old receipt major
                            $receipt_others = ReceiptOther::find($request->id);
                            // only update payment info
                            if ($request->amount == $receipt_others->total)
                            {
                                DB::transaction(function () use ($request, $receipt_others) {
                                    $receiptOtherRequest = new Request();
                                    $receiptOtherRequest->merge([
                                        'id' => $request->id,
                                        'trans_date' => $this->formatDate($request->journal_date, 'sys'),
                                        'remark' => $request->remark,
                                        'reason' => $request->reason,
                                        'logged' => auth()->user()->email
                                    ]);
                                    $this->receiptOtherEloquent->update($receiptOtherRequest, $this->subject . ' Sumbangan Lainnya');
                                    // update journal
                                    Journal::where('id', $receipt_others->journal_id)->update([
                                        'journal_date' => $this->formatDate($request->journal_date,'sys'),
                                        'remark' => $request->reason,
                                        'logged' => auth()->user()->email
                                    ]);
                                    // get COA from journal
                                    $account_id = $this->journalEloquent->getAccount($receipt_others->journal_id, 'KAS', $request->id);
                                    if ($account_id != $request->cash_account)
                                    {
                                        // update journal detail
                                        JournalDetail::where('journal_id', $receipt_others->journal_id)
                                            ->where('account_id', $account_id)
                                            ->where('credit',0)
                                            ->update([
                                                'account_id' => $request->cash_account,
                                                'logged' => auth()->user()->email
                                            ]);
                                    }
                                });
                            } else {
                                DB::transaction(function () use ($request, $receipt_others) {
                                    $uuid = strtotime('now');
                                    $receiptOtherRequest = new Request();
                                    $receiptOtherRequest->merge([
                                        'id' => $request->id,
                                        'source' => $request->source,
                                        'trans_date' => $this->formatDate($request->journal_date, 'sys'),
                                        'total' => $request->amount,
                                        'remark' => $request->remark,
                                        'reason' => $request->reason,
                                        'logged' => auth()->user()->email
                                    ]);
                                    $this->receiptOtherEloquent->update($receiptOtherRequest, $this->subject . ' Sumbangan Lainnya');
                                    // update journal
                                    Journal::where('id', $receipt_others->journal_id)->update([
                                        'journal_date' => $this->formatDate($request->journal_date,'sys'),
                                        'remark' => $request->reason,
                                        'logged' => auth()->user()->email
                                    ]);
                                    //
                                    $cash_account_id = $this->journalEloquent->getAccount($receipt_others->journal_id, 'KAS', $request->receipt_id);
                                    $receipt_account_id = $this->journalEloquent->getAccount($receipt_others->journal_id, 'PENDAPATAN', $request->receipt_id);
                                    // update journal detail
                                    JournalDetail::where('journal_id', $receipt_others->journal_id)
                                        ->where('account_id', $cash_account_id)
                                        ->where('credit',0)
                                        ->update([
                                            'debit' => $request->amount,
                                            'uuid' => $uuid,
                                            'logged' => auth()->user()->email
                                        ]);
                                    //
                                    if ($cash_account_id != $request->cash_account)
                                    {
                                        // update journal detail
                                        JournalDetail::where('journal_id', $receipt_others->journal_id)
                                            ->where('account_id', $cash_account_id)
                                            ->where('credit',0)
                                            ->update([
                                                'account_id' => $request->cash_account,
                                                'logged' => auth()->user()->email
                                            ]);
                                    }
                                    JournalDetail::where('journal_id', $receipt_others->journal_id)
                                        ->where('account_id', $receipt_account_id)
                                        ->where('debit',0)
                                        ->update([
                                            'credit' => $request->amount,
                                            'uuid' => $uuid,
                                            'logged' => auth()->user()->email
                                        ]);
                                });
                            }
                            $response = $this->getResponse('store', '', $this->subject .' Sumbangan Lainnya');
                        }
                    }
                } catch (\Throwable $e) {
                    $response = $this->getResponse('error', $e->getMessage(), $this->subject .' Sumbangan Lainnya');
                }
                break;
            default:
                // JTT
                $validated = $request->validate([
                    'major_id' => 'required|int',
                    'student_id' => 'required|int',
                    'department_id' => 'required|int',
                    'receipt_id' => 'required|int',
                    'bookyear_id' => 'required|int',
                    'is_prospect' => 'required|int',
                    'instalment' => 'required',
                    'discount' => 'required|gt:-1',
                    'cash_account' => 'required|gt:0',
                    'journal_date' => 'required'
                ]);
                try
                {
                    if ($request->id < 1)
                    {
                        // get receipt type
                        $receipt_type = ReceiptType::find($request->receipt_id);
                        // find pay amount
                        $payment_majors = PaymentMajor::find($request->major_id);
                        // check is_paid
                        if ($payment_majors->is_paid == 1)
                        {
                            throw new Exception('Pembayaran ' .$receipt_type->name. ' santri '  . $request->student_name . ' (' . $request->student_no . ') periode '. $this->getMonthName($payment_majors->period_month) .' / '. $payment_majors->period_year .' sudah lunas.', 1);
                        }
                        // get instalment and discount
                        $total_receipt = ReceiptMajor::select(DB::raw('SUM(total) AS total'), DB::raw('SUM(discount_amount) AS instalment'))
                                            ->where('major_id', $payment_majors->id)
                                            ->first();
                        // check instalment amount with payment major must have paid
                        $remark_journal = '';
                        $is_paid = 0;
                        if (($total_receipt->total + $total_receipt->instalment + ($request->instalment - $request->discount) + $request->discount) > $payment_majors->amount)
                        {
                            throw new Exception('Maaf, pembayaran tidak dapat dilakukan! Jumlah bayaran cicilan lebih besar daripada pembayaran yang harus dilunasi.', 1);
                        } else {
                            $is_paid = 0;
                            $remark_journal = '';
                            if (($total_receipt->total + $total_receipt->instalment + ($request->instalment - $request->discount) + $request->discount) == $payment_majors->amount)
                            {
                                $remark_journal = 'Pelunasan '. $receipt_type->name .' santri '. $request->student_name .' ('. $request->student_no .') periode '. $this->getMonthName($payment_majors->period_month) .'/'. $payment_majors->period_year;
                                $is_paid = 1;
                            } else {
                                $instalment_count = ReceiptMajor::where('major_id', $payment_majors->id)->count() + 1;
                                $remark_journal = 'Pembayaran ke-'. $instalment_count . ' ' . $receipt_type->name . ' santri '. $request->student_name .' ('. $request->student_no .') periode '. $this->getMonthName($payment_majors->period_month) .'/'. $payment_majors->period_year;
                                $is_paid = 0;
                            }
                            // fetch prefix and number from bookyear for cash no.
                            $bookyear = BookYear::find($request->bookyear_id);
                            $number = $bookyear->number;
                            $number += 1;
                            $cash_no = sprintf('%06d', $number);
                            // db transaction
                            DB::transaction(function () use ($request, $remark_journal, $receipt_type, $cash_no, $payment_majors, $is_paid) {
                                $uuid = strtotime('now');
                                // store to journal
                                $journal = $this->journalEloquent->store($this->formatDate($request->journal_date,'sys'), $remark_journal, $cash_no, $request->bookyear_id, 'receipt_jtt', $request->department_id);
                                // store journal detail
                                $this->journalEloquent->createDetail($journal->id, $request->cash_account, $request->instalment - $request->discount, 0, $uuid);
                                $this->journalEloquent->createDetail($journal->id, $receipt_type->receivable_account, 0, $request->instalment, $uuid);
                                if ($request->discount > 0)
                                {
                                    $this->journalEloquent->createDetail($journal->id, $receipt_type->discount_account, $request->discount, 0, $uuid);
                                }
                                // increment number in bookyear
                                BookYear::where('id', $request->bookyear_id)->increment('number');
                                // store to receipt major
                                $receiptMajorRequest = new Request();
                                $receiptMajorRequest->merge([
                                    'trans_date' => $this->formatDate($request->journal_date,'sys'),
                                    'major_id' => $payment_majors->id,
                                    'journal_id' => $journal->id,
                                    'total' => $request->instalment - $request->discount,
                                    'employee_id' => auth()->user()->id,
                                    'first_instalment' => 1,
                                    'is_prospect' => $request->is_prospect,
                                    'discount_amount' => $request->discount,
                                    'remark' => $this->getMonthName($payment_majors->period_month) .'/'. $payment_majors->period_year,
                                    'logged' => auth()->user()->email,
                                ]);
                                $this->receiptMajorEloquent->create($receiptMajorRequest, $this->subject .' Iuran Wajib Santri');
                                // update is_paid
                                if ($is_paid > 0)
                                {
                                    $paymentMajorRequest = new Request();
                                    $paymentMajorRequest->merge([
                                        'id' => $payment_majors->id,
                                        'is_paid' => 1,
                                        'logged' => auth()->user()->email,
                                    ]);
                                    $this->paymentMajorEloquent->update($paymentMajorRequest, $this->subject .' Iuran Wajib Santri');
                                }
                            });
                            $response = $this->getResponse('store', '', $this->subject .' Iuran Wajib Santri', $is_paid);
                        }
                    } else {
                        if (empty($request->reason))
                        {
                            throw new Exception('Alasan Ubah Data wajib diisi.', 1);
                        } else {
                            // get receipt type
                            $receipt_type = ReceiptType::find($request->receipt_id);
                            // get old receipt major
                            $receipt_majors = ReceiptMajor::find($request->id);
                            // only update payment info
                            if ($request->instalment == $receipt_majors->total && $request->discount == $receipt_majors->discount_amount)
                            {
                                DB::transaction(function () use ($request, $receipt_majors) {
                                    $receiptMajorRequest = new Request();
                                    $receiptMajorRequest->merge([
                                        'id' => $request->id,
                                        'trans_date' => $this->formatDate($request->journal_date,'sys'),
                                        'remark' => $request->remark,
                                        'reason' => $request->reason,
                                        'logged' => auth()->user()->email
                                    ]);
                                    $this->receiptMajorEloquent->update($receiptMajorRequest, $this->subject .' Iuran Wajib Santri');
                                    // update journal
                                    Journal::where('id', $receipt_majors->journal_id)->update([
                                            'journal_date' => $this->formatDate($request->journal_date,'sys'),
                                            'remark' => $request->reason,
                                            'updated_at' => Carbon::now(),
                                            'logged' => auth()->user()->email
                                        ]);
                                    // get COA from journal
                                    $account_id = $this->journalEloquent->getAccount($receipt_majors->journal_id, 'KAS', $request->id);
                                    if ($account_id != $request->cash_account)
                                    {
                                        // update journal detail
                                        JournalDetail::where('journal_id', $receipt_majors->journal_id)
                                            ->where('account_id', $account_id)
                                            ->where('credit',0)
                                            ->update([
                                                'account_id' => $request->cash_account,
                                                'logged' => auth()->user()->email
                                            ]);
                                    }
                                });
                                $response = $this->getResponse('store', '', $this->subject .' Iuran Wajib Santri');
                            } else {
                                $payment_majors = PaymentMajor::find($request->major_id);
                                $total_receipt = ReceiptMajor::select(DB::raw('SUM(total) AS total'), DB::raw('SUM(discount_amount) AS discount'))
                                                    ->where('major_id', $request->major_id)
                                                    ->where('id','<>',$request->id)
                                                    ->first();
                                if (($total_receipt->total + $total_receipt->discount + ($request->instalment - $request->discount) + $request->discount) > $payment_majors->amount)
                                {
                                    throw new Exception('Maaf, pembayaran tidak dapat dilakukan! Jumlah bayaran cicilan lebih besar daripada pembayaran yang harus dilunasi.', 1);
                                } else {
                                    $remark_journal = '';
                                    $is_paid = 0;
                                    if (($total_receipt->total + $total_receipt->discount + ($request->instalment - $request->discount) + $request->discount) == $payment_majors->amount)
                                    {
                                        $remark_journal = 'Pelunasan '. $receipt_type->name .' santri '. $request->student_name .' ('. $request->student_no .') periode '. $this->getMonthName($payment_majors->period_month) .'/'. $payment_majors->period_year;
                                        $is_paid = 1;
                                    } else {
                                        $instalment_count = 0;
                                        $receipt_major = ReceiptMajor::where('major_id', $request->major_id)->orderBy('trans_date')->get();
                                        foreach ($receipt_major as $val)
                                        {
                                            $instalment_count++;
                                            if ($val->id == $request->receipt_id)
                                                break;
                                        }
                                        $remark_journal = 'Pembayaran ke-'. $instalment_count . ' ' . $receipt_type->name . ' santri '. $request->student_name .' ('. $request->student_no .') periode '. $this->getMonthName($payment_majors->period_month) .'/'. $payment_majors->period_year;
                                        $is_paid = 0;
                                    }
                                    $cash_account_id = $this->journalEloquent->getAccount($receipt_majors->journal_id, 'KAS', $request->id);
                                    $receivable_account_id = $this->journalEloquent->getAccount($receipt_majors->journal_id, 'PIUTANG', $request->id);
                                    $discount_account_id = $this->journalEloquent->getAccount($receipt_majors->journal_id, 'DISKON', $request->id);
                                    // start transaction
                                    DB::transaction(function () use ($request, $payment_majors, $receipt_majors, $cash_account_id, $receivable_account_id, $discount_account_id, $remark_journal, $is_paid) {
                                        $uuid = strtotime('now');
                                        $receiptMajorRequest = new Request();
                                        $receiptMajorRequest->merge([
                                            'id' => $request->id,
                                            'total' => $request->instalment,
                                            'trans_date' => $this->formatDate($request->journal_date,'sys'),
                                            'remark' => $request->remark,
                                            'reason' => $request->reason,
                                            'discount_amount' => $request->discount,
                                            'logged' => auth()->user()->email
                                        ]);
                                        $this->receiptMajorEloquent->update($receiptMajorRequest, $this->subject .' Iuran Wajib Santri');
                                        // get journal_id
                                        $journal_id = ReceiptMajor::find($request->id)->journal_id;
                                        // update journal
                                        Journal::where('id', $journal_id)->update([
                                            'journal_date' => $this->formatDate($request->journal_date,'sys'),
                                            'transaction' => $remark_journal,
                                            'logged' => auth()->user()->email
                                        ]);
                                        // update journal_detail
                                        JournalDetail::where('journal_id', $journal_id)
                                            ->where('account_id', $cash_account_id)
                                            ->where('credit',0)
                                            ->update([
                                                'debit' => ($request->instalment - $request->discount),
                                                'uuid' => $uuid,
                                                'logged' => auth()->user()->email
                                            ]);
                                        if ($request->cash_account != $cash_account_id)
                                        {
                                            JournalDetail::where('journal_id', $journal_id)
                                                ->where('account_id', $cash_account_id)
                                                ->where('credit',0)
                                                ->update([
                                                    'account_id' => $request->cash_account,
                                                    'uuid' => $uuid,
                                                    'logged' => auth()->user()->email
                                                ]);
                                        }
                                        JournalDetail::where('journal_id', $journal_id)
                                            ->where('account_id', $receivable_account_id)
                                            ->where('debit',0)
                                            ->update([
                                                'credit' => $request->instalment,
                                                'uuid' => $uuid,
                                                'logged' => auth()->user()->email
                                            ]);
                                        $count_journal_detail = JournalDetail::select(DB::raw('COUNT(id) as count_id'))->where('journal_id', $journal_id)->where('account_id', $discount_account_id)->first()->count_id;
                                        if ($count_journal_detail == 0 && $request->discount > 0)
                                        {
                                            $this->journalEloquent->createDetail($journal_id, $discount_account_id, $request->discount, 0, $uuid);
                                        } else {
                                            JournalDetail::where('journal_id', $journal_id)
                                                ->where('account_id', $discount_account_id)
                                                ->update([
                                                    'debit' => $request->discount,
                                                    'credit' => 0,
                                                    'uuid' => $uuid,
                                                    'logged' => auth()->user()->email
                                                ]);
                                        }
                                        // update payment majors
                                        $paymentMajorRequest = new Request();
                                        $paymentMajorRequest->merge([
                                            'id' => $request->major_id,
                                            'is_paid' => $is_paid,
                                            'logged' => auth()->user()->email,
                                        ]);
                                        $this->paymentMajorEloquent->update($paymentMajorRequest, $this->subject .' Iuran Wajib Santri');
                                    });
                                    $response = $this->getResponse('store', '', $this->subject .' Iuran Wajib Santri');
                                }
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    $response = $this->getResponse('error', $e->getMessage(), $this->subject .' Iuran Wajib Santri');
                }
                break;
        }
        return response()->json($response);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return JSON
     */
    public function show($id, $category_id)
    {
        switch ($category_id)
        {
            case 2:
                return response()->json($this->receiptVoluntaryEloquent->show($id));
                break;
            case 3:
                return response()->json($this->receiptMajorEloquent->show($category_id, $id));
                break;
            case 4:
                return response()->json($this->receiptVoluntaryEloquent->show($id));
                break;
            case 5:
                return response()->json($this->receiptOtherEloquent->show($id));
                break;
            default:
                return response()->json($this->receiptMajorEloquent->show($category_id, $id));
                break;
        }
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return JSON
     */
    public function data(Request $request)
    {
        return $this->receiptMajorEloquent->data($request);
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return JSON
     */
    public function dataVoluntary(Request $request)
    {
        return $this->receiptVoluntaryEloquent->data($request);
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return JSON
     */
    public function dataOther(Request $request)
    {
        return $this->receiptOtherEloquent->data($request);
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return JSON
     */
    public function dataPeriod(Request $request)
    {
        $receipt_type = ReceiptType::find($request->receipt_id);
        $periods = PaymentMajor::where('department_id', $request->department_id)
                        ->where('category_id', $receipt_type->category_id)
                        ->where('receipt_id', $request->receipt_id)
                        ->where('student_id', $request->student_id)
                        ->orderByDesc('id')
                        ->get()->map(function($model) {
                            $model['id'] = $model->id;
                            $model['text'] = $this->getMonthName($model->period_month) .' / '. $model->period_year;
                            return $model->only('id','text');
                        });
        return response()->json($periods);
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return JSON
     */
    public function dataPayment(Request $request)
    {
        $receipt_type = ReceiptType::find($request->receipt_id);
        $payment_majors = PaymentMajor::where('department_id', $request->department_id)
                            ->where('category_id', $receipt_type->category_id)
                            ->where('receipt_id', $request->receipt_id);
        if ($receipt_type->category_id == 1)
        {
            $payment_majors = $payment_majors->where('id', $request->payment_major_id)->first();
        } elseif ($receipt_type->category_id == 2) {
            $payment_majors = $payment_majors->where('student_id', $request->student_id)->first();
        } elseif ($receipt_type->category_id == 3 || $receipt_type->category_id == 4) {
            $payment_majors = $payment_majors->where('prospect_student_id', $request->student_id)->first();
        } else {
            $payment_majors = $payment_majors->first();
        }
        $journal = Journal::find($payment_majors->journal_id);
        //
        return response()->json(array(
            'payment_major_id' => $payment_majors->id,
            'amount' => $payment_majors->amount,
            'instalment' => $payment_majors->instalment,
            'is_paid' => $payment_majors->is_paid,
            'period' => 'Tahun ' . $payment_majors->period_year,
            'journal_date' => $this->formatDate($journal->journal_date,'local'),
        ));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function print(Request $request)
    {
        $payload = json_decode($request->data);
        $data['requests'] = $payload;
        $data['profile'] = $this->getInstituteProfile();
        switch ($payload->category_id)
        {
            case 2:
                $data['categories'] = DB::table('finance.receipt_categories')->select(
                                            DB::raw('UPPER(public.departments.name) as department'),
                                            'finance.receipt_categories.category',
                                            'finance.receipt_types.name',
                                        )
                                        ->join('finance.receipt_types','finance.receipt_types.category_id','=','finance.receipt_categories.id')
                                        ->join('public.departments','public.departments.id','=','finance.receipt_types.department_id')
                                        ->where('finance.receipt_categories.id', $payload->category_id)
                                        ->where('finance.receipt_types.department_id', $payload->department_id)
                                        ->first();
                $data['bookyear'] = $this->getActiveBookYear();
                $data['students'] = Students::find($payload->student_id);
                $data['payments'] = $this->receiptVoluntaryEloquent->dataPayment($payload->is_prospect, $payload->student_id);
                $data['total'] = $this->receiptVoluntaryEloquent->totalPayment($payload->is_prospect, $payload->student_id);
                $view = View::make('finance::pages.receipts.receipt_voluntary_pdf', $data);
                break;
            case 3:
                $data['payment_majors'] = PaymentMajor::where('department_id', $payload->department_id)
                                            ->where('category_id', $payload->category_id)
                                            ->where('prospect_student_id', $payload->student_id)
                                            ->where('receipt_id', $payload->receipt_id)
                                            ->where('bookyear_id', $payload->bookyear_id)
                                            ->orderBy('id', 'asc')
                                            ->get()->map(function($model){
                                                $model['department'] = $model->getDepartment->name;
                                                $model['book_year'] = $model->getBookYear->book_year;
                                                $model['category'] = $model->getCategory->category;
                                                $model['receipt_type'] = $model->getReceipt->name;
                                                $model['student'] = DB::table('academic.prospect_students')->find($model->prospect_student_id);
                                                $model['period_payment'] = 'Tahun '. $model->period_year;
                                                return $model;
                                            });

                $view = View::make('finance::pages.receipts.receipts_pdf', $data);
                break;
            case 4:
                $data['categories'] = DB::table('finance.receipt_categories')->select(
                                            DB::raw('UPPER(public.departments.name) as department'),
                                            'finance.receipt_categories.category',
                                            'finance.receipt_types.name',
                                        )
                                        ->join('finance.receipt_types','finance.receipt_types.category_id','=','finance.receipt_categories.id')
                                        ->join('public.departments','public.departments.id','=','finance.receipt_types.department_id')
                                        ->where('finance.receipt_categories.id', $payload->category_id)
                                        ->where('finance.receipt_types.department_id', $payload->department_id)
                                        ->first();
                $data['students'] = AdmissionProspect::find($payload->student_id);
                $data['payments'] = $this->receiptVoluntaryEloquent->dataPayment($payload->is_prospect, $payload->student_id);
                $data['total'] = $this->receiptVoluntaryEloquent->totalPayment($payload->is_prospect, $payload->student_id);
                $data['bookyear'] = $this->getActiveBookYear();
                $view = View::make('finance::pages.receipts.receipt_voluntary_pdf', $data);
                break;
            default:
                $data['payment_majors'] = PaymentMajor::where('department_id', $payload->department_id)
                                            ->where('category_id', $payload->category_id)
                                            ->where('student_id', $payload->student_id)
                                            ->where('receipt_id', $payload->receipt_id)
                                            ->where('bookyear_id', $payload->bookyear_id)
                                            ->orderBy('id', 'asc')
                                            ->get()->map(function($model){
                                                $model['department'] = $model->getDepartment->name;
                                                $model['book_year'] = $model->getBookYear->book_year;
                                                $model['category'] = $model->getCategory->category;
                                                $model['receipt_type'] = $model->getReceipt->name;
                                                $model['student'] = $model->getStudent;
                                                $model['period_payment'] = $this->getMonthName($model->period_month) .' / '. $model->period_year;
                                                return $model;
                                            });
                $view = View::make('finance::pages.receipts.receipts_pdf', $data);
                break;
        }
        //
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake();
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        //
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortraits($hashfile, $filename);
        echo $filename;
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function printReceipt(Request $request)
    {
        $data['profile'] = $this->getInstituteProfile();
        $payload = $request->all();
        if ($payload['category_id'] == 1 || $payload['category_id'] == 3)
        {
            $data['receipt_majors'] = ReceiptMajor::select(
                                            'finance.receipt_majors.trans_date',
                                            'finance.receipt_majors.total',
                                            'finance.receipt_majors.major_id',
                                            'finance.journals.bookyear_id',
                                            'finance.journals.transaction',
                                            'finance.journals.cash_no',
                                            'public.employees.name',
                                            'finance.payment_majors.amount',
                                        )
                                        ->join('finance.payment_majors','finance.payment_majors.id','=','finance.receipt_majors.major_id')
                                        ->join('finance.journals','finance.journals.id','=','finance.receipt_majors.journal_id')
                                        ->join('public.employees','public.employees.id','=','finance.journals.employee_id')
                                        ->where('finance.receipt_majors.id', $payload['receipt_id'])
                                        ->get()->map(function($model){
                                            $model['trans_date'] = $this->formatDate($model['trans_date'],'iso');
                                            $model['cash_no'] = $this->getPrefixBookYear($model->bookyear_id) . $model->cash_no;
                                            return $model;
                                        })[0];
            $data['total'] = $this->receiptMajorEloquent->totalInstalment($data['receipt_majors']['major_id']);
            $data['requests'] = $payload;
            $data['values'] = array(
                'total' => 'Rp'.number_format($data['receipt_majors']['total'], 2),
                'counted' => $data['receipt_majors']['total'] > 0 ? $this->counted($data['receipt_majors']['total']) : 'nol',
                'balance' => 'Rp'.number_format(($data['receipt_majors']['amount'] - $data['total']['total']), 2)
            );
        } elseif ($payload['category_id'] == 2 || $payload['category_id'] == 4) {
            $data['receipt_majors'] = ReceiptVoluntary::select(
                                            'finance.receipt_voluntaries.trans_date',
                                            'finance.receipt_voluntaries.total',
                                            'finance.receipt_voluntaries.receipt_id',
                                            'finance.journals.transaction',
                                            'finance.journals.cash_no',
                                            'finance.journals.bookyear_id',
                                            'public.employees.name',
                                        )
                                        ->join('finance.journals','finance.journals.id','=','finance.receipt_voluntaries.journal_id')
                                        ->join('public.employees','public.employees.id','=','finance.journals.employee_id')
                                        ->where('finance.receipt_voluntaries.id', $payload['receipt_id'])
                                        ->get()->map(function($model){
                                            $model['trans_date'] = $this->formatDate($model['trans_date'],'iso');
                                            $model['cash_no'] = $this->getPrefixBookYear($model->bookyear_id) . $model->cash_no;
                                            return $model;
                                        })[0];
            $data['total'] = $this->receiptVoluntaryEloquent->dataPayment($payload['is_prospect'], $payload['student_id']);
            $data['requests'] = $payload;
            $data['values'] = array(
                'total' => 'Rp'.number_format($data['receipt_majors']['total'], 2),
                'counted' => $data['receipt_majors']['total'] > 0 ? $this->counted($data['receipt_majors']['total']) : 'nol',
            );
        } else {
            $data['receipt_majors'] = ReceiptOther::select(
                                            'finance.receipt_others.trans_date',
                                            'finance.receipt_others.total',
                                            'finance.receipt_others.receipt_id',
                                            'finance.journals.transaction',
                                            'finance.journals.cash_no',
                                            'finance.journals.bookyear_id',
                                            DB::raw('employee as name'),
                                        )
                                        ->join('finance.journals','finance.journals.id','=','finance.receipt_others.journal_id')
                                        ->where('finance.receipt_others.id', $payload['receipt_id'])
                                        ->get()->map(function($model){
                                            $model['trans_date'] = $this->formatDate($model['trans_date'],'iso');
                                            $model['cash_no'] = $this->getPrefixBookYear($model->bookyear_id) . $model->cash_no;
                                            return $model;
                                        })[0];
            $data['total'] = $this->receiptOtherEloquent->dataPayment();
            $data['requests'] = $payload;
            $data['values'] = array(
                'total' => 'Rp'.number_format($data['receipt_majors']['total'], 2),
                'counted' => $data['receipt_majors']['total'] > 0 ? $this->counted($data['receipt_majors']['total']) : 'nol',
            );
        }
        $view = View::make('finance::pages.receipts.receipt_pdf', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake();
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        //
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortrait($hashfile, $filename);
        echo $filename;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function dataShow(Request $request)
    {
        if (!$request->ajax())
        {
            abort(404);
        }
        $data['url'] = url('/storage/downloads') . '/' . $request->url;
        return view('finance::pages.receipts.receipt_view_pdf', $data);
    }
}
