<?php

namespace Modules\Finance\Repositories\Receipt;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Http\Traits\ReferenceTrait;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use Modules\Finance\Entities\PaymentMajor;
use Modules\Finance\Entities\ReceiptType;
use Modules\Finance\Entities\ReceiptVoluntary;
use Modules\Finance\Entities\JournalDetail;
use Carbon\Carbon;

class ReceiptVoluntaryEloquent implements ReceiptVoluntaryRepository
{

    use ReferenceTrait;
	use HelperTrait;
    use AuditLogTrait;

    public function create(Request $request, $subject)
    {
        $payload = $request->all();
        $this->logActivity($request, 0, $subject, 'Tambah');
        return ReceiptVoluntary::create($payload);
    }

    public function update(Request $request, $subject)
    {
        $payload = Arr::except($request->all(), ['created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah');
        //
        return ReceiptVoluntary::where('id', $payload['id'])->update($payload);
    }

	public function show($id)
    {
        $journal_details = JournalDetail::orderBy('id');
        return ReceiptVoluntary::select(
                    'finance.receipt_voluntaries.id',
                    DB::raw('finance.receipt_voluntaries.total as amount'),
                    DB::raw("to_char(finance.receipt_voluntaries.trans_date,'dd/mm/yyyy') as journal_date"),
                    DB::raw('journal_details.account_id as cash_account'),
                    'finance.receipt_voluntaries.remark',
                    'finance.receipt_voluntaries.journal_id',
                )
                ->join('finance.journals','finance.journals.id','=','finance.receipt_voluntaries.journal_id')
                ->joinSub($journal_details, 'journal_details', function ($join) {
                    $join->on('finance.receipt_voluntaries.journal_id','=','journal_details.journal_id');
                })
                ->where('finance.receipt_voluntaries.id', $id)
                ->first();
    }

    public function data(Request $request)
    {
        $query = $this->dataPayment($request->is_prospect, $request->student_id);
        // result
        $data = array();
        foreach ($query as $val)
        {
            $data[] = array(
                'id' => $val->id,
                'journal' => '<b>'. $val->cash_no .'</b><br/>'. $val->journal_date,
                'account' => $val->code . ' ' . $val->name,
                'total' => number_format($val->total, 2),
                'remark' => $val->remark,
                'logged' => $val->employee,
            );
        }
        $totals = $this->totalPayment($request->is_prospect, $request->student_id);
        $footer[] = array(
            'account' => 'Total',
            'total' => '<b>'.number_format($totals->total, 2).'</b>',
        );
        //
        $result["total"] = $query->count();
        $result["rows"] = $data;
        $result["footer"] = $footer;
        return $result;
    }

    public function dataPayment($is_prospect, $student_id)
    {
        // query
        $query = ReceiptVoluntary::select(
                        'finance.receipt_voluntaries.id',
                        'finance.journals.cash_no',
                        'finance.journals.journal_date',
                        'finance.journals.bookyear_id',
                        'finance.codes.code',
                        'finance.codes.name',
                        'finance.receipt_voluntaries.total',
                        'finance.receipt_voluntaries.remark',
                        'finance.receipt_voluntaries.employee',
                    )
                    ->join('finance.receipt_types','finance.receipt_types.id','=','finance.receipt_voluntaries.receipt_id')
                    ->join('finance.journals','finance.journals.id','=','finance.receipt_voluntaries.journal_id')
                    ->join('finance.journal_details','finance.journal_details.journal_id','=','finance.receipt_voluntaries.journal_id')
                    ->join('finance.codes','finance.codes.id','=','finance.journal_details.account_id');
        if ($is_prospect == 0)
        {
            $query = $query->where('student_id', $student_id);
        } else {
            $query = $query->where('prospect_student_id', $student_id);
        }
        $query = $query->where('finance.codes.category_id', 1)->orderByDesc('finance.receipt_voluntaries.id')->get()->map(function($model){
            $model['journal_date'] = $this->formatDate($model['journal_date'],'iso');
            $model['cash_no'] = $this->getPrefixBookYear($model->bookyear_id) . $model->cash_no;
            return $model;
        });
        return $query;
    }

    public function totalPayment($is_prospect, $student_id)
    {
        $query = ReceiptVoluntary::select(DB::raw('SUM(total) AS total'));
        if ($is_prospect == 0)
        {
            $query = $query->where('student_id', $student_id);
        } else {
            $query = $query->where('prospect_student_id', $student_id);
        }
        $query = $query->first();
        return $query;
    }

    public function dataPaymentStudent(Request $request)
    {
        $query = ReceiptVoluntary::where('bookyear_id',$request['bookyear_id'])
                    ->whereRaw('trans_date::date >= ?', $this->formatDate($request['start_date'],'sys'))
                    ->whereRaw('trans_date::date <= ?', $this->formatDate($request['end_date'],'sys'));
        $query = $request['is_prospect'] == 0 ? $query->where('student_id',$request['student_id']) : $query->where('prospect_student_id',$request['student_id']);
        return $query->get();
    }

    public function totalPaymentReceipt($receipt_id, $student_id, $is_prospect)
    {
        $query = ReceiptVoluntary::select(DB::raw('SUM(total) as total_receipt'))->where('receipt_id',$receipt_id);
        $query = $is_prospect == 0 ? $query->where('student_id',$student_id) : $query->where('prospect_student_id',$student_id);
        return $query->where('is_prospect',$is_prospect)->first();
    }

    public function lastPaymentReceipt($receipt_id, $student_id, $is_prospect)
    {
        $query = ReceiptVoluntary::where('receipt_id',$receipt_id);
        $query = $is_prospect == 0 ? $query->where('student_id',$student_id) : $query->where('prospect_student_id',$student_id);
        return $query->where('is_prospect',$is_prospect)->orderByDesc('trans_date')->limit(1)->get()->map(function($model){
            $model['trans_date'] = $this->formatDate($model['trans_date'],'iso');
            return $model;
        });
    }

    public function dataPaymentClass(Request $request)
    {
        $query = $this->paymentClass($request->bookyear_id, $request->department_id, $request->class_id, $request->is_prospect)->get();
        //
        $data['payments'] = array();
        $totalMajor = 0;
        foreach ($query as $row)
        {
            $data["payments"][] = array(
                'student_no' => $row->student_no,
                'student' => $row->student,
                'class' => Str::upper($row->class_name),
                'pays' => $this->listPayment($request->bookyear_id, $request->department_id, $request->class_id, $row->student_id, 0)['pays'],
                'total' => 'Rp'.number_format($this->listPayment($request->bookyear_id, $request->department_id, $request->class_id, $row->student_id, 0)['major'],2),
            );
            $totalMajor += $this->listPayment($request->bookyear_id, $request->department_id, $request->class_id, $row->student_id, 0)['major'];
        }
        $result["total"] = $query->count();
        $result["rows"] = $data['payments'];
        $result["footer"][] = array(
            'pays' => '<b>Total</b>',
            'total' => '<b>Rp'.number_format($totalMajor,2).'</b>',
        );
        return $result;
    }

    public function paymentClass($bookyear_id, $department_id, $class_id, $is_prospect)
    {
        return ReceiptVoluntary::select(
                    DB::raw('DISTINCT ON (finance.receipt_voluntaries.student_id) student_id'),
                    DB::raw('INITCAP(academic.students.name) as student'),
                    DB::raw('INITCAP(academic.classes.class) as class_name'),
                    'finance.receipt_voluntaries.total',
                    'academic.students.student_no',
                )
                ->join('academic.students','academic.students.id','=','finance.receipt_voluntaries.student_id')
                ->join('academic.classes','academic.classes.id','=','academic.students.class_id')
                ->where('finance.receipt_voluntaries.is_prospect',$is_prospect)
                ->where('finance.receipt_voluntaries.bookyear_id', $bookyear_id)
                ->where('finance.receipt_voluntaries.department_id', $department_id)
                ->where('academic.students.class_id', $class_id)
                ->orderBy('finance.receipt_voluntaries.student_id');
    }

    public function listPayment($bookyear_id, $department_id, $class_id, $student_id, $is_prospect)
    {
        $query = ReceiptVoluntary::select(
                        'finance.receipt_voluntaries.student_id',
                        'finance.receipt_voluntaries.trans_date',
                        'finance.receipt_voluntaries.total',
                    )
                    ->join('academic.students','academic.students.id','=','finance.receipt_voluntaries.student_id')
                    ->where('finance.receipt_voluntaries.is_prospect', $is_prospect)
                    ->where('finance.receipt_voluntaries.student_id', $student_id)
                    ->where('finance.receipt_voluntaries.bookyear_id', $bookyear_id)
                    ->where('finance.receipt_voluntaries.department_id', $department_id)
                    ->where('academic.students.class_id', $class_id);

        $query = $query->orderBy('finance.receipt_voluntaries.trans_date')->get()->map(function($model){
            $model['tdate'] = $this->formatDate($model['trans_date'],'iso');
            return $model;
        });
        $totalpay = 0;
        $i = 1;
        $result = '<table><tbody><tr>';
        foreach ($query as $row)
        {
            $result .= '<td style="padding:5px 5px 5px 25px;text-align:right;width:105px;"><span style="display:flex;margin-left:-22px;margin-top:-5px;font-size:12px;"><b>#'.$i.'</b></span>Rp' . number_format($row->total) . '<hr style="margin:0.2rem 0 !important;" />' . $row->tdate . '</td>';
            $totalpay = $totalpay + $row->total;
            $i++;
        }
        $result .= '</tr></tbody></table>';
        //
        return array(
            'pays' => $result,
            'major' => $totalpay,
            'queries' => $query
        );
    }

    public function maxInstallment(Request $request)
    {
        return ReceiptVoluntary::select(
                    'finance.receipt_voluntaries.student_id',
                    DB::raw('COUNT(finance.receipt_voluntaries.student_id) total_trx'),
                )
                ->join('academic.students','academic.students.id','=','finance.receipt_voluntaries.student_id')
                ->join('academic.classes','academic.classes.id','=','academic.students.class_id')
                ->where('finance.receipt_voluntaries.is_prospect', $request->is_prospect)
                ->where('finance.receipt_voluntaries.bookyear_id', $request->bookyear_id)
                ->where('finance.receipt_voluntaries.department_id', $request->department_id)
                ->where('academic.students.class_id', $request->class_id)
                ->groupBy('finance.receipt_voluntaries.student_id')
                ->get();
    }

    public function dataPaymentProspectGroup(Request $request)
    {
        $query = $this->paymentProspectGroup($request->department_id, $request->prospect_group_id)->get();
        //
        $data['payments'] = array();
        $totalMajor = 0;
        foreach ($query as $row)
        {
            $data["payments"][] = array(
                'student_no' => $row->registration_no,
                'student' => $row->student,
                'group' => Str::upper($row->group_name),
                'pays' => $this->listPaymentProspectGroup($request->department_id, $request->prospect_group_id, $row->prospect_student_id)['pays'],
                'total' => 'Rp'.number_format($this->listPaymentProspectGroup($request->department_id, $request->prospect_group_id, $row->prospect_student_id)['major'],2),
            );
            $totalMajor += $this->listPaymentProspectGroup($request->department_id, $request->prospect_group_id, $row->prospect_student_id)['major'];
        }
        $result["total"] = $query->count();
        $result["rows"] = $data['payments'];
        $result["footer"][] = array(
            'pays' => '<b>Total</b>',
            'total' => '<b>Rp'.number_format($totalMajor,2).'</b>',
        );
        return $result;
    }

    public function paymentProspectGroup($department_id, $prospect_group_id)
    {
        return ReceiptVoluntary::select(
                    DB::raw('DISTINCT ON (finance.receipt_voluntaries.prospect_student_id) prospect_student_id'),
                    DB::raw('INITCAP(academic.prospect_students.name) as student'),
                    DB::raw('INITCAP(academic.prospect_student_groups.group) as group_name'),
                    'finance.receipt_voluntaries.total',
                    'academic.prospect_students.registration_no',
                )
                ->join('academic.prospect_students','academic.prospect_students.id','=','finance.receipt_voluntaries.prospect_student_id')
                ->join('academic.prospect_student_groups','academic.prospect_student_groups.id','=','academic.prospect_students.prospect_group_id')
                ->where('finance.receipt_voluntaries.is_prospect',1)
                ->where('finance.receipt_voluntaries.department_id',$department_id)
                ->where('academic.prospect_students.prospect_group_id',$prospect_group_id)
                ->orderBy('finance.receipt_voluntaries.prospect_student_id');
    }

    public function listPaymentProspectGroup($department_id, $prospect_group_id, $prospect_student_id)
    {
        $query = ReceiptVoluntary::select(
                        'finance.receipt_voluntaries.prospect_student_id',
                        'finance.receipt_voluntaries.trans_date',
                        'finance.receipt_voluntaries.total',
                    )
                    ->join('academic.prospect_students','academic.prospect_students.id','=','finance.receipt_voluntaries.prospect_student_id')
                    ->join('academic.prospect_student_groups','academic.prospect_student_groups.id','=','academic.prospect_students.prospect_group_id')
                    ->where('finance.receipt_voluntaries.is_prospect', 1)
                    ->where('finance.receipt_voluntaries.prospect_student_id', $prospect_student_id)
                    ->where('finance.receipt_voluntaries.department_id', $department_id)
                    ->where('academic.prospect_students.prospect_group_id',$prospect_group_id);

        $query = $query->orderBy('finance.receipt_voluntaries.trans_date')->get()->map(function($model){
            $model['tdate'] = $this->formatDate($model['trans_date'],'iso');
            return $model;
        });
        $totalpay = 0;
        $i = 1;
        $result = '<table><tbody><tr>';
        foreach ($query as $row)
        {
            $result .= '<td style="padding:5px 5px 5px 25px;text-align:right;width:105px;"><span style="display:flex;margin-left:-22px;margin-top:-5px;font-size:12px;"><b>#'.$i.'</b></span>Rp' . number_format($row->total) . '<hr style="margin:0.2rem 0 !important;" />' . $row->tdate . '</td>';
            $totalpay = $totalpay + $row->total;
            $i++;
        }
        $result .= '</tr></tbody></table>';
        //
        return array(
            'pays' => $result,
            'major' => $totalpay,
            'queries' => $query
        );
    }

    public function maxInstallmentProspectGroup(Request $request)
    {
        return ReceiptVoluntary::select(
                    'finance.receipt_voluntaries.prospect_student_id',
                    DB::raw('COUNT(finance.receipt_voluntaries.prospect_student_id) total_trx'),
                )
                ->join('academic.prospect_students','academic.prospect_students.id','=','finance.receipt_voluntaries.prospect_student_id')
                ->join('academic.prospect_student_groups','academic.prospect_student_groups.id','=','academic.prospect_students.prospect_group_id')
                ->where('finance.receipt_voluntaries.is_prospect', 1)
                ->where('finance.receipt_voluntaries.department_id', $request->department_id)
                ->where('academic.prospect_students.prospect_group_id',$request->prospect_group_id)
                ->groupBy('finance.receipt_voluntaries.prospect_student_id')
                ->get();
    }

    public function dataRecapTotal($bookyear_id, $department_id, $category, $start_date, $end_date, $employee_id)
    {
        $query = ReceiptVoluntary::select(DB::raw('
                        SUM(finance.receipt_voluntaries.total) as total_grand,
                        finance.receipt_types.name as receipt_type,
                        finance.receipt_voluntaries.department_id
                    '))
                    ->join('finance.receipt_types','finance.receipt_types.id','=','finance.receipt_voluntaries.receipt_id')
                    ->join('finance.receipt_categories','finance.receipt_categories.id','=','finance.receipt_types.category_id')
                    ->join('finance.journals','finance.journals.id','=','finance.receipt_voluntaries.journal_id')
                    ->where('finance.receipt_categories.code', $category)
                    ->where('finance.receipt_voluntaries.bookyear_id', $bookyear_id)
                    ->where('finance.receipt_voluntaries.department_id', $department_id)
                    ->whereRaw('finance.receipt_voluntaries.trans_date::date >= ?', $this->formatDate($start_date,'sys'))
                    ->whereRaw('finance.receipt_voluntaries.trans_date::date <= ?', $this->formatDate($end_date,'sys'));
        //
        if ($employee_id > 0)
        {
            $query = $query->where('finance.journals.employee_id', $employee_id);
        }
        $query = $query->groupBy('finance.receipt_types.name','finance.receipt_voluntaries.department_id')->get();

        return array(
            'data' => $query,
            'subtotal' => $query->sum('total_grand')
        );
    }

    public function dataRecapTotalDetail($bookyear_id, $department_id, $category, $start_date, $end_date, $employee_id)
    {
        $query = ReceiptVoluntary::select('finance.receipt_voluntaries.trans_date','finance.receipt_voluntaries.employee','finance.receipt_voluntaries.total','finance.journals.transaction')
                    ->join('finance.receipt_types','finance.receipt_types.id','=','finance.receipt_voluntaries.receipt_id')
                    ->join('finance.receipt_categories','finance.receipt_categories.id','=','finance.receipt_types.category_id')
                    ->join('finance.journals','finance.journals.id','=','finance.receipt_voluntaries.journal_id')
                    ->where('finance.receipt_categories.code', $category)
                    ->where('finance.receipt_voluntaries.bookyear_id', $bookyear_id)
                    ->where('finance.receipt_voluntaries.department_id', $department_id)
                    ->whereRaw('finance.receipt_voluntaries.trans_date::date >= ?', $this->formatDate($start_date,'sys'))
                    ->whereRaw('finance.receipt_voluntaries.trans_date::date <= ?', $this->formatDate($end_date,'sys'));
        if ($employee_id > 0)
        {
            $query = $query->where('finance.journals.employee_id', $employee_id);
        }
        return $query->orderBy('finance.receipt_voluntaries.trans_date')->get()->map(function($model){
            $model['trans_date'] = $this->formatDate($model['trans_date'],'local');
            return $model;
        });
    }

    public function dataRecapDaily($bookyear_id, $department_id, $category, $start_date, $end_date, $employee_id)
    {
        $query = ReceiptVoluntary::select('finance.receipt_voluntaries.trans_date')
                    ->join('finance.receipt_types','finance.receipt_types.id','=','finance.receipt_voluntaries.receipt_id')
                    ->join('finance.receipt_categories','finance.receipt_categories.id','=','finance.receipt_types.category_id')
                    ->join('finance.journals','finance.journals.id','=','finance.receipt_voluntaries.journal_id')
                    ->where('finance.receipt_categories.code', $category)
                    ->where('finance.receipt_voluntaries.bookyear_id', $bookyear_id)
                    ->where('finance.receipt_voluntaries.department_id', $department_id)
                    ->whereRaw('finance.receipt_voluntaries.trans_date::date >= ?', $this->formatDate($start_date,'sys'))
                    ->whereRaw('finance.receipt_voluntaries.trans_date::date <= ?', $this->formatDate($end_date,'sys'));
        if ($employee_id > 0)
        {
            $query = $query->where('finance.journals.employee_id', $employee_id);
        }
        return $query->groupBy('finance.receipt_voluntaries.trans_date')->orderBy('finance.receipt_voluntaries.trans_date')->get()->map(function($model){
            $model['trans_date'] = $this->formatDate($model['trans_date'],'local');
            return $model;
        });
    }

    public function dataRecapDailyTrans($bookyear_id, $department_id, $type_id, $trans_date, $employee_id)
    {
        $category = ReceiptType::where('id', $type_id)->first();
        $queryTrans = ReceiptVoluntary::select(DB::raw('SUM(finance.receipt_voluntaries.total) as total'))
                                ->join('finance.receipt_types','finance.receipt_types.id','=','finance.receipt_voluntaries.receipt_id')
                                ->join('finance.receipt_categories','finance.receipt_categories.id','=','finance.receipt_types.category_id')
                                ->join('finance.journals','finance.journals.id','=','finance.receipt_voluntaries.journal_id')
                                ->where('finance.receipt_voluntaries.bookyear_id', $bookyear_id)
                                ->where('finance.receipt_voluntaries.department_id', $department_id)
                                ->whereDate('finance.receipt_voluntaries.trans_date', $this->formatDate($trans_date,'sys'));
        if ($employee_id > 0)
        {
            $queryTrans = $queryTrans->where('finance.journals.employee_id', $employee_id);
        }
        //
        return array(
            'transaction' => $queryTrans->where('finance.receipt_types.id', $type_id)->first(),
            'subtotal' => $queryTrans->where('finance.receipt_types.category_id', $category->category_id)->first(),
        );
    }

    public function dataRecapTransDetail($bookyear_id, $department_id, $receipt_category_id, $trans_date, $type_id, $employee_id)
    {
        $query = ReceiptVoluntary::select('finance.receipt_voluntaries.trans_date','finance.receipt_voluntaries.employee','finance.receipt_voluntaries.total','finance.journals.transaction')
                    ->join('finance.receipt_types','finance.receipt_types.id','=','finance.receipt_voluntaries.receipt_id')
                    ->join('finance.receipt_categories','finance.receipt_categories.id','=','finance.receipt_types.category_id')
                    ->join('finance.journals','finance.journals.id','=','finance.receipt_voluntaries.journal_id')
                    ->where('finance.receipt_categories.code', $receipt_category_id)
                    ->where('finance.receipt_voluntaries.bookyear_id', $bookyear_id)
                    ->where('finance.receipt_voluntaries.department_id', $department_id)
                    ->where('finance.receipt_types.id', $type_id)
                    ->whereDate('finance.receipt_voluntaries.trans_date', $this->formatDate($trans_date,'sys'));
        if ($employee_id > 0)
        {
            $query = $query->where('finance.journals.employee_id', $employee_id);
        }
        return $query->get()->map(function($model){
            $model['trans_date'] = $this->formatDate($model['trans_date'],'local');
            return $model;
        });
    }

    public function dataReceiptJournal(Request $request)
    {
        $param = $this->gridRequest($request, 'desc', 'trans_date');
        $query = ReceiptVoluntary::select(
                        'finance.journals.cash_no',
                        'finance.receipt_voluntaries.trans_date',
                        'finance.journals.transaction',
                        'finance.journals.source',
                        'finance.journals.bookyear_id',
                        'finance.receipt_voluntaries.employee',
                        'finance.receipt_voluntaries.journal_id'
                    )
                    ->join('finance.receipt_types','finance.receipt_types.id','=','finance.receipt_voluntaries.receipt_id')
                    ->join('finance.receipt_categories','finance.receipt_categories.id','=','finance.receipt_types.category_id')
                    ->join('finance.journals','finance.journals.id','=','finance.receipt_voluntaries.journal_id')
                    ->where('finance.receipt_voluntaries.bookyear_id', $request->bookyear_id)
                    ->where('finance.receipt_voluntaries.department_id', $request->department_id)
                    ->where('finance.receipt_types.category_id', $request->category_id)
                    ->whereRaw('finance.receipt_voluntaries.trans_date::date >= ?', $this->formatDate($request->start_date,'sys'))
                    ->whereRaw('finance.receipt_voluntaries.trans_date::date <= ?', $this->formatDate($request->end_date,'sys'));

        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function($model) {
            $model['cash_no'] = '<b>'. $this->getPrefixBookYear($model->bookyear_id) . $model->cash_no.'</b>';
            $model['trans_date'] = $this->formatDate($model->trans_date,'iso');
            $model['source'] = $this->getTransactionSource()[$model->source];
            $model['total'] = 'Rp'.number_format(JournalDetail::select(DB::raw('SUM(debit) as total_debit'))->where('journal_id', $model->journal_id)->pluck('total_debit')->first(),2);
            $model['name'] = $model->employee;
            return $model;
        });
        return $result;
    }

    private function logActivity(Request $request, $model_id, $subject, $action)
    {
        if ($action == 'Tambah')
        {
            $data = array(
                'receipt_id' => $request->receipt_id,
                'journal_id' => $request->journal_id,
                'prospect_student_id' => $request->prospect_student_id,
                'student_id' => $request->student_id,
                'trans_date' => $request->trans_date,
                'total' => $request->total,
                'employee' => $request->employee,
                'is_prospect' => $request->is_prospect,
                'bookyear_id' => $request->bookyear_id,
                'department_id' => $request->department_id,
            );
            $this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
        } else {
            $query = ReceiptVoluntary::find($model_id);
            $before = array(
                'receipt_id' => $query->receipt_id,
                'journal_id' => $query->journal_id,
                'prospect_student_id' => $query->prospect_student_id,
                'student_id' => $query->student_id,
                'trans_date' => $query->trans_date,
                'total' => $query->total,
                'employee' => $query->employee,
                'is_prospect' => $query->is_prospect,
                'bookyear_id' => $query->bookyear_id,
                'department_id' => $query->department_id,
            );
            $after = array(
                'receipt_id' => $request->has('receipt_id') ? $request->receipt_id : $query->receipt_id,
                'journal_id' => $request->has('journal_id') ? $request->journal_id : $query->journal_id,
                'prospect_student_id' => $request->has('prospect_student_id') ? $request->prospect_student_id : $query->prospect_student_id,
                'student_id' => $request->has('student_id') ? $request->student_id : $query->student_id,
                'trans_date' => $request->has('trans_date') ? $request->trans_date : $query->trans_date,
                'total' => $request->has('total') ? $request->total : $query->total,
                'employee' => $request->has('employee') ? $request->employee : $query->employee,
                'is_prospect' => $request->has('is_prospect') ? $request->is_prospect : $query->is_prospect,
                'bookyear_id' => $request->has('bookyear_id') ? $request->bookyear_id : $query->bookyear_id,
                'department_id' => $request->has('department_id') ? $request->department_id : $query->department_id,
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
