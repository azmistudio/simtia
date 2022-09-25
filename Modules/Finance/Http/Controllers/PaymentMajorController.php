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
use App\Http\Traits\ReferenceTrait;
use Modules\Finance\Entities\PaymentMajor;
use Modules\Finance\Entities\ReceiptMajor;
use Modules\Finance\Entities\BookYear;
use Modules\Finance\Entities\ReceiptType;
use Modules\Finance\Entities\Journal;
use Modules\Finance\Entities\JournalDetail;
use Modules\Academic\Entities\Students;
use Modules\Academic\Entities\AdmissionProspect;
use Modules\Finance\Repositories\Receipt\PaymentMajorEloquent;
use Modules\Finance\Repositories\Receipt\ReceiptMajorEloquent;
use Modules\Finance\Repositories\Journal\JournalEloquent;
use Modules\Finance\Http\Requests\PaymentMajorRequest;
use View;
use Exception;

class PaymentMajorController extends Controller
{
    use HelperTrait;
    use DepartmentTrait;
    use ReferenceTrait;

    private $subject = 'Data Besar Pembayaran';

    function __construct(
        PaymentMajorEloquent $paymentMajorEloquent,
        ReceiptMajorEloquent $receiptMajorEloquent,
        JournalEloquent $journalEloquent
    )
    {
        $this->paymentMajorEloquent = $paymentMajorEloquent;
        $this->receiptMajorEloquent = $receiptMajorEloquent;
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
        $data['departments'] = $this->listDepartment();
        $data['categories'] = DB::table('finance.receipt_categories')->select('id','code','category')->whereIn('code', ['JTT','CSWJB'])->orderBy('order')->get();
        return view('finance::pages.receipts.payment_major', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(PaymentMajorRequest $request)
    {
        $validated = $request->validated();
        try 
        {
            $categories = explode("-", $request->category_id);
            $category_code = $categories[1];
            $instalment = $request->first_instalment ?: 1;
            $request->merge([
                'category_id' => $categories[0],
                'class' => $request->has('class') ? array_filter($request->class) : 0,
                'group' => $request->has('group') ? array_filter($request->group) : 0,
                'first_instalment' => $request->has('first_instalment') ? $request->first_instalment : 1,
                'logged' => auth()->user()->email,
            ]);
            if ($request->id < 1) 
            {
                if ($category_code == 'JTT')
                {
                    if (count($request->class) < 1) 
                    {
                        $response = $this->getResponse('warning', 'Kelas wajib dipilih (minimal 1).');
                    } else {
                        $isValid = true;
                        // validate month
                        if (!$request->has('period_month'))
                        {
                            $isValid = false;
                            $response = $this->getResponse('warning', 'Periode Bayar wajib dipilih.');
                        }
                        // get Active's Book Year
                        $bookyear = BookYear::where('is_active', 1)->firstOr( function () {
                            $isValid = false;
                            $response = $this->getResponse('warning', 'Tidak ada Tahun Buku yang aktif.');
                        });
                        $bookyear_id = $bookyear->id;
                        $bookyear_prefix = $bookyear->prefix;
                        $bookyear_number = $bookyear->number;
                        // get Students
                        $students = Students::select('id')->whereIn('class_id', $request->class)->where('is_active',1)->get();
                        if (count($students) < 1) 
                        {
                            $isValid = false;
                            $response = $this->getResponse('warning', 'Tidak ada Santri yang ditemukan di Kelas yang dipilih.');
                        }
                        $listStudents = array();
                        foreach ($students as $student) 
                        {
                            $majors = PaymentMajor::where('student_id', $student->id)
                                        ->where('receipt_id', $request->receipt_id)
                                        ->where('bookyear_id', $bookyear_id)
                                        ->where('period_month', $request->period_month)
                                        ->where('period_year', $request->period_year)
                                        ->count();
                            if ($majors == 0) 
                            {
                                $listStudents[] = $student->id;
                            }
                        }
                        $nStudents = count($listStudents);
                        if ($nStudents == 0)
                        {
                            $isValid = false;
                            $response = $this->getResponse('warning', 'Tidak ada Santri yang belum diatur Besar Pembayarannya.');
                        } else {
                            // transaction begin
                            DB::transaction(function () use ($request, $isValid, $nStudents, $listStudents, $bookyear_id, $bookyear_number, $bookyear_prefix, $instalment) {
                                $uuid = strtotime('now');
                                // get COA based on Receipt Type
                                $accounts = ReceiptType::select(
                                                DB::raw('cash_account as cash'),
                                                DB::raw('receipt_account as receipt'),
                                                DB::raw('receivable_account as receivable'),
                                                DB::raw('discount_account as discount'),
                                            )
                                            ->where('id', $request->receipt_id)
                                            ->first();
                                $cash_account = $accounts->cash;
                                $receipt_account = $accounts->receipt;
                                $receivable_account = $accounts->receivable;
                                $discount_account = $accounts->discount;
                                $name_account = $accounts->name;
                                // loop students
                                for ($i=0; $isValid && $i < $nStudents; $i++) 
                                { 
                                    $studentId = $listStudents[$i];
                                    $student = Students::select('student_no','name')->where('id', $studentId)->first();
                                    $bookyear_number += 1;
                                    $cash_no = sprintf('%06d', $bookyear_number);
                                    $remark = 'Pendataan Besar Pembayaran ' . $name_account . 'santri ' . Str::title($student->name) . ' (' . $student->student_no . ') ' . ' Periode ' . $this->getMonthName($request->period_month) . '/' . $request->period_year;
                                    // is paid
                                    $is_paid = 0;
                                    if ($request->amount == 0)
                                    {
                                        $is_paid = 2; // free of charge
                                    }
                                    $journal_id = 0;
                                    if ($isValid)
                                    {
                                        // store to journal
                                        $journal = $this->journalEloquent->store(date('Y-m-d'), $remark, $cash_no, $bookyear_id, 'major_jtt', $request->department_id);
                                        $journal_id = $journal->id;
                                        // store major 
                                        $request->merge([
                                            'student_id' => $studentId,
                                            'journal_id' => $journal_id,
                                            'bookyear_id' => $bookyear_id,
                                            'is_paid' => $is_paid,
                                            'is_prospect' => 0,
                                            'period_month' => $request->period_month,
                                            'period_year' => $request->period_year
                                        ]);
                                        $major = $this->paymentMajorEloquent->create($request, $this->subject);
                                        $major_id = $major->id;
                                        // store journal detail
                                        $this->journalEloquent->createDetail($journal_id, $receivable_account, $request->amount, 0, $uuid);
                                        $this->journalEloquent->createDetail($journal_id, $receipt_account, 0, $request->amount, $uuid);
                                        // check first instalment
                                        if ($instalment == 2)
                                        {
                                            // set first instalment = 0
                                            // store to journal
                                            $journal_ = $this->journalEloquent->store(date('Y-m-d'), $remark, $cash_no, $bookyear_id, 'receipt_jtt', $request->department_id);
                                            $journal_id_ = $journal_->id;
                                            // store journal detail
                                            $this->journalEloquent->createDetail($journal_id_, $cash_account, 0, 0, $uuid);
                                            $this->journalEloquent->createDetail($journal_id_, $receivable_account, 0, 0, $uuid);
                                            // store receipt major
                                            $receiptMajorRequest = new Request();
                                            $receiptMajorRequest->merge([
                                                'trans_date' => date('Y-m-d'),
                                                'major_id' => $major_id,
                                                'journal_id' => $journal_id_,
                                                'total' => 0,
                                                'employee_id' => auth()->user()->id,
                                                'first_instalment' => $instalment,
                                                'is_prospect' => 0,
                                                'discount_amount' => 0,
                                                'logged' => auth()->user()->email,
                                            ]);
                                            $this->receiptMajorEloquent->create($receiptMajorRequest, $this->subject);
                                        }
                                    }
                                }
                                // increment amount in bookyear
                                BookYear::where('id', $bookyear_id)->update(['number' => $bookyear_number]);
                            });
                            $response = $this->getResponse('store', '', $this->subject);
                        }
                    }
                } else {
                    if (count($request->group) < 1) 
                    {
                        $response = $this->getResponse('warning', 'Kelompok wajib dipilih (minimal 1).');
                    } else {
                        $isValid = true;
                        // get Active's Book Year
                        $bookyear = BookYear::where('is_active', 1)->firstOr( function () {
                            $isValid = false;
                            $response = $this->getResponse('warning', 'Tidak ada Tahun Buku yang aktif.');
                        });
                        $bookyear_id = $bookyear->id;
                        $bookyear_prefix = $bookyear->prefix;
                        $bookyear_number = $bookyear->number;
                        // get prospect students
                        $students = AdmissionProspect::select('id')->whereIn('prospect_group_id', $request->group)->where('is_active', 1)->get();
                        if (count($students) < 1) 
                        {
                            $isValid = false;
                            $response = $this->getResponse('warning', 'Tidak ada Calon Santri yang ditemukan di Kelompok yang dipilih.');
                        }
                        $listStudents = array();
                        foreach ($students as $student) 
                        {
                            $majors = PaymentMajor::where('prospect_student_id', $student->id)->where('receipt_id', $request->receipt_id)->where('bookyear_id', $bookyear_id)->count();
                            if ($majors == 0) 
                            {
                                $listStudents[] = $student->id;
                            }
                        }
                        $nStudents = count($listStudents);
                        if ($nStudents == 0)
                        {
                            $isValid = false;
                            $response = $this->getResponse('warning', 'Tidak ada Calon Santri yang belum diatur Besar Pembayarannya.');
                        } else {
                            DB::transaction(function () use ($request, $isValid, $nStudents, $listStudents, $bookyear_id, $bookyear_number, $bookyear_prefix, $instalment) {
                                $uuid = strtotime('now');
                                // get COA based on Receipt Type
                                $accounts = ReceiptType::select(
                                        DB::raw('cash_account as cash'),
                                        DB::raw('receipt_account as receipt'),
                                        DB::raw('receivable_account as receivable'),
                                        DB::raw('discount_account as discount'),
                                    )->where('id', $request->receipt_id)->first();
                                $cash_account = $accounts->cash;
                                $receipt_account = $accounts->receipt;
                                $receivable_account = $accounts->receivable;
                                $discount_account = $accounts->discount;
                                $name_account = $accounts->name;
                                // loop students
                                for ($i=0; $isValid && $i < $nStudents; $i++) 
                                { 
                                    $studentId = $listStudents[$i];
                                    $student = AdmissionProspect::select('registration_no','name')->where('id', $studentId)->first();
                                    $bookyear_number += 1;
                                    $cash_no = sprintf('%06d', $bookyear_number);
                                    $remark = 'Pendataan Besar Pembayaran ' . $name_account . 'santri ' . Str::title($student->name) . ' (' . $student->registration_no . ')';
                                    // is paid
                                    $is_paid = 0;
                                    if ($request->amount == 0)
                                    {
                                        $is_paid = 2; // free of charge
                                    }
                                    $journal_id = 0;
                                    if ($isValid)
                                    {
                                        // store to journal
                                        $journal = $this->journalEloquent->store(date('Y-m-d'), $remark, $cash_no, $bookyear_id, 'major_jtt_prospect', $request->department_id);
                                        $journal_id = $journal->id;
                                        // store major 
                                        $request->merge([
                                            'prospect_student_id' => $studentId,
                                            'journal_id' => $journal_id,
                                            'bookyear_id' => $bookyear_id,
                                            'is_paid' => $is_paid,
                                            'is_prospect' => 1,
                                            'period_year' => dat('Y')
                                        ]);
                                        $major = $this->paymentMajorEloquent->create($request, $this->subject);
                                        $major_id = $major->id;
                                        // store journal detail
                                        $this->journalEloquent->createDetail($journal_id, $receivable_account, $request->amount, 0, $uuid);
                                        $this->journalEloquent->createDetail($journal_id, $receipt_account, 0, $request->amount, $uuid);
                                        // check first instalment
                                        if ($instalment == 2)
                                        {
                                            // set first instalment = 0
                                            // store to journal
                                            $journal_ = $this->journalEloquent->store(date('Y-m-d'), $remark, $cash_no, $bookyear_id, 'receipt_jtt_prospect', $request->department_id);
                                            $journal_id_ = $journal_->id;
                                            // store journal detail
                                            $this->journalEloquent->createDetail($journal_id_, $cash_account, 0, 0, $uuid);
                                            $this->journalEloquent->createDetail($journal_id_, $receivable_account, 0, 0, $uuid);
                                            // store receipt major
                                            $receiptMajorRequest = new Request();
                                            $receiptMajorRequest->merge([
                                                'trans_date' => date('Y-m-d'),
                                                'major_id' => $major_id,
                                                'journal_id' => $journal_id_,
                                                'total' => 0,
                                                'employee_id' => auth()->user()->id,
                                                'first_instalment' => $instalment,
                                                'is_prospect' => 1,
                                                'discount_amount' => 0,
                                                'logged' => auth()->user()->email,
                                            ]);
                                            $this->receiptMajorEloquent->create($receiptMajorRequest, $this->subject);
                                        }
                                    }
                                }
                                // increment amount in bookyear
                                BookYear::where('id', $bookyear_id)->update(['number' => $bookyear_number]);
                            });
                            $response = $this->getResponse('store', '', $this->subject);
                        }
                    }
                }
            } else {
                if (empty($request->reason_paid))
                {
                    $response = $this->getResponse('warning', 'Alasan Ubah Data wajib diisi.');
                } else {
                    // get existing transaction
                    $payment_major = PaymentMajor::find($request->id);
                    // get COA based on receipt type
                    $accounts = ReceiptType::find($request->receipt_id);
                    $amount = $request->amount;
                    // get total paid 
                    $total_paid = ReceiptMajor::select(DB::raw('SUM(total) as total'), DB::raw('COUNT(id) as count_id'))->where('major_id', $request->id)->first();
                    $continue = true;
                    if ($total_paid->total > $amount)
                    {
                        throw new Exception('Maaf, besar pembayaran yang harus dilunasi lebih kecil dari jumlah pembayaran cicilan yang telah dilakukan.', 1);
                        $amount = 0;
                        $continue = false;
                    } 
                    $diff = 0;
                    $journal_jtt = 0;
                    $bookyear_jtt = 0;
                    $amount_jtt = 0;
                    $is_paid = 0;
                    if ($continue)
                    {   
                        $journal_jtt = $payment_major->journal_id;
                        $bookyear_jtt = $payment_major->bookyear_id;
                        $amount_jtt = $payment_major->amount;
                        $diff = $amount - $amount_jtt;
                        if ($amount == 0)
                        {
                            $is_paid = 2; // free
                        } elseif ($total_paid->total == $amount) {
                            $is_paid = 1; // paid
                        }
                    }
                    if ($continue && $diff == 0)
                    {
                        DB::transaction(function () use ($request, $payment_major) {
                            // update payment major
                            $paymentMajorRequest = new Request();
                            $paymentMajorRequest->merge([
                                'id' => $request->id,
                                'instalment' => $request->instalment,
                                'remark' => $request->remark_paid,
                                'reason' => $request->reason_paid,
                                'logged' => auth()->user()->email,
                            ]);
                            $this->paymentMajorEloquent->update($paymentMajorRequest, $this->subject);
                        });
                        $continue = false;
                    }
                    if ($continue)
                    {
                        DB::transaction(function () use ($request, $amount, $amount_jtt, $diff, $is_paid, $bookyear_jtt, $journal_jtt, $accounts, $payment_major) {
                            $uuid = strtotime('now');
                            $paymentMajorRequest = new Request();
                            $paymentMajorRequest->merge([
                                'id' => $request->id,
                                'amount' => $request->amount,
                                'instalment' => $request->instalment,
                                'is_paid' => $is_paid,
                                'remark' => $request->remark_paid,
                                'reason' => $request->reason_paid,
                                'logged' => auth()->user()->email,
                            ]);
                            $this->paymentMajorEloquent->update($paymentMajorRequest, $this->subject);
                            // adjustment bookyear
                            $create_adjustment = false;
                            if ($bookyear_jtt == 0)
                            {
                                $create_adjustment = ($total_paid->count_id > 0);
                            } else {
                                $create_adjustment = ($request->bookyear_id != $bookyear_jtt);
                            }
                            //
                            if ($create_adjustment)
                            {
                                // create adjustment journal
                                $bookyear = BookYear::where('id', $request->bookyear_id)->first();
                                $number = $bookyear->number;
                                $number += 1;
                                $cash_no = sprintf('%06d', $number);
                                $remark_journal = 'Jurnal penyesuaian perubahan besar pembayaran '.$accounts->name.' santri '.$request->student_name.' ('.$request->student_no.')';
                                // store to journal
                                $journal = $this->journalEloquent->store($remark_journal, $cash_no, $bookyear_id, 'major_jtt', $request->department_id);
                                // store to journal detail
                                if ($diff > 0)
                                {
                                    $this->journalEloquent->createDetail($journal_id, $accounts->receivable_account, $diff, 0, $uuid);
                                    $this->journalEloquent->createDetail($journal_id, $accounts->receipt_account, 0, $diff, $uuid);
                                } else {
                                    $this->journalEloquent->createDetail($journal_id, $accounts->receivable_account, 0, abs($diff), $uuid);
                                    $this->journalEloquent->createDetail($journal_id, $accounts->receipt_account, abs($diff), 0, $uuid);
                                }
                                // update bookyear number
                                BookYear::where('id', $request->bookyear_id)->increment('number');
                            } elseif ($journal_jtt > 0) {
                                $this->journalEloquent->updateDetail($journal_jtt, $accounts->receivable_account, $amount, $amount_jtt, $uuid, true);
                                $this->journalEloquent->updateDetail($journal_jtt, $accounts->receipt_account, $amount, $amount_jtt, $uuid, false);
                            }
                        });
                    }
                    $response = $this->getResponse('store', '', $this->subject);
                }
            }
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), $this->subject);
        }
        return response()->json($response);
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return JSON
     */
    public function dataStudent(Request $request)
    {
        return $this->paymentMajorEloquent->dataStudent($request);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return response()->json(PaymentMajor::find($id));
    }

    /**
     * Show the specified resource.
     * @param Request $request
     * @return JSON
     */
    public function detail(Request $request)
    {
        $query = PaymentMajor::where('department_id', $request->department_id)->where('category_id', $request->category_id)->where('receipt_id', $request->receipt_id);
        if ($request->category_id == 1)
        {
            $query = $query->where('student_id', $request->student_id);
        } else {
            $query = $query->where('prospect_student_id', $request->student_id);
        }
        return $query->first();
    }

    /**
     * Show the specified resource.
     * @param Request $request
     * @return JSON
     */
    public function periodPayment(Request $request)
    {
        $periods = explode('/', $request->schoolyear);
        $bookyear = BookYear::where('book_year', $periods[0])->first();
        $query = PaymentMajor::select('period_month','period_year')
                    ->where('department_id', $request->department_id)
                    ->where('category_id', $request->category_id)
                    ->where('receipt_id', $request->receipt_id)
                    ->where('bookyear_id', $bookyear->id)
                    ->groupBy('period_month','period_year')
                    ->orderBy('period_month')
                    ->get()->map(function($model)
                    {
                        $model['id'] = $model->period_month.$model->period_year;
                        $model['text'] = 'Periode ' . $this->getMonthName($model->period_month) . ' / ' . $model->period_year; 
                        return $model->only(['id','text']);
                    });
        return response()->json($query);
    }
}
