<?php

namespace Modules\Finance\Repositories\Receipt;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\ReferenceTrait;
use Modules\Finance\Entities\ReceiptType;
use Modules\Finance\Entities\ReceiptOther;
use Modules\Finance\Entities\JournalDetail;
use Carbon\Carbon;

class ReceiptOtherEloquent implements ReceiptOtherRepository
{

	use HelperTrait;
    use AuditLogTrait;
    use ReferenceTrait;

    public function create(Request $request, $subject)
    {
        $payload = $request->all();
        $this->logActivity($request, 0, $subject, 'Tambah');
        return ReceiptOther::create($payload);
    }

    public function update(Request $request, $subject)
    {
        $payload = Arr::except($request->all(), ['created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah');
        //
        return ReceiptOther::where('id', $payload['id'])->update($payload);
    }

	public function show($id)
    {
        $journal_details = JournalDetail::orderBy('id');
        return ReceiptOther::select(
                    'finance.receipt_others.id',
                    'finance.receipt_others.source',
                    DB::raw('finance.receipt_others.total as amount'),
                    DB::raw("to_char(finance.receipt_others.trans_date,'dd/mm/yyyy') as journal_date"),
                    DB::raw('journal_details.account_id as cash_account'),
                    'finance.receipt_others.remark'
                )
                ->join('finance.journals','finance.journals.id','=','finance.receipt_others.journal_id')
                ->joinSub($journal_details, 'journal_details', function ($join) {
                    $join->on('finance.receipt_others.journal_id','=','journal_details.journal_id');
                })
                ->where('finance.receipt_others.id', $id)
                ->first();
    }

    public function data(Request $request)
    {
        $sort = isset($request->sort) ? $request->sort : 'finance.receipt_majors.trans_date';
        $order = isset($request->order) ? $request->order : 'asc';
        $query = $this->dataPayment();
        // result
        $data = array();
        foreach ($query as $val)
        {
            $data[] = array(
                'id' => $val->id,
                'journal' => '<b>'. $val->cash_no .'</b><br/>'. $val->journal_date,
                'source' => $val->source,
                'account' => $val->code . ' ' . $val->name,
                'total' => number_format($val->total, 2),
                'remark' => $val->remark,
                'logged' => $val->employee,
            );
        }
        $totals = $this->totalPayment();
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

    public function dataPayment()
    {
        // query
        return ReceiptOther::select(
                    'finance.receipt_others.id',
                    'finance.journals.cash_no',
                    'finance.journals.journal_date',
                    'finance.journals.bookyear_id',
                    'finance.codes.code',
                    'finance.codes.name',
                    'finance.receipt_others.source',
                    'finance.receipt_others.total',
                    'finance.receipt_others.remark',
                    'finance.receipt_others.employee',
                )
                ->join('finance.receipt_types','finance.receipt_types.id','=','finance.receipt_others.receipt_id')
                ->join('finance.journals','finance.journals.id','=','finance.receipt_others.journal_id')
                ->join('finance.journal_details','finance.journal_details.journal_id','=','finance.receipt_others.journal_id')
                ->join('finance.codes','finance.codes.id','=','finance.journal_details.account_id')
                ->where('finance.codes.category_id', 1)
                ->orderByDesc('finance.receipt_others.id')
                ->get()->map(function($model){
                    $model['journal_date'] = $this->formatDate($model['journal_date'],'iso');
                    $model['cash_no'] = $this->getPrefixBookYear($model->bookyear_id) . $model->cash_no;
                    return $model;
                });
    }

    public function totalPayment()
    {
        return ReceiptOther::select(DB::raw('SUM(total) AS total'))->first();
    }

    public function dataRecapTotal($bookyear_id, $department_id, $category, $start_date, $end_date, $employee_id)
    {
        $query = ReceiptOther::select(DB::raw('
                        SUM(finance.receipt_others.total) as total_grand,
                        finance.receipt_types.name as receipt_type,
                        finance.receipt_others.department_id
                    '))
                    ->join('finance.receipt_types','finance.receipt_types.id','=','finance.receipt_others.receipt_id')
                    ->join('finance.receipt_categories','finance.receipt_categories.id','=','finance.receipt_types.category_id')
                    ->join('finance.journals','finance.journals.id','=','finance.receipt_others.journal_id')
                    ->where('finance.receipt_categories.code', $category)
                    ->where('finance.receipt_others.bookyear_id', $bookyear_id)
                    ->where('finance.receipt_others.department_id', $department_id)
                    ->whereRaw('finance.receipt_others.trans_date::date >= ?', $this->formatDate($start_date,'sys'))
                    ->whereRaw('finance.receipt_others.trans_date::date <= ?', $this->formatDate($end_date,'sys'));
        //
        if ($employee_id > 0)
        {
            $query = $query->where('finance.journals.employee_id', $employee_id);
        }
        $query = $query->groupBy('finance.receipt_types.name','finance.receipt_others.department_id')->get();

        return array(
            'data' => $query,
            'subtotal' => $query->sum('total_grand')
        );
    }

    public function dataRecapTotalDetail($bookyear_id, $department_id, $category, $start_date, $end_date, $employee_id)
    {
        $query = ReceiptOther::select('finance.receipt_others.trans_date','finance.receipt_others.employee','finance.receipt_others.total','finance.journals.transaction')
                    ->join('finance.receipt_types','finance.receipt_types.id','=','finance.receipt_others.receipt_id')
                    ->join('finance.receipt_categories','finance.receipt_categories.id','=','finance.receipt_types.category_id')
                    ->join('finance.journals','finance.journals.id','=','finance.receipt_others.journal_id')
                    ->where('finance.receipt_categories.code', $category)
                    ->where('finance.receipt_others.bookyear_id', $bookyear_id)
                    ->where('finance.receipt_others.department_id', $department_id)
                    ->whereRaw('finance.receipt_others.trans_date::date >= ?', $this->formatDate($start_date,'sys'))
                    ->whereRaw('finance.receipt_others.trans_date::date <= ?', $this->formatDate($end_date,'sys'));
        if ($employee_id > 0)
        {
            $query = $query->where('finance.journals.employee_id', $employee_id);
        }
        return $query->orderBy('finance.receipt_others.trans_date')->get()->map(function($model){
            $model['trans_date'] = $this->formatDate($model['trans_date'],'local');
            return $model;
        });
    }

    public function dataRecapDaily($bookyear_id, $department_id, $category, $start_date, $end_date, $employee_id)
    {
        $query = ReceiptOther::select('finance.receipt_others.trans_date')
                    ->join('finance.receipt_types','finance.receipt_types.id','=','finance.receipt_others.receipt_id')
                    ->join('finance.receipt_categories','finance.receipt_categories.id','=','finance.receipt_types.category_id')
                    ->join('finance.journals','finance.journals.id','=','finance.receipt_others.journal_id')
                    ->where('finance.receipt_categories.code', $category)
                    ->where('finance.receipt_others.bookyear_id', $bookyear_id)
                    ->where('finance.receipt_others.department_id', $department_id)
                    ->whereRaw('finance.receipt_others.trans_date::date >= ?', $this->formatDate($start_date,'sys'))
                    ->whereRaw('finance.receipt_others.trans_date::date <= ?', $this->formatDate($end_date,'sys'));
        if ($employee_id > 0)
        {
            $query = $query->where('finance.journals.employee_id', $employee_id);
        }
        return $query->groupBy('finance.receipt_others.trans_date')->orderBy('finance.receipt_others.trans_date')->get()->map(function($model){
            $model['trans_date'] = $this->formatDate($model['trans_date'],'local');
            return $model;
        });
    }

    public function dataRecapDailyTrans($bookyear_id, $department_id, $type_id, $trans_date, $employee_id)
    {
        $category = ReceiptType::where('id', $type_id)->first();
        $queryTrans = ReceiptOther::select(DB::raw('SUM(finance.receipt_others.total) as total'))
                                ->join('finance.receipt_types','finance.receipt_types.id','=','finance.receipt_others.receipt_id')
                                ->join('finance.receipt_categories','finance.receipt_categories.id','=','finance.receipt_types.category_id')
                                ->join('finance.journals','finance.journals.id','=','finance.receipt_others.journal_id')
                                ->where('finance.receipt_others.bookyear_id', $bookyear_id)
                                ->where('finance.receipt_others.department_id', $department_id)
                                ->whereDate('finance.receipt_others.trans_date', $this->formatDate($trans_date,'sys'));
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
        $query = ReceiptOther::select('finance.receipt_others.trans_date','finance.receipt_others.employee','finance.receipt_others.total','finance.journals.transaction')
                    ->join('finance.receipt_types','finance.receipt_types.id','=','finance.receipt_others.receipt_id')
                    ->join('finance.receipt_categories','finance.receipt_categories.id','=','finance.receipt_types.category_id')
                    ->join('finance.journals','finance.journals.id','=','finance.receipt_others.journal_id')
                    ->where('finance.receipt_categories.code', $receipt_category_id)
                    ->where('finance.receipt_others.bookyear_id', $bookyear_id)
                    ->where('finance.receipt_others.department_id', $department_id)
                    ->where('finance.receipt_types.id', $type_id)
                    ->whereDate('finance.receipt_others.trans_date', $this->formatDate($trans_date,'sys'));
        if ($employee_id > 0)
        {
            $query = $query->where('finance.journals.employee_id', $employee_id);
        }
        return $query->get()->map(function($model){
            $model['trans_date'] = $this->formatDate($model['trans_date'],'local');
            return $model;
        });
    }

    public function dataReceipt(Request $request)
    {
        $param = $this->gridRequest($request, 'desc', 'finance.receipt_others.trans_date');
        $query = ReceiptOther::select(
                        'finance.receipt_others.id',
                        'finance.journals.cash_no',
                        'finance.journals.journal_date',
                        'finance.journals.bookyear_id',
                        'finance.codes.code',
                        'finance.codes.name',
                        'finance.receipt_others.source',
                        'finance.receipt_others.total',
                        'finance.receipt_others.remark',
                        'finance.receipt_others.employee',
                    )
                    ->join('finance.receipt_types','finance.receipt_types.id','=','finance.receipt_others.receipt_id')
                    ->join('finance.journals','finance.journals.id','=','finance.receipt_others.journal_id')
                    ->join('finance.journal_details','finance.journal_details.journal_id','=','finance.receipt_others.journal_id')
                    ->join('finance.codes','finance.codes.id','=','finance.journal_details.account_id')
                    ->where('finance.codes.category_id', 1)
                    ->where('finance.receipt_others.receipt_id', $request->receipt_id)
                    ->where('finance.receipt_others.department_id', $request->department_id)
                    ->where('finance.receipt_others.bookyear_id', $request->bookyear_id)
                    ->whereRaw('finance.receipt_others.trans_date >= ?', $this->formatDate($request->start_date,'sys'))
                    ->whereRaw('finance.receipt_others.trans_date <= ?', $this->formatDate($request->end_date,'sys'));

        $footer[] = array(
            'source' => 'Total',
            'total' => '<b>'.number_format($this->dataReceiptTotal($request->bookyear_id, $request->receipt_id, $request->department_id, $request->start_date, $request->end_date)->total, 2).'</b>',
        );
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function($model){
            $model['total'] = number_format($model->total,2);
            $model['cash_no'] = $this->getPrefixBookYear($model->bookyear_id) . $model->cash_no;
            $model['journal'] = '<b>'.$model->cash_no .'</b><br/>'. $this->formatDate($model->journal_date,'iso');
            return $model;
        });
        $result["footer"] = $footer;
        return $result;
    }

    public function dataReceiptTotal($bookyear_id, $receipt_id, $department_id, $start_date, $end_date)
    {
        return ReceiptOther::select(DB::raw('SUM(total) AS total'))
                    ->join('finance.receipt_types','finance.receipt_types.id','=','finance.receipt_others.receipt_id')
                    ->join('finance.journals','finance.journals.id','=','finance.receipt_others.journal_id')
                    ->join('finance.journal_details','finance.journal_details.journal_id','=','finance.receipt_others.journal_id')
                    ->join('finance.codes','finance.codes.id','=','finance.journal_details.account_id')
                    ->where('finance.codes.category_id', 1)
                    ->where('finance.receipt_others.receipt_id', $receipt_id)
                    ->where('finance.receipt_others.department_id', $department_id)
                    ->where('finance.receipt_others.bookyear_id', $bookyear_id)
                    ->whereRaw('finance.receipt_others.trans_date >= ?', $this->formatDate($start_date,'sys'))
                    ->whereRaw('finance.receipt_others.trans_date <= ?', $this->formatDate($end_date,'sys'))
                    ->first();
    }

    public function dataReceiptJournal(Request $request)
    {
        $param = $this->gridRequest($request, 'desc', 'trans_date');
        $query = ReceiptOther::select(
                        'finance.journals.cash_no',
                        'finance.receipt_others.trans_date',
                        'finance.journals.transaction',
                        'finance.journals.source',
                        'finance.journals.bookyear_id',
                        'finance.receipt_others.employee',
                        'finance.receipt_others.journal_id'
                    )
                    ->join('finance.receipt_types','finance.receipt_types.id','=','finance.receipt_others.receipt_id')
                    ->join('finance.receipt_categories','finance.receipt_categories.id','=','finance.receipt_types.category_id')
                    ->join('finance.journals','finance.journals.id','=','finance.receipt_others.journal_id')
                    ->where('finance.receipt_others.bookyear_id', $request->bookyear_id)
                    ->where('finance.receipt_others.department_id', $request->department_id)
                    ->where('finance.receipt_types.category_id', $request->category_id)
                    ->whereRaw('finance.receipt_others.trans_date::date >= ?', $this->formatDate($request->start_date,'sys'))
                    ->whereRaw('finance.receipt_others.trans_date::date <= ?', $this->formatDate($request->end_date,'sys'));

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
                'source' => $request->source,
                'trans_date' => $request->trans_date,
                'total' => $request->total,
                'employee' => $request->employee,
                'bookyear_id' => $request->bookyear_id,
                'department_id' => $request->department_id,
            );
            $this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
        } else {
            $query = ReceiptOther::find($model_id);
            $before = array(
                'receipt_id' => $query->receipt_id,
                'journal_id' => $query->journal_id,
                'source' => $query->source,
                'trans_date' => $query->trans_date,
                'total' => $query->total,
                'employee' => $query->employee,
                'bookyear_id' => $query->bookyear_id,
                'department_id' => $query->department_id,
            );
            $after = array(
                'receipt_id' => $request->has('receipt_id') ? $request->receipt_id : $query->receipt_id,
                'journal_id' => $request->has('journal_id') ? $request->journal_id : $query->journal_id,
                'source' => $request->has('source') ? $request->source : $query->source,
                'trans_date' => $request->has('trans_date') ? $request->trans_date : $query->trans_date,
                'total' => $request->has('total') ? $request->total : $query->total,
                'employee' => $request->has('employee') ? $request->employee : $query->employee,
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
