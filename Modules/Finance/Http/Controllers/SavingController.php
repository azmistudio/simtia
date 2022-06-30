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
use App\Http\Traits\HelperTrait;
use App\Http\Traits\PdfTrait;
use Modules\Academic\Entities\Students;
use Modules\HR\Entities\Employee;
use Modules\Finance\Entities\Saving;
use Modules\Finance\Entities\SavingType;
use Modules\Finance\Entities\Journal;
use Modules\Finance\Entities\JournalDetail;
use Modules\Finance\Entities\BookYear;
use Modules\Finance\Repositories\Saving\SavingEloquent;
use Modules\Finance\Repositories\Saving\SavingTypeEloquent;
use Modules\Finance\Repositories\Reference\CodeEloquent;
use Modules\Finance\Repositories\Journal\JournalEloquent;
use Modules\Finance\Http\Requests\SavingRequest;
use View;
use Exception;

class SavingController extends Controller
{
    use DepartmentTrait;
    use HelperTrait;
    use PdfTrait;

    private $subject_student = 'Data Transaksi Tabungan Santri';
    private $subject_employee = 'Data Transaksi Tabungan Pegawai';

    function __construct(
        SavingEloquent $savingEloquent, 
        SavingTypeEloquent $savingTypeEloquent, 
        CodeEloquent $codeEloquent,
        JournalEloquent $journalEloquent
    )
    {
        $this->journalEloquent = $journalEloquent;
        $this->savingEloquent = $savingEloquent;
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
        $data['bookyear'] = $this->getActiveBookYear();
        if ($data['bookyear']->id > 0)
        {
            return view('finance::pages.savings.student_saving', $data);
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
    public function storeStudent(SavingRequest $request)
    {
        $validated = $request->validated();
        try 
        {
            $request->merge([
                'is_employee' => 0,
                'trans_date' => $this->formatDate($request->trans_date,'sys'),
                'debit' => $request->transaction_type == 'debit' ? $request->amount : 0,
                'credit' => $request->transaction_type == 'credit' ? $request->amount : 0,
                'employee' => auth()->user()->name,
                'logged' => auth()->user()->email,
            ]);
            if ($request->id < 1) 
            {
                // get balance
                $saving_balance = $this->savingEloquent->getBalance($request->saving_id, $request->student_id, 0);
                // get saving type
                $saving_type = SavingType::find($request->saving_id);
                // create cash no
                $bookyear = BookYear::find($request->bookyear_id);
                $number = $bookyear->number;
                $number += 1;
                $cash_no = sprintf('%06d', $number);
                // 
                if ($request->transaction_type == 'credit')
                {
                    /* deposit */
                    $remark_journal = 'Setoran tabungan ' . $saving_type->name . ' santri ' . $request->student_name . ' (' . $request->student_no . ')';
                    // transaction
                    DB::transaction(function () use ($request, $cash_no, $remark_journal, $saving_balance, $saving_type) {
                        $uuid = strtotime('now');
                        // store to journal
                        $journal = $this->journalEloquent->store($request->trans_date, $remark_journal, $cash_no, $request->bookyear_id, 'savingdeposit', $request->department_id);
                        // store journal detail
                        $this->journalEloquent->createDetail($journal->id, $request->cash_account, $request->amount, 0, $uuid);
                        $this->journalEloquent->createDetail($journal->id, $saving_type->credit_account, 0, $request->amount, $uuid);
                        // increment number in bookyear                  
                        BookYear::where('id', $request->bookyear_id)->increment('number');
                        $request->merge([
                            'journal_id' => $journal->id,
                        ]);
                        // store saving
                        $this->savingEloquent->create($request, $this->subject_student);
                    });
                    $response = $this->getResponse('store', '', $this->subject_student);
                } else {
                    /* withdraw */
                    if ($request->amount > $saving_balance)
                    {
                        throw new Exception('Saldo tabungan tidak mencukupi untuk penarikan.', 1);
                    } else {
                        $remark_journal = 'Penarikan tabungan ' . $saving_type->name . ' santri ' . $request->student_name . ' (' . $request->student_no . ')';
                        DB::transaction(function () use ($request, $cash_no, $remark_journal, $saving_balance, $saving_type) {
                            $uuid = strtotime('now');
                            // store to journal
                            $journal = $this->journalEloquent->store($request->trans_date, $remark_journal, $cash_no, $request->bookyear_id, 'savingwithdrawal', $request->department_id);
                            // store journal detail
                            $this->journalEloquent->createDetail($journal->id, $request->cash_account, 0, $request->amount, $uuid);
                            $this->journalEloquent->createDetail($journal->id, $saving_type->credit_account, $request->amount, 0, $uuid);
                            // increment number in bookyear                  
                            BookYear::where('id', $request->bookyear_id)->increment('number');
                            $request->merge([
                                'journal_id' => $journal->id,
                            ]);
                            // store saving
                            $this->savingEloquent->create($request, $this->subject_student);
                        });
                        $response = $this->getResponse('store', '', $this->subject_student);
                    }
                }
            } else {
                if (empty($request->reason))
                {
                    throw new Exception('Alasan Ubah Data wajib diisi.', 1);
                } else {
                    $isValid = true;
                    $error_message = '';
                    // get saving
                    $transaction = Saving::find($request->id);
                    $cash_account_id = JournalDetail::select('finance.journal_details.account_id','finance.codes.code')
                                        ->join('finance.journals','finance.journals.id','=','finance.journal_details.journal_id')
                                        ->join('finance.codes','finance.codes.id','=','finance.journal_details.account_id')
                                        ->join('finance.code_categories','finance.code_categories.id','=','finance.codes.category_id')
                                        ->where('finance.journal_details.journal_id', $transaction->journal_id)
                                        ->where('finance.code_categories.category', 'HARTA')
                                        ->first();
                    // get saving type
                    $saving_type = SavingType::find($request->saving_id);
                    $uuid = strtotime('now');
                    // check reverse transaction type
                    if ($transaction->transaction_type != $request->transaction_type)
                    {
                        // from credit to debit
                        if ($transaction->transaction_type == 'credit')
                        {
                            // get balance
                            $saving_balance = $this->savingEloquent->getBalance($request->saving_id, $request->student_id, 0);
                            $old_value = JournalDetail::where('journal_id', $transaction->journal_id)->where('debit','>',0)->first()->debit;
                            if ( (($saving_balance - $old_value) - $request->amount) < 0 )
                            {
                                $isValid = false;
                                $error_message = 'Saldo tabungan tidak mencukupi.';
                            } else {
                                // check if amount not change
                                if (
                                    ($transaction->credit == 0 && $request->amount == $transaction->debit) || 
                                    ($transaction->debit == 0 && $request->amount == $transaction->credit)
                                )
                                {
                                    DB::transaction(function () use ($request, $transaction, $saving_type, $uuid) {
                                        // update journal
                                        Journal::where('id', $transaction->journal_id)->update([
                                            'journal_date' => $request->trans_date,
                                            'transaction' => 'Penarikan tabungan ' . $saving_type->name . ' santri ' . $request->student_name . ' (' . $request->student_no . ')',
                                            'source' => 'savingwithdrawal',
                                            'logged' => auth()->user()->email
                                        ]);
                                        JournalDetail::where('journal_id', $transaction->journal_id)->delete();
                                        // create journal detail
                                        $this->journalEloquent->createDetail($transaction->journal_id, $request->cash_account, 0, $request->amount, $uuid);
                                        $this->journalEloquent->createDetail($transaction->journal_id, $saving_type->credit_account, $request->amount, 0, $uuid);
                                        // update info
                                        $savingRequest = new Request();
                                        $savingRequest->merge([
                                            'id' => $request->id,
                                            'trans_date' => $request->trans_date,
                                            'debit' => $transaction->credit,
                                            'credit' => 0,
                                            'remark' => $request->remark,
                                            'reason' => $request->reason,
                                            'transaction_type' => 'debit',
                                            'employee' => auth()->user()->name,
                                            'logged' => auth()->user()->email,
                                        ]);
                                        $this->savingEloquent->update($savingRequest, $this->subject_student);
                                    });
                                } else {
                                    // amount changed
                                    DB::transaction(function () use ($request, $transaction, $saving_type, $uuid) {
                                        // update journal
                                        Journal::where('id', $transaction->journal_id)->update([
                                            'journal_date' => $request->trans_date,
                                            'remark' => $request->reason,
                                            'logged' => auth()->user()->email
                                        ]);
                                        // update journal detail
                                        JournalDetail::where('journal_id', $transaction->journal_id)
                                            ->where('account_id', $transaction->cash_account)
                                            ->update([
                                                'account_id' => $request->cash_account,
                                                'debit' => 0,
                                                'credit' => $request->amount,
                                                'uuid' => $uuid,
                                                'logged' => auth()->user()->email,
                                            ]);
                                        JournalDetail::where('journal_id', $transaction->journal_id)
                                            ->where('account_id', $transaction->credit_account)
                                            ->update([
                                                'account_id' => $saving_type->credit_account,
                                                'debit' => $request->amount,
                                                'credit' => 0,
                                                'uuid' => $uuid,
                                                'logged' => auth()->user()->email,
                                            ]);
                                        // update info
                                        $savingRequest = new Request();
                                        $savingRequest->merge([
                                            'id' => $request->id,
                                            'trans_date' => $request->trans_date,
                                            'debit' => $request->amount,
                                            'credit' => 0,
                                            'remark' => $request->remark,
                                            'reason' => $request->reason,
                                            'transaction_type' => 'debit',
                                            'employee' => auth()->user()->name,
                                            'logged' => auth()->user()->email,
                                        ]);
                                        $this->savingEloquent->update($savingRequest, $this->subject_student);
                                    });
                                }
                            }
                        } else {
                            // from debit to credit
                            // check if amount not change
                            if (
                                ($transaction->debit == 0 && $request->amount == $transaction->credit) || 
                                ($transaction->credit == 0 && $request->amount == $transaction->debit)
                            )
                            {
                                DB::transaction(function () use ($request, $transaction, $saving_type, $uuid) {
                                    // update journal detail
                                    JournalDetail::where('journal_id', $transaction->journal_id)
                                        ->where('account_id', $transaction->cash_account)
                                        ->update([
                                            'account_id' => $request->cash_account,
                                            'debit' => $transaction->debit,
                                            'credit' => 0,
                                            'uuid' => $uuid,
                                            'logged' => auth()->user()->email,
                                        ]);
                                    JournalDetail::where('journal_id', $transaction->journal_id)
                                        ->where('account_id', $transaction->credit_account)
                                        ->update([
                                            'account_id' => $saving_type->credit_account,
                                            'debit' => 0,
                                            'credit' => $transaction->debit,
                                            'uuid' => $uuid,
                                            'logged' => auth()->user()->email,
                                        ]);
                                    // update info
                                    $savingRequest = new Request();
                                    $savingRequest->merge([
                                        'id' => $request->id,
                                        'trans_date' => $request->trans_date,
                                        'remark' => $request->remark,
                                        'reason' => $request->reason,
                                        'debit' => 0,
                                        'credit' => $transaction->debit,
                                        'transaction_type' => 'credit',
                                        'employee' => auth()->user()->name,
                                        'logged' => auth()->user()->email,
                                    ]);
                                    $this->savingEloquent->update($savingRequest, $this->subject_student);
                                });
                            } else {
                                // amount changed
                                DB::transaction(function () use ($request, $transaction, $saving_type, $uuid) {
                                    // update journal detail
                                    JournalDetail::where('journal_id', $transaction->journal_id)
                                        ->where('account_id', $transaction->cash_account)
                                        ->update([
                                            'account_id' => $request->cash_account,
                                            'debit' => $request->amount,
                                            'credit' => 0,
                                            'uuid' => $uuid,
                                            'logged' => auth()->user()->email,
                                        ]);
                                    JournalDetail::where('journal_id', $transaction->journal_id)
                                        ->where('account_id', $transaction->credit_account)
                                        ->update([
                                            'account_id' => $saving_type->credit_account,
                                            'debit' => 0,
                                            'credit' => $request->amount,
                                            'uuid' => $uuid,
                                            'logged' => auth()->user()->email,
                                        ]);
                                    // update info
                                    $savingRequest = new Request();
                                    $savingRequest->merge([
                                        'id' => $request->id,
                                        'trans_date' => $request->trans_date,
                                        'debit' => 0,
                                        'credit' => $request->amount,
                                        'remark' => $request->remark,
                                        'reason' => $request->reason,
                                        'transaction_type' => 'credit',
                                        'employee' => auth()->user()->name,
                                        'logged' => auth()->user()->email,
                                    ]);
                                    $this->savingEloquent->update($savingRequest, $this->subject_student);
                                });
                            }
                            // update journal
                            Journal::where('id', $transaction->journal_id)->update([
                                'journal_date' => $request->trans_date,
                                'transaction' => 'Setoran tabungan ' . $saving_type->name . ' santri ' . $request->student_name . ' (' . $request->student_no . ')',
                                'source' => 'savingdeposit',
                                'logged' => auth()->user()->email
                            ]);
                        }
                    } else {
                        if (
                            ($transaction->debit == 0 && $request->amount == $transaction->credit) || 
                            ($transaction->credit == 0 && $request->amount == $transaction->debit)
                        )
                        {
                            // update info
                            $savingRequest = new Request();
                            $savingRequest->merge([
                                'id' => $request->id,
                                'trans_date' => $request->trans_date,
                                'remark' => $request->remark,
                                'reason' => $request->reason,
                                'employee' => auth()->user()->name,
                                'logged' => auth()->user()->email,
                            ]);
                            $this->savingEloquent->update($savingRequest, $this->subject_student);
                            // update journal
                            Journal::where('id', $transaction->journal_id)->update([
                                'journal_date' => $request->trans_date,
                                'remark' => $request->reason,
                                'logged' => auth()->user()->email
                            ]);
                        } else {
                            if ($request->transaction_type == 'debit')
                            {
                                // get balance
                                $sum_trans = Saving::select(
                                                    DB::raw('SUM(debit) as total_debit'),
                                                    DB::raw('SUM(credit) as total_credit')
                                                )
                                                ->where('student_id', $request->student_id)
                                                ->where('saving_id', $request->saving_id)
                                                ->first();
                                $balance = $sum_trans->total_credit - $sum_trans->total_debit;
                                // get transaction
                                $transaction = Saving::select('debit')->where('id', $request->id)->first();
                                $balance = $balance + $transaction->debit; 
                                if ($balance < $request->amount)
                                {
                                    $isValid = false;
                                    $error_message = 'Saldo tabungan tidak mencukupi untuk penarikan.';
                                } 
                            } else {
                                // get balance
                                $transaction = Saving::select('credit')->where('id', $request->id)->first();
                                $first_credit = $transaction->credit;
                                if ($request->amount < $first_credit)
                                {
                                    $sum_trans = Saving::select(
                                                        DB::raw('SUM(debit) as total_debit'),
                                                        DB::raw('SUM(credit) as total_credit')
                                                    )
                                                    ->where('student_id', $request->student_id)
                                                    ->where('saving_id', $request->saving_id)
                                                    ->first();
                                    $total_deposit = $sum_trans->total_credit;
                                    $total_deposit = $total_deposit - $first_credit + $request->amount;
                                    if ($total_deposit < $sum_trans->total_debit)
                                    {
                                        $isValid = false;
                                        $error_message = 'Saldo tabungan akan menjadi NEGATIF.';
                                    }
                                }
                            }    
                            if ($isValid)
                            {
                                if ($request->transaction_type == 'credit')
                                {
                                    $debit = 0;
                                    $credit = $request->amount;
                                } else {
                                    $debit = $request->amount;
                                    $credit = 0;
                                }         
                                // transaction      
                                DB::transaction(function () use ($request, $transaction, $cash_account_id, $saving_type, $debit, $credit, $uuid) {
                                    // update saving
                                    $savingRequest = new Request();
                                    $savingRequest->merge([
                                        'id' => $request->id,
                                        'trans_date' => $request->trans_date,
                                        'debit' => $debit,
                                        'credit' => $credit,
                                        'remark' => $request->remark,
                                        'reason' => $request->reason,
                                        'employee' => auth()->user()->name,
                                        'logged' => auth()->user()->email,
                                    ]);
                                    $this->savingEloquent->update($savingRequest, $this->subject_student);
                                    // get journal
                                    $journal = Saving::find($request->id);
                                    // update journal
                                    Journal::where('id', $journal->journal_id)->update([
                                        'journal_date' => $request->trans_date,
                                        'remark' => $request->reason,
                                        'logged' => auth()->user()->email
                                    ]);
                                    // update journal detail
                                    JournalDetail::where('journal_id', $journal->journal_id)
                                        ->where('account_id', $cash_account_id->account_id)
                                        ->update([
                                            'debit' => $credit,
                                            'credit' => $debit,
                                            'uuid' => $uuid,
                                            'logged' => auth()->user()->email,
                                        ]);
                                    JournalDetail::where('journal_id', $journal->journal_id)
                                        ->where('account_id', $saving_type->credit_account)
                                        ->update([
                                            'debit' => $debit,
                                            'credit' => $credit,
                                            'uuid' => $uuid,
                                            'logged' => auth()->user()->email,
                                        ]);
                                });
                            }
                        } 
                    }
                    if (!$isValid)
                    {
                        throw new Exception($error_message, 1);
                    } else {
                        if ($request->cash_account != $cash_account_id->account_id)
                        {
                            JournalDetail::where('journal_id', $transaction->journal_id)
                                ->where('account_id', $cash_account_id->account_id)
                                ->update([
                                    'account_id' => $request->cash_account,
                                    'logged' => auth()->user()->email,
                                ]);
                        }
                        $response = $this->getResponse('store', '', $this->subject_student);
                    }
                }
            }
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), $this->subject_student);
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
        return response()->json($this->savingEloquent->show($id));
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return JSON
     */
    public function infoStudent(Request $request)
    {
        return $this->savingEloquent->info($request, 0);
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return JSON
     */
    public function dataStudent(Request $request)
    {
        return $this->savingEloquent->data($request, 0);
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
        $data['codes_cash'] = $this->codeEloquent->combobox(1,'1-1');
        $data['sections'] = Reference::where('category', 'hr_section')->get();
        $data['bookyear'] = $this->getActiveBookYear();
        if ($data['bookyear']->id > 0)
        {
            return view('finance::pages.savings.employee_saving', $data);
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
    public function storeEmployee(SavingRequest $request)
    {
        $validated = $request->validated();
        try 
        {
            $request->merge([
                'is_employee' => 1,
                'trans_date' => $this->formatDate($request->trans_date,'sys'),
                'debit' => $request->transaction_type == 'debit' ? $request->amount : 0,
                'credit' => $request->transaction_type == 'credit' ? $request->amount : 0,
                'employee' => auth()->user()->name,
                'logged' => auth()->user()->email,
            ]);
            if ($request->id < 1) 
            {
                // get balance
                $balance = Saving::select(DB::raw('SUM(credit - debit) as value'))
                            ->where('is_employee',1)
                            ->where('employee_id',$request->employee_id)
                            ->where('saving_id',$request->saving_id)
                            ->first();
                $saving_balance = is_null($balance->value) ? 0 : $balance->value;
                // get saving type
                $saving_type = SavingType::find($request->saving_id);
                // create cash no
                $bookyear = BookYear::find($request->bookyear_id);
                $number = $bookyear->number;
                $number += 1;
                $cash_no = sprintf('%06d', $number);
                // 
                if ($request->transaction_type == 'credit')
                {
                    /* deposit */
                    $remark_journal = 'Setoran tabungan ' . $saving_type->name . ' pegawai ' . $request->name . ' (' . $request->employee_no . ')';
                    // transaction
                    DB::transaction(function () use ($request, $cash_no, $remark_journal, $saving_balance, $saving_type) {
                        $uuid = strtotime('now');
                        // store to journal
                        $journal = $this->journalEloquent->store($request->trans_date, $remark_journal, $cash_no, $request->bookyear_id, 'savingdeposit', $request->department_id);
                        // store journal detail
                        $this->journalEloquent->createDetail($journal->id, $request->cash_account, $request->amount, 0, $uuid);
                        $this->journalEloquent->createDetail($journal->id, $saving_type->credit_account, 0, $request->amount, $uuid);
                        // increment number in bookyear                  
                        BookYear::where('id', $request->bookyear_id)->increment('number');
                        $request->merge([
                            'journal_id' => $journal->id,
                        ]);
                        // store saving
                        $this->savingEloquent->create($request, $this->subject_employee);
                    });
                    $response = $this->getResponse('store', '', $this->subject_employee);
                } else {
                    /* withdraw */
                    if ($request->amount > $saving_balance)
                    {
                        throw new Exception('Saldo tabungan tidak mencukupi untuk penarikan.', 1);
                    } else {
                        $remark_journal = 'Penarikan tabungan ' . $saving_type->name . ' pegawai ' . $request->employee_name . ' (' . $request->employee_no . ')';
                        DB::transaction(function () use ($request, $cash_no, $remark_journal, $saving_balance, $saving_type) {
                            $uuid = strtotime('now');
                            // store to journal
                            $journal = $this->journalEloquent->store($request->trans_date, $remark_journal, $cash_no, $request->bookyear_id, 'savingwithdrawal', $request->department_id);
                            // store journal detail
                            $this->journalEloquent->createDetail($journal->id, $request->cash_account, 0, $request->amount, $uuid);
                            $this->journalEloquent->createDetail($journal->id, $saving_type->credit_account, $request->amount, 0, $uuid);
                            // increment number in bookyear                  
                            BookYear::where('id', $request->bookyear_id)->increment('number');
                            $request->merge([
                                'journal_id' => $journal->id,
                            ]);
                            // store saving
                            $this->savingEloquent->create($request, $this->subject_employee);
                        });
                        $response = $this->getResponse('store', '', $this->subject_employee);
                    }
                }
            } else {
                if (empty($request->reason))
                {
                    throw new Exception('Alasan Ubah Data wajib diisi.', 1);
                } else {
                    $isValid = true;
                    $error_message = '';
                    // get saving
                    $transaction = Saving::find($request->id);
                    $cash_account_id = JournalDetail::select('finance.journal_details.account_id','finance.codes.code')
                                        ->join('finance.journals','finance.journals.id','=','finance.journal_details.journal_id')
                                        ->join('finance.codes','finance.codes.id','=','finance.journal_details.account_id')
                                        ->join('finance.code_categories','finance.code_categories.id','=','finance.codes.category_id')
                                        ->where('finance.journal_details.journal_id', $transaction->journal_id)
                                        ->where('finance.code_categories.category', 'HARTA')
                                        ->first();
                    // get saving type
                    $saving_type = SavingType::find($request->saving_id);
                    $uuid = strtotime('now');
                    // check reverse transaction type
                    if ($transaction->transaction_type != $request->transaction_type)
                    {
                        // from credit to debit
                        if ($transaction->transaction_type == 'credit')
                        {
                            // get balance
                            $saving_balance = $this->savingEloquent->getBalance($request->saving_id, $request->employee_id, 1);
                            $old_value = JournalDetail::where('journal_id', $transaction->journal_id)->where('debit','>',0)->first()->debit;
                            if ( (($saving_balance - $old_value) - $request->amount) < 0 )
                            {
                                $isValid = false;
                                $error_message = 'Saldo tabungan tidak mencukupi.';
                            } else {
                                // check if amount not change
                                if (
                                    ($transaction->credit == 0 && $request->amount == $transaction->debit) || 
                                    ($transaction->debit == 0 && $request->amount == $transaction->credit)
                                )
                                {
                                    DB::transaction(function () use ($request, $transaction, $saving_type, $uuid) {
                                        // update journal
                                        Journal::where('id', $transaction->journal_id)->update([
                                            'journal_date' => $request->trans_date,
                                            'transaction' => 'Penarikan tabungan ' . $saving_type->name . ' pegawai ' . $request->name . ' (' . $request->employee_no . ')',
                                            'source' => 'savingwithdrawal',
                                            'logged' => auth()->user()->email
                                        ]);
                                        JournalDetail::where('journal_id', $transaction->journal_id)->delete();
                                        // create journal detail
                                        $this->journalEloquent->createDetail($transaction->journal_id, $request->cash_account, 0, $request->amount, $uuid);
                                        $this->journalEloquent->createDetail($transaction->journal_id, $saving_type->credit_account, $request->amount, 0, $uuid);
                                        // update info
                                        $savingRequest = new Request();
                                        $savingRequest->merge([
                                            'id' => $request->id,
                                            'trans_date' => $request->trans_date,
                                            'debit' => $transaction->credit,
                                            'credit' => 0,
                                            'remark' => $request->remark,
                                            'reason' => $request->reason,
                                            'transaction_type' => 'debit',
                                            'employee' => auth()->user()->name,
                                            'logged' => auth()->user()->email,
                                        ]);
                                        $this->savingEloquent->update($savingRequest, $this->subject_employee);
                                    });
                                } else {
                                    // amount changed
                                    DB::transaction(function () use ($request, $transaction, $saving_type, $uuid) {
                                        // update journal
                                        Journal::where('id', $transaction->journal_id)->update([
                                            'journal_date' => $request->trans_date,
                                            'remark' => $request->reason,
                                            'logged' => auth()->user()->email
                                        ]);
                                        // update journal detail
                                        JournalDetail::where('journal_id', $transaction->journal_id)
                                            ->where('account_id', $transaction->cash_account)
                                            ->update([
                                                'account_id' => $request->cash_account,
                                                'debit' => 0,
                                                'credit' => $request->amount,
                                                'uuid' => $uuid,
                                                'logged' => auth()->user()->email,
                                            ]);
                                        JournalDetail::where('journal_id', $transaction->journal_id)
                                            ->where('account_id', $transaction->credit_account)
                                            ->update([
                                                'account_id' => $saving_type->credit_account,
                                                'debit' => $request->amount,
                                                'credit' => 0,
                                                'uuid' => $uuid,
                                                'logged' => auth()->user()->email,
                                            ]);
                                        // update info
                                        $savingRequest = new Request();
                                        $savingRequest->merge([
                                            'id' => $request->id,
                                            'trans_date' => $request->trans_date,
                                            'debit' => $request->amount,
                                            'credit' => 0,
                                            'remark' => $request->remark,
                                            'reason' => $request->reason,
                                            'transaction_type' => 'debit',
                                            'employee' => auth()->user()->name,
                                            'logged' => auth()->user()->email,
                                        ]);
                                        $this->savingEloquent->update($savingRequest, $this->subject_employee);
                                    });
                                }
                            }
                        } else {
                            // from debit to credit
                            // check if amount not change
                            if (
                                ($transaction->debit == 0 && $request->amount == $transaction->credit) || 
                                ($transaction->credit == 0 && $request->amount == $transaction->debit)
                            )
                            {
                                DB::transaction(function () use ($request, $transaction, $saving_type, $uuid) {
                                    // update journal detail
                                    JournalDetail::where('journal_id', $transaction->journal_id)
                                        ->where('account_id', $transaction->cash_account)
                                        ->update([
                                            'account_id' => $request->cash_account,
                                            'debit' => $transaction->debit,
                                            'credit' => 0,
                                            'uuid' => $uuid,
                                            'logged' => auth()->user()->email,
                                        ]);
                                    JournalDetail::where('journal_id', $transaction->journal_id)
                                        ->where('account_id', $transaction->credit_account)
                                        ->update([
                                            'account_id' => $saving_type->credit_account,
                                            'debit' => 0,
                                            'credit' => $transaction->debit,
                                            'uuid' => $uuid,
                                            'logged' => auth()->user()->email,
                                        ]);
                                    // update info
                                    $savingRequest = new Request();
                                    $savingRequest->merge([
                                        'id' => $request->id,
                                        'trans_date' => $request->trans_date,
                                        'remark' => $request->remark,
                                        'reason' => $request->reason,
                                        'debit' => 0,
                                        'credit' => $transaction->debit,
                                        'transaction_type' => 'credit',
                                        'employee' => auth()->user()->name,
                                        'logged' => auth()->user()->email,
                                    ]);
                                    $this->savingEloquent->update($savingRequest, $this->subject_employee);
                                });
                            } else {
                                // amount changed
                                DB::transaction(function () use ($request, $transaction, $saving_type, $uuid) {
                                    // update journal detail
                                    JournalDetail::where('journal_id', $transaction->journal_id)
                                        ->where('account_id', $transaction->cash_account)
                                        ->update([
                                            'account_id' => $request->cash_account,
                                            'debit' => $request->amount,
                                            'credit' => 0,
                                            'uuid' => $uuid,
                                            'logged' => auth()->user()->email,
                                        ]);
                                    JournalDetail::where('journal_id', $transaction->journal_id)
                                        ->where('account_id', $transaction->credit_account)
                                        ->update([
                                            'account_id' => $saving_type->credit_account,
                                            'debit' => 0,
                                            'credit' => $request->amount,
                                            'uuid' => $uuid,
                                            'logged' => auth()->user()->email,
                                        ]);
                                    // update info
                                    $savingRequest = new Request();
                                    $savingRequest->merge([
                                        'id' => $request->id,
                                        'trans_date' => $request->trans_date,
                                        'debit' => 0,
                                        'credit' => $request->amount,
                                        'remark' => $request->remark,
                                        'reason' => $request->reason,
                                        'transaction_type' => 'credit',
                                        'employee' => auth()->user()->name,
                                        'logged' => auth()->user()->email,
                                    ]);
                                    $this->savingEloquent->update($savingRequest, $this->subject_employee);
                                });
                            }
                            // update journal
                            Journal::where('id', $transaction->journal_id)->update([
                                'journal_date' => $request->trans_date,
                                'transaction' => 'Setoran tabungan ' . $saving_type->name . ' pegawai ' . $request->name . ' (' . $request->employee_no . ')',
                                'source' => 'savingdeposit',
                                'logged' => auth()->user()->email
                            ]);
                        }
                    } else {
                        if (
                            ($transaction->debit == 0 && $request->amount == $transaction->credit) || 
                            ($transaction->credit == 0 && $request->amount == $transaction->debit)
                        )
                        {
                            // update info
                            $savingRequest = new Request();
                            $savingRequest->merge([
                                'id' => $request->id,
                                'remark' => $request->remark,
                                'reason' => $request->reason,
                                'employee' => auth()->user()->name,
                                'logged' => auth()->user()->email,
                            ]);
                            $this->savingEloquent->update($savingRequest, $this->subject_employee);
                            // update journal
                            Journal::where('id', $transaction->journal_id)->update([
                                'journal_date' => $request->trans_date,
                                'remark' => $request->reason,
                                'logged' => auth()->user()->email
                            ]);
                        } else {
                            if ($request->transaction_type == 'debit')
                            {
                                // get balance
                                $sum_trans = Saving::select(
                                                    DB::raw('SUM(debit) as total_debit'),
                                                    DB::raw('SUM(credit) as total_credit')
                                                )
                                                ->where('employee_id', $request->employee_id)
                                                ->where('saving_id', $request->saving_id)
                                                ->first();
                                $balance = $sum_trans->total_credit - $sum_trans->total_debit;
                                // get transaction
                                $transaction = Saving::select('debit')->where('id', $request->id)->first();
                                $balance = $balance + $transaction->debit; 
                                if ($balance < $request->amount)
                                {
                                    $isValid = false;
                                    $error_message = 'Saldo tabungan tidak mencukupi untuk penarikan.';
                                } 
                            } else {
                                // get balance
                                $transaction = Saving::select('credit')->where('id', $request->id)->first();
                                $first_credit = $transaction->credit;
                                if ($request->amount < $first_credit)
                                {
                                    $sum_trans = Saving::select(
                                                    DB::raw('SUM(debit) as total_debit'),
                                                    DB::raw('SUM(credit) as total_credit')
                                                )
                                                ->where('employee_id', $request->employee_id)
                                                ->where('saving_id', $request->saving_id)
                                                ->first();
                                    $total_deposit = $sum_trans->total_credit;
                                    $total_deposit = $total_deposit - $first_credit + $request->amount;
                                    if ($total_deposit < $sum_trans->total_debit)
                                    {
                                        $isValid = false;
                                        $error_message = 'Saldo tabungan akan menjadi NEGATIF.';
                                    }
                                }
                            }    
                            if ($isValid)
                            {
                                if ($request->transaction_type == 'credit')
                                {
                                    $debit = 0;
                                    $credit = $request->amount;
                                } else {
                                    $debit = $request->amount;
                                    $credit = 0;
                                }         
                                // transaction      
                                DB::transaction(function () use ($request, $transaction, $cash_account_id, $saving_type, $debit, $credit, $uuid) {
                                    // update saving
                                    $savingRequest = new Request();
                                    $savingRequest->merge([
                                        'id' => $request->id,
                                        'trans_date' => $request->trans_date,
                                        'debit' => $debit,
                                        'credit' => $credit,
                                        'remark' => $request->remark,
                                        'reason' => $request->reason,
                                        'employee' => auth()->user()->name,
                                        'logged' => auth()->user()->email,
                                    ]);
                                    $this->savingEloquent->update($savingRequest, $this->subject_employee);
                                    // get journal
                                    $journal = Saving::select('journal_id')->where('id', $request->id)->first();
                                    // update journal
                                    Journal::where('id', $journal->journal_id)->update([
                                        'journal_date' => $request->trans_date,
                                        'remark' => $request->reason,
                                        'logged' => auth()->user()->email
                                    ]);
                                    // update journal detail
                                    JournalDetail::where('journal_id', $journal->journal_id)
                                        ->where('account_id', $cash_account_id->account_id)
                                        ->update([
                                            'debit' => $credit,
                                            'credit' => $debit,
                                            'uuid' => $uuid,
                                            'logged' => auth()->user()->email,
                                        ]);
                                    JournalDetail::where('journal_id', $journal->journal_id)
                                        ->where('account_id', $saving_type->credit_account)
                                        ->update([
                                            'debit' => $debit,
                                            'credit' => $credit,
                                            'uuid' => $uuid,
                                            'logged' => auth()->user()->email,
                                        ]);
                                });
                            }
                        } 
                    }
                    if (!$isValid)
                    {
                        throw new Exception($error_message, 1);
                    } else {
                        if ($request->cash_account != $cash_account_id->account_id)
                        {
                            JournalDetail::where('journal_id', $transaction->journal_id)
                                ->where('account_id', $cash_account_id->account_id)
                                ->update([
                                    'account_id' => $request->cash_account,
                                    'logged' => auth()->user()->email,
                                ]);
                        }
                        $response = $this->getResponse('store', '', $this->subject_employee);
                    }
                }
            }
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), $this->subject_employee);
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
        return response()->json($this->savingEloquent->show($id));
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return JSON
     */
    public function infoEmployee(Request $request)
    {
        return $this->savingEloquent->info($request, 1);
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return JSON
     */
    public function dataEmployee(Request $request)
    {
        return $this->savingEloquent->data($request, 1);
    }

    //

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function printPdf(Request $request)
    {
        $data['profile'] = $this->getInstituteProfile();
        $data['data'] = json_decode($request->data);
        $type = $data['data']->is_employee == 0 ? 'santri' : 'pegawai';
        $data['bookyear'] = BookYear::select('book_year')->where('id', $data['data']->bookyear_id)->first();
        $saving_types = explode('-', $data['data']->saving_type);
        $data['saving_type'] = SavingType::find($saving_types[0]);
        $request->merge([
            'saving_type' => $saving_types[0],
            'bookyear_id' => $data['data']->bookyear_id,
            'person_id' => $data['data']->person_id,
        ]);
        $data['summary'] = $this->savingEloquent->info($request, $data['data']->is_employee);
        if ($data['data']->is_employee == 0)
        {
            $data['person'] = Students::select('mobile','phone','address')->where('id', $data['data']->person_id)->first();
            $subject = $this->subject_student;
        } else {
            $data['person'] = Employee::select('mobile','phone','address')->where('id', $data['data']->person_id)->first();
            $subject = $this->subject_employee;
        }
        //
        $view = View::make('finance::pages.savings.saving_trans_pdf', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($subject)->snake();
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortrait($hashfile, $filename);
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
        $type = $payload['is_employee'] == 0 ? 'santri' : 'pegawai';
        $subject = $payload['is_employee'] == 0 ? $this->subject_student : $this->subject_employee;
        $data['transactions'] = Saving::select(
                                    'finance.savings.debit',
                                    'finance.savings.credit',
                                    'finance.savings.employee',
                                    'finance.savings.trans_date',
                                    'finance.journals.transaction',
                                    'finance.journals.cash_no',
                                    'finance.journals.bookyear_id',
                                )
                                ->where('finance.savings.id', $payload['transaction_id'])
                                ->join('finance.journals','finance.journals.id','=','finance.savings.journal_id')
                                ->get()->map(function($model){
                                    $model['trans_date'] = $this->formatDate($model['trans_date'],'iso');
                                    $model['cash_no'] = $this->getPrefixBookYear($model->bookyear_id) . $model->cash_no;
                                    return $model;
                                })[0];
        $total = $data['transactions']['debit'] > 0 ? $data['transactions']['debit'] : $data['transactions']['credit'];
        $data['values'] = array(
            'total' => 'Rp'.number_format($total,2),
            'counted' => $total > 0 ? $this->counted(str_replace(',','',str_replace('Rp', '', $total))) : 'nol',
        );
        $data['requests'] = $payload;
        // 
        $view = View::make('finance::pages.receipts.receipt_saving_pdf', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($subject)->snake();
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortrait($hashfile, $filename);
        echo $filename;
    }
}
