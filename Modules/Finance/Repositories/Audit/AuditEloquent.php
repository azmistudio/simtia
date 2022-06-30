<?php

namespace Modules\Finance\Repositories\Audit;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\ReferenceTrait;
use Modules\Finance\Entities\Audit;
use Carbon\Carbon;

class AuditEloquent implements AuditRepository
{	

	use HelperTrait;
    use ReferenceTrait;

    public function data(Request $request)
    {
        // query
        $query = Audit::select(
                        'finance.audits.source',
                        DB::raw('COUNT(finance.audits.id) as total'),
                    )
                    ->join('public.departments','public.departments.id','=','finance.audits.department_id')
                    ->where('source','<>','begin_balance')
                    ->where('finance.audits.bookyear_id', $request->bookyear_id)
                    ->whereRaw('finance.audits.audit_date::date >= ?', $this->formatDate($request->start_date,'sys'))
                    ->whereRaw('finance.audits.audit_date::date <= ?', $this->formatDate($request->end_date,'sys'));
        if ($request->department_id > 1)
        {
            $query = $query->where('finance.audits.department_id', $request->department_id);
        }
        $query = $query->groupBy('finance.audits.source')->get();
        //
        if ($query->count() > 0)
        {
            foreach ($query as $row) 
            {
                $result['rows'][] = array(
                    'source' => $row->source,
                    'changed' => $this->getTransactionSource()[$row->source],
                    'total' => $row->total,
                );
            }
        } else {
            $result['rows'] = array();
        }
        // result
        $result["total"] = $query->count();
        return $result;
    }

    public function dataAuditPaymentMajor($source, $bookyear_id, $department_id, $startdate, $enddate)
    {
        $query = Audit::select(
                        'finance.audits.id',
                        'finance.audits.employee',
                        'finance.audits.audit_date',
                        'finance.audits.remark',
                        'finance.journals.cash_no',
                        'finance.journals.transaction',
                    )
                    ->join('public.departments','public.departments.id','=','finance.audits.department_id')
                    ->join('finance.payment_majors','finance.payment_majors.id','=','finance.audits.source_id')
                    ->join('finance.journals','finance.journals.id','=','finance.payment_majors.journal_id')
                    ->where('finance.audits.source', $source)
                    ->where('finance.audits.bookyear_id', $bookyear_id)
                    ->whereRaw('finance.audits.audit_date::date >= ?', $this->formatDate($startdate,'sys'))
                    ->whereRaw('finance.audits.audit_date::date <= ?', $this->formatDate($enddate,'sys'));
        if ($department_id > 1)
        {
            $query = $query->where('finance.audits.department_id', $department_id);
        }
        return $query->orderByDesc('finance.audits.id')->get();
    }

    public function dataAuditJournalVoucher($source, $bookyear_id, $department_id, $startdate, $enddate)
    {
        $query = Audit::select(
                        'finance.audits.id',
                        'finance.audits.employee',
                        'finance.audits.audit_date',
                        'finance.audits.remark',
                        'finance.journals.cash_no',
                        'finance.journals.transaction',
                    )
                    ->join('public.departments','public.departments.id','=','finance.audits.department_id')
                    ->join('finance.journals','finance.journals.id','=','finance.audits.source_id')
                    ->where('finance.audits.source', $source)
                    ->where('finance.audits.bookyear_id', $bookyear_id)
                    ->whereRaw('finance.audits.audit_date::date >= ?', $this->formatDate($startdate,'sys'))
                    ->whereRaw('finance.audits.audit_date::date <= ?', $this->formatDate($enddate,'sys'));
        if ($department_id > 1)
        {
            $query = $query->where('finance.audits.department_id', $department_id);
        }
        return $query->orderByDesc('finance.audits.id')->get();
    }

    public function dataAuditReceiptMajor($source, $bookyear_id, $department_id, $startdate, $enddate)
    {
        $query = Audit::select(
                        'finance.audits.id',
                        'finance.audits.employee',
                        'finance.audits.audit_date',
                        'finance.audits.remark',
                        'finance.journals.cash_no',
                        'finance.journals.transaction',
                    )
                    ->join('public.departments','public.departments.id','=','finance.audits.department_id')
                    ->join('finance.receipt_majors','finance.receipt_majors.id','=','finance.audits.source_id')
                    ->join('finance.journals','finance.journals.id','=','finance.receipt_majors.journal_id')
                    ->where('finance.audits.source', $source)
                    ->where('finance.audits.bookyear_id', $bookyear_id)
                    ->whereRaw('finance.audits.audit_date::date >= ?', $this->formatDate($startdate,'sys'))
                    ->whereRaw('finance.audits.audit_date::date <= ?', $this->formatDate($enddate,'sys'));
        if ($department_id > 1)
        {
            $query = $query->where('finance.audits.department_id', $department_id);
        }
        return $query->orderByDesc('finance.audits.id')->get();
    }

