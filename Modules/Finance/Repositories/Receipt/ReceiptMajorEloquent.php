<?php

namespace Modules\Finance\Repositories\Receipt;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Http\Traits\ReferenceTrait;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use Modules\Finance\Entities\BookYear;
use Modules\Finance\Entities\PaymentMajor;
use Modules\Finance\Entities\ReceiptType;
use Modules\Finance\Entities\ReceiptMajor;
use Modules\Finance\Entities\JournalDetail;
use Carbon\Carbon;

class ReceiptMajorEloquent implements ReceiptMajorRepository
{

    use ReferenceTrait;
	use AuditLogTrait;
	use HelperTrait;

	public function create(Request $request, $subject)
	{
		$payload = $request->all();
		$this->logActivity($request, 0, $subject, 'Tambah');
		return ReceiptMajor::create($payload);
	}

    public function update(Request $request, $subject)
    {
        $payload = Arr::except($request->all(), ['created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah');
        // 
        return ReceiptMajor::where('id', $payload['id'])->update($payload);
    }

	public function show($category_id, $id)
	{
		$journal_details = JournalDetail::orderBy('id');
        if ($category_id == 1)
        {
            return ReceiptMajor::select(
                        'finance.receipt_majors.id',
                        'finance.receipt_majors.major_id',
                        'finance.payment_majors.department_id',
                        DB::raw('academic.students.id as student_id'),
                        'finance.payment_majors.bookyear_id',
                        'finance.payment_majors.is_prospect',
                        'academic.students.student_no',
                        DB::raw('INITCAP(academic.students.name) as student_name'),
                        DB::raw("CONCAT(academic.grades.grade, ' - ', UPPER(academic.classes.class)) as class_name"),
                        DB::raw('finance.receipt_majors.total as instalment'),
                        DB::raw('finance.receipt_majors.discount_amount as discount'),
                        DB::raw("to_char(finance.receipt_majors.trans_date,'dd/mm/yyyy') as journal_date"),
                        DB::raw('journal_details.account_id as cash_account'),
                        'finance.receipt_majors.remark'
                    )
                    ->join('finance.payment_majors','finance.payment_majors.id','=','finance.receipt_majors.major_id')
                    ->join('finance.journals','finance.journals.id','=','finance.receipt_majors.journal_id')
                    ->joinSub($journal_details, 'journal_details', function ($join) {
                        $join->on('finance.receipt_majors.journal_id','=','journal_details.journal_id');
                    })
                    ->join('finance.codes','finance.codes.id','=','journal_details.account_id')
                    ->join('academic.students','academic.students.id','=','finance.payment_majors.student_id')
                    ->join('academic.classes','academic.classes.id','=','academic.students.class_id')
                    ->join('academic.grades','academic.grades.id','=','academic.classes.grade_id')
                    ->join('public.departments','public.departments.id','=','finance.payment_majors.department_id')
                    ->where('finance.receipt_majors.id', $id)
                    ->first();
        } else {
            return ReceiptMajor::select(
                        'finance.receipt_majors.id',
                        'finance.receipt_majors.major_id',
                        'finance.payment_majors.department_id',
                        'finance.payment_majors.bookyear_id',
                        'finance.payment_majors.is_prospect',
                        DB::raw('academic.prospect_students.registration_no as student_no'),
                        DB::raw('INITCAP(academic.prospect_students.name) as student_name'),
                        DB::raw("CONCAT(UPPER(academic.admissions.name), ' - ', UPPER(academic.prospect_student_groups.group)) as class_name"),
                        DB::raw('finance.receipt_majors.total as instalment'),
                        DB::raw('finance.receipt_majors.discount_amount as discount'),
                        DB::raw("to_char(finance.receipt_majors.trans_date,'dd/mm/yyyy') as journal_date"),
                        DB::raw('journal_details.account_id as cash_account'),
                        'finance.receipt_majors.remark'
                    )
                    ->join('finance.payment_majors','finance.payment_majors.id','=','finance.receipt_majors.major_id')
                    ->join('finance.journals','finance.journals.id','=','finance.receipt_majors.journal_id')
                    ->joinSub($journal_details, 'journal_details', function ($join) {
                        $join->on('finance.receipt_majors.journal_id','=','journal_details.journal_id');
                    })
                    ->join('finance.codes','finance.codes.id','=','journal_details.account_id')
                    ->join('academic.prospect_students','academic.prospect_students.id','=','finance.payment_majors.prospect_student_id')
                    ->join('academic.prospect_student_groups','academic.prospect_student_groups.id','=','academic.prospect_students.prospect_group_id')
                    ->join('academic.admissions','academic.admissions.id','=','academic.prospect_student_groups.admission_id')
                    ->join('public.departments','public.departments.id','=','finance.payment_majors.department_id')
                    ->where('finance.receipt_majors.id', $id)
                    ->first();
        }
	}

	public function data(Request $request)
	{
        $sort = isset($request->sort) ? $request->sort : 'finance.receipt_majors.trans_date';
        $order = isset($request->order) ? $request->order : 'asc';
        $query = $this->dataInstalment($request->payment_major_id);
        // result
        $data = array();
        foreach ($query as $val) 
        {
            $data[] = array(
                'id' => $val->id,
                'journal' => '<b>'. $val->cash_no .'</b><br/>'. $val->journal_date,
                'account' => $val->code . ' ' . $val->name,
                'total' => number_format($val->total, 2),
                'discount' => number_format($val->discount_amount, 2),
                'remark' => $val->remark,
                'logged' => $val->logged,
            );            
        }
        $totals = $this->totalInstalment($request->payment_major_id);
        $footer[] = array(
            'account' => 'Total',
            'total' => '<b>'.number_format($totals->total, 2).'</b>',
            'discount' => '<b>'.number_format($totals->discount, 2).'</b>',
            'remark' => 'Sisa: <b>' . number_format($request->amount - $totals->total, 2) .'</b>'
        );
        //
        $result["total"] = $query->count();
        $result["rows"] = $data;
        $result["footer"] = $footer;
        return $result;
	}

	public function dataInstalment($payment_major_id)
    {
        // query
        return ReceiptMajor::select(
                    'finance.receipt_majors.id',
                    'finance.receipt_majors.major_id',
                    'finance.journals.bookyear_id',
                    'finance.journals.cash_no',
                    'finance.journals.journal_date',
                    'finance.codes.code',
                    'finance.codes.name',
                    'finance.receipt_majors.total',
                    'finance.receipt_majors.discount_amount',
                    'finance.receipt_majors.remark',
                    'finance.receipt_majors.logged',
                )
                ->join('finance.payment_majors','finance.payment_majors.id','=','finance.receipt_majors.major_id')
                ->join('finance.journals','finance.journals.id','=','finance.receipt_majors.journal_id')
                ->join('finance.journal_details','finance.journal_details.journal_id','=','finance.receipt_majors.journal_id')
                ->join('finance.codes','finance.codes.id','=','finance.journal_details.account_id')
                ->where('finance.codes.category_id', 1)
                ->where('finance.journal_details.debit','>',0)
                ->where('finance.payment_majors.id', $payment_major_id)
                ->orderBy('finance.receipt_majors.id')
                ->get()->map(function($model){
                    $model['journal_date'] = $this->formatDate($model['journal_date'],'iso');
                    $model['cash_no'] = $this->getPrefixBookYear($model->bookyear_id) . $model->cash_no;
                    return $model;
                });
    }

    public function totalInstalment($payment_major_id)
    {
        return ReceiptMajor::select(DB::raw('SUM(total) AS total'), DB::raw('SUM(discount_amount) AS discount'))
                ->where('major_id', $payment_major_id)
                ->first();
    }

    public function totalInstalments($payment_major_id)
    {
        return ReceiptMajor::select('major_id', DB::raw('SUM(total) AS total'), DB::raw('SUM(discount_amount) AS discount'))
                ->where('major_id', $payment_major_id)
                ->groupBy('major_id')
                ->get();
    }

    public function maxInstallment(Request $request)
    {
        $period = sprintf('%06d', $request->period);
        $subQuery = ReceiptMajor::select('finance.payment_majors.student_id',DB::raw('COUNT(finance.receipt_majors.id) as total'))
                        ->join('finance.payment_majors','finance.payment_majors.id','=','finance.receipt_majors.major_id')
                        ->join('academic.students','academic.students.id','=','finance.payment_majors.student_id')
                        ->join('academic.classes','academic.classes.id','=','academic.students.class_id')
                        ->where('finance.receipt_majors.is_prospect',0)
                        ->where('finance.payment_majors.bookyear_id',$request->bookyear_id)
                        ->where('finance.payment_majors.category_id',1)
                        ->where('finance.payment_majors.category_id',1)
                        ->where('finance.payment_majors.period_month', substr($period, 0,2))
                        ->where('finance.payment_majors.period_year', substr($period, 2,4))
                        ->where('academic.students.class_id',$request->class_id);
        if ($request->status > -1)
        {
            $subQuery = $subQuery->where('finance.payment_majors.is_paid',$request->status);
        }
        $subQuery = $subQuery->groupBy('finance.payment_majors.student_id');
        return DB::table(DB::raw("({$subQuery->toSql()}) as sub"))->select(DB::raw('MAX(sub.total) as max, COUNT(sub.student_id) as count'))->mergeBindings($subQuery->getQuery())->first();
    }

    public function dataPaymentClass(Request $request)
    {
        $query = $this->paymentClass($request->bookyear_id, $request->class_id, $request->status, $request->period)->get();
        //
        $data['payments'] = array();
        $totalPayment = 0;
        $totalMajor = 0;
        $totalDiscount = 0;
        $totalArrear = 0;
        foreach ($query as $row) 
        {
            $data["payments"][] = array(
                'student_no' => $row->student_no,
                'student' => $row->student,
                'class' => Str::upper($row->class_name),
                'status' => $row->status == 'Lunas' ? '<b>'.$row->status.'</b>' : $row->status,
                'payment' => 'Rp'.number_format($row->amount,2),
                'remark' => $row->remark,
                'pays' => $this->listPayment($request->bookyear_id, $row->student_id, $request->status, $row->id)['pays'],
                'major' => 'Rp'.number_format($this->listPayment($request->bookyear_id, $row->student_id, $request->status, $row->id)['major'],2),
                'discount' => 'Rp'.number_format($this->listPayment($request->bookyear_id, $row->student_id, $request->status, $row->id)['discount'],2),
                'arrears' => 'Rp'.number_format($row->amount - $this->listPayment($request->bookyear_id, $row->student_id, $request->status, $row->id)['major'],2),
            );
            $totalPayment += $row->amount;
            $totalMajor += $this->listPayment($request->bookyear_id, $row->student_id, $request->status, $row->id)['major'];
            $totalDiscount += $this->listPayment($request->bookyear_id, $row->student_id, $request->status, $row->id)['discount'];
            $totalArrear += $row->amount - $this->listPayment($request->bookyear_id, $row->student_id, $request->status, $row->id)['major'];
        }
        $result["total"] = $query->count();
        $result["rows"] = $data['payments'];
        $result["footer"][] = array(
            'status' => '<b>Total</b>',
            'payment' => '<b>Rp'.number_format($totalPayment,2).'</b>',
            'major' => '<b>Rp'.number_format($totalMajor,2).'</b>',
            'discount' => '<b>Rp'.number_format($totalDiscount,2).'</b>',
            'arrears' => '<b>Rp'.number_format($totalArrear,2).'</b>',
        );
        return $result;
    }

    public function paymentClass($bookyear_id, $class_id, $is_paid, $period)
    {
        $period_pay = sprintf('%06d', $period);
        $query = PaymentMajor::select(
                        DB::raw('DISTINCT ON (finance.payment_majors.student_id) student_id'),
                        DB::raw('INITCAP(academic.students.name) as student'),
                        DB::raw('INITCAP(academic.classes.class) as class_name'),
                        DB::raw("CASE 
                            WHEN finance.payment_majors.is_paid = 0 THEN 'Belum Lunas' 
                            WHEN finance.payment_majors.is_paid = 1 THEN 'Lunas'  
                            WHEN finance.payment_majors.is_paid = 2 THEN 'Gratis' END status"),
                        'finance.payment_majors.amount',
                        'finance.payment_majors.remark',
                        'finance.payment_majors.id',
                        'academic.students.student_no',
                    )
                    ->join('academic.students','academic.students.id','=','finance.payment_majors.student_id')
                    ->join('academic.classes','academic.classes.id','=','academic.students.class_id')
                    ->where('finance.payment_majors.is_prospect',0)
                    ->where('finance.payment_majors.bookyear_id',$bookyear_id)
                    ->where('finance.payment_majors.category_id',1)
                    ->where('finance.payment_majors.period_month', substr($period_pay, 0,2))
                    ->where('finance.payment_majors.period_year', substr($period_pay, 2,4))
                    ->where('academic.students.class_id',$class_id);
        if ($is_paid > -1)
        {
            $query = $query->where('finance.payment_majors.is_paid',$is_paid);
        }
        return $query->orderBy('finance.payment_majors.student_id');
    }

    public function listPayment($bookyear_id, $student_id, $is_paid, $major_id)
    {
        $query = ReceiptMajor::select(
                        'finance.payment_majors.student_id',
                        'finance.receipt_majors.trans_date',
                        'finance.receipt_majors.total',
                        'finance.receipt_majors.discount_amount'
                    )
                    ->join('finance.journals','finance.journals.id','=','finance.receipt_majors.journal_id')
                    ->join('finance.payment_majors','finance.payment_majors.id','=','finance.receipt_majors.major_id')
                    ->where('finance.journals.bookyear_id', $bookyear_id)
                    ->where('finance.payment_majors.student_id', $student_id)
                    ->where('finance.receipt_majors.major_id', $major_id)
                    ->where('finance.receipt_majors.is_prospect',0);
        if ($is_paid > -1)
        {
            $query = $query->where('finance.payment_majors.is_paid',$is_paid);
        }
        $query = $query->orderBy('finance.receipt_majors.trans_date')->get()->map(function($model){
            $model['tdate'] = $this->formatDate($model['trans_date'],'iso');
            return $model;
        });
        $totalpay = 0;
        $totaldiscount = 0;
        $i = 1;
        $result = '<table><tbody><tr>';
        foreach ($query as $row) 
        {
            $result .= '<td style="padding:5px 5px 5px 25px;text-align:right;width:105px;"><span style="display:flex;margin-left:-22px;margin-top:-5px;font-size:12px;"><b>#'.$i.'</b></span>Rp' . number_format($row->total) . '<hr style="margin:0.2rem 0 !important;" />' . $row->tdate . '</td>';
            $totalpay = $totalpay + $row->total + $row->discount_amount;
            $totaldiscount = $totaldiscount + $row->discount_amount;
            $i++;
        }
        $result .= '</tr></tbody></table>';
        //
        return array(
            'pays' => $result,
            'major' => $totalpay,
            'discount' => $totaldiscount,
            'queries' => $query
        );
    }

    public function totalPaymentReceipt($major_id)
    {
        return ReceiptMajor::select(DB::raw('SUM(total) as total_receipt, SUM(discount_amount) as total_discount'))->where('major_id',$major_id)->first();
    }

    public function lastPaymentReceipt($major_id)
    {
        return ReceiptMajor::where('major_id',$major_id)->orderByDesc('trans_date')->limit(1)->get()->map(function($model){
            $model['trans_date'] = $this->formatDate($model['trans_date'],'iso');
            return $model;
        });
    }

    public function dataPaymentClassArrear(Request $request)
    {
        $query = $this->paymentClassArrear($request->bookyear_id, $request->payment, $request->duration, $request->date_delay, $request->period)->get();
        //
        $data['payments'] = array();
        $totalPayment = 0;
        $totalMajor = 0;
        $totalDiscount = 0;
        $totalArrear = 0;
        foreach ($query as $row) 
        {
            $data["payments"][] = array(
                'student_no' => Str::upper($row->student_no),
                'student' => $row->student,
                'class' => Str::upper($row->class_name),
                'delayed' => $this->paymentClassDelay($request->bookyear_id, $request->payment, $request->duration, $request->date_delay, $row->student_id, $request->period)->pluck('delay')->first(),
                'payment' => 'Rp'.number_format($row->amount,2),
                'remark' => $row->remark,
                'pays' => $this->listPayment($request->bookyear_id, $row->student_id, $request->status, $row->id)['pays'],
                'major' => 'Rp'.number_format($this->listPayment($request->bookyear_id, $row->student_id, $request->status, $row->id)['major'],2),
                'discount' => 'Rp'.number_format($this->listPayment($request->bookyear_id, $row->student_id, $request->status, $row->id)['discount'],2),
                'arrears' => 'Rp'.number_format($row->amount - $this->listPayment($request->bookyear_id, $row->student_id, $request->status, $row->id)['major'],2),
            );
            $totalPayment += $row->amount;
            $totalMajor += $this->listPayment($request->bookyear_id, $row->student_id, $request->status, $row->id)['major'];
            $totalDiscount += $this->listPayment($request->bookyear_id, $row->student_id, $request->status, $row->id)['discount'];
            $totalArrear += $row->amount - $this->listPayment($request->bookyear_id, $row->student_id, $request->status, $row->id)['major'];
        }
        $result["total"] = $query->count();
        $result["rows"] = $data['payments'];
        $result["footer"][] = array(
            'status' => '<b>Total</b>',
            'payment' => '<b>Rp'.number_format($totalPayment,2).'</b>',
            'major' => '<b>Rp'.number_format($totalMajor,2).'</b>',
            'discount' => '<b>Rp'.number_format($totalDiscount,2).'</b>',
            'arrears' => '<b>Rp'.number_format($totalArrear,2).'</b>',
        );
        return $result;
    }

    public function paymentClassArrear($bookyear_id, $receipt_type_id, $duration, $date_delay, $period)
    {
        $query_major = $this->paymentClassDelay($bookyear_id, $receipt_type_id, $duration, $date_delay, 0, $period)
                        ->get()->pluck('id')->toArray();
        return PaymentMajor::select(
                    DB::raw('DISTINCT ON (finance.payment_majors.student_id) student_id'),
                    DB::raw('INITCAP(academic.students.name) as student'),
                    DB::raw('INITCAP(academic.classes.class) as class_name'),
                    'finance.payment_majors.amount',
                    'finance.payment_majors.remark',
                    'finance.payment_majors.id',
                    'academic.students.student_no',
                )
                ->join('academic.students','academic.students.id','=','finance.payment_majors.student_id')
                ->join('academic.classes','academic.classes.id','=','academic.students.class_id')
                ->whereIn('finance.payment_majors.id', $query_major)
                ->orderBy('finance.payment_majors.student_id');
    }

    public function paymentClassDelay($bookyear_id, $receipt_type_id, $duration, $date_delay, $student_id, $period)
    {
        $period_pay = sprintf('%06d', $period);
        $query = PaymentMajor::select('finance.payment_majors.id')
                    ->selectRaw('((?::DATE) - COALESCE(MAX(finance.receipt_majors.trans_date),MAX(finance.journals.journal_date))) as delay', [$date_delay])
                    ->selectRaw('COUNT(finance.receipt_majors.major_id) as count_trx')
                    ->leftJoin('finance.receipt_majors','finance.receipt_majors.major_id','=','finance.payment_majors.id')
                    ->join('finance.journals','finance.journals.id','=','finance.payment_majors.journal_id')
                    ->join('academic.students','academic.students.id','=','finance.payment_majors.student_id')
                    ->where('finance.payment_majors.is_paid',0)
                    ->where('finance.payment_majors.bookyear_id',$bookyear_id)
                    ->where('finance.payment_majors.receipt_id',$receipt_type_id)
                    ->where('finance.payment_majors.period_month', substr($period_pay, 0,2))
                    ->where('finance.payment_majors.period_year', substr($period_pay, 2,4));
        if ($student_id > 0)
        {
            $query = $query->where('finance.payment_majors.student_id', $student_id);
        }
        return $query->groupBy('finance.payment_majors.id')->havingRaw('((?::DATE) - COALESCE(MAX(finance.receipt_majors.trans_date),MAX(finance.journals.journal_date))) >= ?', [$date_delay, $duration]);
    }

    public function dataPaymentProspectGroup(Request $request)
    {
        $query = $this->paymentProspectGroup($request->department_id, $request->category, $request->prospect_group_id, $request->status)->get();
        //
        $data['payments'] = array();
        $totalPayment = 0;
        $totalMajor = 0;
        $totalDiscount = 0;
        $totalArrear = 0;
        foreach ($query as $row) 
        {
            $data["payments"][] = array(
                'student_no' => $row->registration_no,
                'student' => $row->student,
                'group' => Str::upper($row->group_name),
                'status' => $row->status == 'Lunas' ? '<b>'.$row->status.'</b>' : $row->status,
                'payment' => 'Rp'.number_format($row->amount,2),
                'remark' => $row->remark,
                'pays' => $this->listPaymentProspectGroup($request->department_id, $row->prospect_student_id, $request->status)['pays'],
                'major' => 'Rp'.number_format($this->listPaymentProspectGroup($request->department_id, $row->prospect_student_id, $request->status)['major'],2),
                'discount' => 'Rp'.number_format($this->listPaymentProspectGroup($request->department_id, $row->prospect_student_id, $request->status)['discount'],2),
                'arrears' => 'Rp'.number_format($row->amount - $this->listPaymentProspectGroup($request->department_id, $row->prospect_student_id, $request->status)['major'],2),
            );
            $totalPayment += $row->amount;
            $totalMajor += $this->listPaymentProspectGroup($request->department_id, $row->prospect_student_id, $request->status)['major'];
            $totalDiscount += $this->listPaymentProspectGroup($request->department_id, $row->prospect_student_id, $request->status)['discount'];
            $totalArrear += $row->amount - $this->listPaymentProspectGroup($request->department_id, $row->prospect_student_id, $request->status)['major'];
        }
        $result["total"] = $query->count();
        $result["rows"] = $data['payments'];
        $result["footer"][] = array(
            'status' => '<b>Total</b>',
            'payment' => '<b>Rp'.number_format($totalPayment,2).'</b>',
            'major' => '<b>Rp'.number_format($totalMajor,2).'</b>',
            'discount' => '<b>Rp'.number_format($totalDiscount,2).'</b>',
            'arrears' => '<b>Rp'.number_format($totalArrear,2).'</b>',
        );
        return $result;
    }

    public function paymentProspectGroup($department_id, $category, $prospect_group_id, $is_paid)
    {
        $query = PaymentMajor::select(
                        DB::raw('DISTINCT ON (finance.payment_majors.prospect_student_id) prospect_student_id'),
                        DB::raw('INITCAP(academic.prospect_students.name) as student'),
                        DB::raw('INITCAP(academic.prospect_student_groups.group) as group_name'),
                        DB::raw("CASE 
                            WHEN finance.payment_majors.is_paid = 0 THEN 'Belum Lunas' 
                            WHEN finance.payment_majors.is_paid = 1 THEN 'Lunas'  
                            WHEN finance.payment_majors.is_paid = 2 THEN 'Gratis' END status"),
                        'finance.payment_majors.amount',
                        'finance.payment_majors.remark',
                        'finance.payment_majors.id',
                        'academic.prospect_students.registration_no',
                    )
                    ->join('finance.receipt_categories','finance.receipt_categories.id','=','finance.payment_majors.category_id')
                    ->join('academic.prospect_students','academic.prospect_students.id','=','finance.payment_majors.prospect_student_id')
                    ->join('academic.prospect_student_groups','academic.prospect_student_groups.id','=','academic.prospect_students.prospect_group_id')
                    ->where('finance.payment_majors.is_prospect',1)
                    ->where('finance.payment_majors.department_id',$department_id)
                    ->where('finance.receipt_categories.code',$category)
                    ->where('academic.prospect_students.prospect_group_id',$prospect_group_id);
        if ($is_paid > -1)
        {
            $query = $query->where('finance.payment_majors.is_paid',$is_paid);
        }
        return $query->orderBy('finance.payment_majors.prospect_student_id');
    }

    public function listPaymentProspectGroup($department_id, $prospect_student_id, $is_paid)
    {
        $query = ReceiptMajor::select(
                        'finance.payment_majors.prospect_student_id',
                        'finance.receipt_majors.trans_date',
                        'finance.receipt_majors.total',
                        'finance.receipt_majors.discount_amount'
                    )
                    ->join('finance.journals','finance.journals.id','=','finance.receipt_majors.journal_id')
                    ->join('finance.payment_majors','finance.payment_majors.id','=','finance.receipt_majors.major_id')
                    ->where('finance.journals.department_id', $department_id)
                    ->where('finance.payment_majors.prospect_student_id', $prospect_student_id)
                    ->where('finance.receipt_majors.is_prospect',1);
        if ($is_paid > -1)
        {
            $query = $query->where('finance.payment_majors.is_paid',$is_paid);
        }
        $query = $query->orderBy('finance.receipt_majors.trans_date')->get()->map(function($model){
            $model['tdate'] = $this->formatDate($model['trans_date'],'iso');
            return $model;
        });
        $totalpay = 0;
        $totaldiscount = 0;
        $i = 1;
        $result = '<table><tbody><tr>';
        foreach ($query as $row) 
        {
            $result .= '<td style="padding:5px 5px 5px 25px;text-align:right;width:105px;"><span style="display:flex;margin-left:-22px;margin-top:-5px;font-size:12px;"><b>#'.$i.'</b></span>Rp' . number_format($row->total) . '<hr style="margin:0.2rem 0 !important;" />' . $row->tdate . '</td>';
            $totalpay = $totalpay + $row->total + $row->discount_amount;
            $totaldiscount = $totaldiscount + $row->discount_amount;
            $i++;
        }
        $result .= '</tr></tbody></table>';
        //
        return array(
            'pays' => $result,
            'major' => $totalpay,
            'discount' => $totaldiscount,
            'queries' => $query
        );
    }

    public function maxInstallmentProspectGroup(Request $request)
    {
        return ReceiptMajor::select('finance.payment_majors.prospect_student_id',DB::raw('COUNT(finance.payment_majors.prospect_student_id) as total_trx'))
                ->join('finance.payment_majors','finance.payment_majors.id','=','finance.receipt_majors.major_id')       
                ->join('finance.receipt_categories','finance.receipt_categories.id','=','finance.payment_majors.category_id')       
                ->join('academic.prospect_students','academic.prospect_students.id','=','finance.payment_majors.prospect_student_id')
                ->where('finance.receipt_majors.is_prospect',1)       
                ->where('finance.payment_majors.department_id',$request->department_id)       
                ->where('finance.receipt_categories.code',$request->category)       
                ->where('academic.prospect_students.prospect_group_id',$request->prospect_group_id)       
                ->groupBy('finance.payment_majors.prospect_student_id');
    }
    
    public function dataPaymentProspectArrear(Request $request)
    {
        $query = $this->paymentProspectArrear($request->department_id, $request->category_id, $request->payment, $request->duration, $request->date_delay)->get();
        //
        $data['payments'] = array();
        $totalPayment = 0;
        $totalMajor = 0;
        $totalDiscount = 0;
        $totalArrear = 0;
        foreach ($query as $row) 
        {
            $data["payments"][] = array(
                'registration_no' => Str::upper($row->registration_no),
                'student' => $row->student,
                'group_name' => Str::upper($row->group_name),
                'delayed' => $this->paymentProspectDelay($request->department_id, $request->category_id, $request->payment, $request->duration, $request->date_delay, $row->prospect_student_id)->pluck('delay')->first(),
                'payment' => 'Rp'.number_format($row->amount,2),
                'remark' => $row->remark,
                'pays' => $this->listPaymentProspectGroup($request->department_id, $row->prospect_student_id, $request->status)['pays'],
                'major' => 'Rp'.number_format($this->listPaymentProspectGroup($request->department_id, $row->prospect_student_id, $request->status)['major'],2),
                'discount' => 'Rp'.number_format($this->listPaymentProspectGroup($request->department_id, $row->prospect_student_id, $request->status)['discount'],2),
                'arrears' => 'Rp'.number_format($row->amount - $this->listPaymentProspectGroup($request->department_id, $row->prospect_student_id, $request->status)['major'],2),
            );
            $totalPayment += $row->amount;
            $totalMajor += $this->listPaymentProspectGroup($request->department_id, $row->prospect_student_id, $request->status)['major'];
            $totalDiscount += $this->listPaymentProspectGroup($request->department_id, $row->prospect_student_id, $request->status)['discount'];
            $totalArrear += $row->amount - $this->listPaymentProspectGroup($request->department_id, $row->prospect_student_id, $request->status)['major'];
        }
        $result["total"] = $query->count();
        $result["rows"] = $data['payments'];
        $result["footer"][] = array(
            'status' => '<b>Total</b>',
            'payment' => '<b>Rp'.number_format($totalPayment,2).'</b>',
            'major' => '<b>Rp'.number_format($totalMajor,2).'</b>',
            'discount' => '<b>Rp'.number_format($totalDiscount,2).'</b>',
            'arrears' => '<b>Rp'.number_format($totalArrear,2).'</b>',
        );
        return $result;
    }

    public function paymentProspectArrear($department_id, $category_id, $receipt_type_id, $duration, $date_delay)
    {
        $query_major = $this->paymentProspectDelay($department_id, $category_id, $receipt_type_id, $duration, $date_delay, 0)->get()->pluck('major_id')->toArray();
        return PaymentMajor::select(
                    DB::raw('DISTINCT ON (finance.payment_majors.prospect_student_id) prospect_student_id'),
                    DB::raw('INITCAP(academic.prospect_students.registration_no) as registration_no'),
                    DB::raw('INITCAP(academic.prospect_students.name) as student'),
                    DB::raw('INITCAP(academic.prospect_student_groups.group) as group_name'),
                    'finance.payment_majors.amount',
                    'finance.payment_majors.remark',
                )
                ->join('academic.prospect_students','academic.prospect_students.id','=','finance.payment_majors.prospect_student_id')
                ->join('academic.prospect_student_groups','academic.prospect_student_groups.id','=','academic.prospect_students.prospect_group_id')
                ->whereIn('finance.payment_majors.id', $query_major)
                ->orderBy('finance.payment_majors.prospect_student_id');
    }

    public function paymentProspectDelay($department_id, $category_id, $receipt_type_id, $duration, $date_delay, $student_id)
    {
        $query = ReceiptMajor::select('finance.receipt_majors.major_id')
                    ->selectRaw('((?::DATE) - MAX(finance.receipt_majors.trans_date)) as delay', [$date_delay])
                    ->selectRaw('COUNT(finance.receipt_majors.major_id) as count_trx')
                    ->join('finance.payment_majors','finance.payment_majors.id','=','finance.receipt_majors.major_id')
                    ->join('academic.prospect_students','academic.prospect_students.id','=','finance.payment_majors.prospect_student_id')
                    ->where('finance.payment_majors.is_paid',0)
                    ->where('finance.payment_majors.department_id',$department_id)
                    ->where('finance.payment_majors.category_id',$department_id)
                    ->where('finance.payment_majors.receipt_id',$receipt_type_id);
        // 
        if ($student_id > 0)
        {
            $query = $query->where('finance.payment_majors.prospect_student_id', $student_id);
        }
        return $query->groupBy('finance.receipt_majors.major_id')->havingRaw('((?::DATE) - MAX(finance.receipt_majors.trans_date)) >= ?', [$date_delay, $duration]);
    }

    public function dataRecapTotal($bookyear_id, $department_id, $category, $start_date, $end_date, $employee_id)
    {
        $query = ReceiptMajor::select(DB::raw('
                        SUM(finance.receipt_majors.total) as total, 
                        SUM(finance.receipt_majors.discount_amount) as total_discount,
                        (SUM(finance.receipt_majors.total) - SUM(finance.receipt_majors.discount_amount)) as total_grand,
                        finance.receipt_types.name as receipt_type,
                        finance.payment_majors.department_id
                    '))
                    ->join('finance.payment_majors','finance.payment_majors.id','=','finance.receipt_majors.major_id')
                    ->join('finance.receipt_categories','finance.receipt_categories.id','=','finance.payment_majors.category_id')
                    ->join('finance.receipt_types','finance.receipt_types.id','=','finance.payment_majors.receipt_id')
                    ->where('finance.receipt_categories.code', $category)
                    ->where('finance.payment_majors.bookyear_id', $bookyear_id)
                    ->where('finance.payment_majors.department_id', $department_id)
                    ->whereRaw('finance.receipt_majors.trans_date::date >= ?', $this->formatDate($start_date,'sys'))
                    ->whereRaw('finance.receipt_majors.trans_date::date <= ?', $this->formatDate($end_date,'sys'));
        // 
        if ($employee_id > 0)
        {
            $query = $query->where('finance.receipt_majors.employee_id', $employee_id);
        }
        $query = $query->groupBy('finance.receipt_types.name','finance.payment_majors.department_id')->get();

        return array(
            'data' => $query,
            'subtotal' => $query->sum('total_grand')
        );
    }

    public function dataRecapTotalDetail($bookyear_id, $department_id, $category, $start_date, $end_date, $employee_id)
    {
        $bookyear = BookYear::where('is_active',1)->first();
        $query = ReceiptMajor::select('finance.receipt_majors.trans_date',DB::raw('public.employees.name as employee'),'finance.receipt_majors.total','finance.receipt_majors.discount_amount','finance.journals.transaction')
                    ->join('finance.payment_majors','finance.payment_majors.id','=','finance.receipt_majors.major_id')
                    ->join('finance.receipt_categories','finance.receipt_categories.id','=','finance.payment_majors.category_id')
                    ->join('public.employees','public.employees.id','=','finance.receipt_majors.employee_id')
                    ->join('finance.journals','finance.journals.id','=','finance.receipt_majors.journal_id')
                    ->where('finance.receipt_categories.code', $category)
                    ->where('finance.payment_majors.bookyear_id', $bookyear_id)
                    ->where('finance.payment_majors.department_id', $department_id)
                    ->whereRaw('finance.receipt_majors.trans_date::date >= ?', $this->formatDate($start_date,'sys'))
                    ->whereRaw('finance.receipt_majors.trans_date::date <= ?', $this->formatDate($end_date,'sys'));
        if ($employee_id > 0)
        {
            $query = $query->where('finance.receipt_majors.employee_id', $employee_id);
        }
        return $query->orderBy('finance.receipt_majors.trans_date')->get()->map(function($model){
            $model['trans_date'] = $this->formatDate($model['trans_date'],'local');
            return $model;
        });
    }

    public function dataRecapDaily($bookyear_id, $department_id, $category, $start_date, $end_date, $employee_id)
    {
        $query = ReceiptMajor::select('finance.receipt_majors.trans_date')
                    ->join('finance.payment_majors','finance.payment_majors.id','=','finance.receipt_majors.major_id')
                    ->join('finance.receipt_categories','finance.receipt_categories.id','=','finance.payment_majors.category_id')
                    ->join('public.employees','public.employees.id','=','finance.receipt_majors.employee_id')
                    ->join('finance.journals','finance.journals.id','=','finance.receipt_majors.journal_id')
                    ->where('finance.receipt_categories.code', $category)
                    ->where('finance.payment_majors.bookyear_id', $bookyear_id)
                    ->where('finance.payment_majors.department_id', $department_id)
                    ->whereRaw('finance.receipt_majors.trans_date::date >= ?', $this->formatDate($start_date,'sys'))
                    ->whereRaw('finance.receipt_majors.trans_date::date <= ?', $this->formatDate($end_date,'sys'));
        if ($employee_id > 0)
        {
            $query = $query->where('finance.receipt_majors.employee_id', $employee_id);
        }
        return $query->groupBy('finance.receipt_majors.trans_date')->orderBy('finance.receipt_majors.trans_date')->get()->map(function($model){
            $model['trans_date'] = $this->formatDate($model['trans_date'],'local');
            return $model;
        });
    }

    public function dataRecapDailyTrans($bookyear_id, $department_id, $type_id, $trans_date, $employee_id)
    {
        $category = ReceiptType::where('id', $type_id)->first();
        $queryTrans = ReceiptMajor::select(DB::raw('SUM(finance.receipt_majors.total) as total, SUM(finance.receipt_majors.discount_amount) as total_discount'))
                                ->join('finance.payment_majors','finance.payment_majors.id','=','finance.receipt_majors.major_id')
                                ->join('finance.receipt_categories','finance.receipt_categories.id','=','finance.payment_majors.category_id')
                                ->join('finance.receipt_types','finance.receipt_types.id','=','finance.payment_majors.receipt_id')
                                ->join('public.employees','public.employees.id','=','finance.receipt_majors.employee_id')
                                ->join('finance.journals','finance.journals.id','=','finance.receipt_majors.journal_id')
                                ->where('finance.payment_majors.bookyear_id', $bookyear_id)
                                ->where('finance.receipt_types.department_id', $department_id)
                                ->whereDate('finance.receipt_majors.trans_date', $this->formatDate($trans_date,'sys'));
        if ($employee_id > 0)
        {
            $queryTrans = $queryTrans->where('finance.receipt_majors.employee_id', $employee_id);
        }
        // 
        return array(
            'transaction' => $queryTrans->where('finance.receipt_types.id', $type_id)->first(),
            'subtotal' => $queryTrans->where('finance.receipt_types.category_id', $category->category_id)->first(),
        );
    }

    public function dataRecapTransDetail($bookyear_id, $department_id, $receipt_category_id, $trans_date, $type_id, $employee_id)
    {
        $query = ReceiptMajor::select('finance.receipt_majors.trans_date',DB::raw('public.employees.name as employee'),'finance.receipt_majors.total','finance.receipt_majors.discount_amount','finance.journals.transaction')
                        ->join('finance.payment_majors','finance.payment_majors.id','=','finance.receipt_majors.major_id')
                        ->join('finance.receipt_categories','finance.receipt_categories.id','=','finance.payment_majors.category_id')
                        ->join('finance.receipt_types','finance.receipt_types.id','=','finance.payment_majors.receipt_id')
                        ->join('public.employees','public.employees.id','=','finance.receipt_majors.employee_id')
                        ->join('finance.journals','finance.journals.id','=','finance.receipt_majors.journal_id')
                        ->where('finance.payment_majors.bookyear_id', $bookyear_id)
                        ->where('finance.receipt_types.department_id', $department_id)
                        ->where('finance.receipt_types.id', $type_id)
                        ->where('finance.receipt_categories.code', $receipt_category_id)
                        ->whereDate('finance.receipt_majors.trans_date', $this->formatDate($trans_date,'sys'));
        if ($employee_id > 0)
        {
            $queryTrans = $queryTrans->where('finance.receipt_majors.employee_id', $employee_id);
        }
        //
        return $query->get()->map(function($model){
                    $model['trans_date'] = $this->formatDate($model['trans_date'],'local');
                    return $model;
                });
    }

    public function dataReceiptJournal(Request $request)
    {
        $param = $this->gridRequest($request, 'desc', 'trans_date');
        $query = ReceiptMajor::select(
                        'finance.journals.cash_no',
                        'finance.receipt_majors.trans_date',
                        'finance.journals.transaction',
                        'finance.journals.source',
                        'finance.journals.bookyear_id',
                        'public.employees.name',
                        'finance.receipt_majors.journal_id',
                    )
                    ->join('finance.payment_majors','finance.payment_majors.id','=','finance.receipt_majors.major_id')
                    ->join('finance.receipt_categories','finance.receipt_categories.id','=','finance.payment_majors.category_id')
                    ->join('finance.receipt_types','finance.receipt_types.id','=','finance.payment_majors.receipt_id')
                    ->join('public.employees','public.employees.id','=','finance.receipt_majors.employee_id')
                    ->join('finance.journals','finance.journals.id','=','finance.receipt_majors.journal_id')
                    ->where('finance.payment_majors.bookyear_id', $request->bookyear_id)
                    ->where('finance.receipt_types.department_id', $request->department_id)
                    ->where('finance.payment_majors.category_id', $request->category_id)
                    ->whereRaw('finance.receipt_majors.trans_date::date >= ?', $this->formatDate($request->start_date,'sys'))
                    ->whereRaw('finance.receipt_majors.trans_date::date <= ?', $this->formatDate($request->end_date,'sys'));

        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function($model) {
            $model['cash_no'] = '<b>'. $this->getPrefixBookYear($model->bookyear_id) . $model->cash_no.'</b>';
            $model['trans_date'] = $this->formatDate($model->trans_date,'iso');
            $model['source'] = $this->getTransactionSource()[$model->source];
            $model['total'] = 'Rp'.number_format(JournalDetail::select(DB::raw('SUM(debit) as total_debit'))->where('journal_id', $model->journal_id)->pluck('total_debit')->first(),2);
            return $model;
        });
        return $result;
    }

	private function logActivity(Request $request, $model_id, $subject, $action) 
	{
		if ($action == 'Tambah')
		{
			$data = array(
				'major_id' => $request->major_id, 
				'journal_id' => $request->journal_id, 
				'employee_id' => $request->employee_id, 
				'first_instalment' => $request->first_instalment, 
				'is_prospect' => $request->is_prospect, 
				'trans_date' => $request->trans_date, 
				'total' => $request->total, 
				'discount_amount' => $request->discount_amount, 
			);
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			$query = ReceiptMajor::find($model_id);
			$before = array(
				'major_id' => $query->major_id, 
				'journal_id' => $query->journal_id, 
				'employee_id' => $query->employee_id, 
				'first_instalment' => $query->first_instalment, 
				'is_prospect' => $query->is_prospect, 
				'trans_date' => $query->trans_date, 
				'total' => $query->total, 
				'discount_amount' => $query->discount_amount, 
			);
			$after = array(
				'major_id' => $request->has('major_id') ? $request->major_id : $query->major_id, 
				'journal_id' => $request->has('journal_id') ? $request->journal_id : $query->journal_id, 
				'employee_id' => $request->has('employee_id') ? $request->employee_id : $query->employee_id, 
				'first_instalment' => $request->has('first_instalment') ? $request->first_instalment : $query->first_instalment, 
				'is_prospect' => $request->has('is_prospect') ? $request->is_prospect : $query->is_prospect, 
				'trans_date' => $request->has('trans_date') ? $request->trans_date : $query->trans_date, 
				'total' => $request->has('total') ? $request->total : $query->total, 
				'discount_amount' => $request->has('discount_amount') ? $request->discount_amount : $query->discount_amount, 
			);
			if ($action == 'Ubah')
			{
		        $this->logTransaction('#', $action .' '. $subject, json_encode($before), json_encode($after));
			} else {
		        $this->logTransaction('#', $action .' '. $subject, json_encode($before), '{}');
			}
		} 
	}
	
}