    public function dataAuditReceiptVoluntary($source, $bookyear_id, $department_id, $startdate, $enddate)
    {
        $query = Audit::select(
                        'finance.audits.id',
                        'finance.audits.employee',
                        'finance.audits.audit_date',
                        'finance.audits.remark',
                        'finance.journals.cash_no',
                        'finance.journals.transaction',
                    )
                    ->join('public.departments','public.departments.id','=','finance.audits.department_id')
                    ->join('finance.receipt_voluntaries','finance.receipt_voluntaries.id','=','finance.audits.source_id')
                    ->join('finance.journals','finance.journals.id','=','finance.receipt_voluntaries.journal_id')
                    ->where('finance.audits.source', $source)
                    ->where('finance.audits.bookyear_id', $bookyear_id)
                    ->whereRaw('finance.audits.audit_date::date >= ?', $this->formatDate($startdate,'sys'))
                    ->whereRaw('finance.audits.audit_date::date <= ?', $this->formatDate($enddate,'sys'));
        if ($department_id > 1)
        {
            $query = $query->where('finance.audits.department_id', $department_id);
        }
        return $query->orderByDesc('finance.audits.id')->get();
    }

    public function dataAuditReceiptOther($source, $bookyear_id, $department_id, $startdate, $enddate)
    {
        $query = Audit::select(
                        'finance.audits.id',
                        'finance.audits.employee',
                        'finance.audits.audit_date',
                        'finance.audits.remark',
                        'finance.journals.cash_no',
                        'finance.journals.transaction',
                    )
                    ->join('public.departments','public.departments.id','=','finance.audits.department_id')
                    ->join('finance.receipt_others','finance.receipt_others.id','=','finance.audits.source_id')
                    ->join('finance.journals','finance.journals.id','=','finance.receipt_others.journal_id')
                    ->where('finance.audits.source', $source)
                    ->where('finance.audits.bookyear_id', $bookyear_id)
                    ->whereRaw('finance.audits.audit_date::date >= ?', $this->formatDate($startdate,'sys'))
                    ->whereRaw('finance.audits.audit_date::date <= ?', $this->formatDate($enddate,'sys'));
        if ($department_id > 1)
        {
            $query = $query->where('finance.audits.department_id', $department_id);
        }
        return $query->orderByDesc('finance.audits.id')->get();
    }

    public function dataAuditExpense($source, $bookyear_id, $department_id, $startdate, $enddate)
    {
        $query = Audit::select(
                        'finance.audits.id',
                        'finance.audits.employee',
                        'finance.audits.audit_date',
                        'finance.audits.remark',
                        'finance.journals.cash_no',
                        'finance.journals.transaction',
                    )
                    ->join('public.departments','public.departments.id','=','finance.audits.department_id')
                    ->join('finance.expenditures','finance.expenditures.id','=','finance.audits.source_id')
                    ->join('finance.journals','finance.journals.id','=','finance.expenditures.journal_id')
                    ->where('finance.audits.source', $source)
                    ->where('finance.audits.bookyear_id', $bookyear_id)
                    ->whereRaw('finance.audits.audit_date::date >= ?', $this->formatDate($startdate,'sys'))
                    ->whereRaw('finance.audits.audit_date::date <= ?', $this->formatDate($enddate,'sys'));
        if ($department_id > 1)
        {
            $query = $query->where('finance.audits.department_id', $department_id);
        }
        return $query->orderByDesc('finance.audits.id')->get(); 
    }

    public function dataAuditSaving($source, $bookyear_id, $department_id, $startdate, $enddate)
    {
        $query = Audit::select(
                        'finance.audits.id',
                        'finance.audits.employee',
                        'finance.audits.audit_date',
                        'finance.audits.remark',
                        'finance.journals.cash_no',
                        'finance.journals.transaction',
                    )
                    ->join('public.departments','public.departments.id','=','finance.audits.department_id')
                    ->join('finance.savings','finance.savings.id','=','finance.audits.source_id')
                    ->join('finance.journals','finance.journals.id','=','finance.savings.journal_id')
                    ->where('finance.audits.source', $source)
                    ->where('finance.audits.bookyear_id', $bookyear_id)
                    ->whereRaw('finance.audits.audit_date::date >= ?', $this->formatDate($startdate,'sys'))
                    ->whereRaw('finance.audits.audit_date::date <= ?', $this->formatDate($enddate,'sys'));
        if ($department_id > 1)
        {
            $query = $query->where('finance.audits.department_id', $department_id);
        }
        return $query->orderByDesc('finance.audits.id')->get(); 
    }
	
}