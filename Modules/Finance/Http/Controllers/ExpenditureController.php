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
use Modules\Finance\Entities\ExpenditureType;
use Modules\Finance\Entities\Expenditure;
use Modules\Finance\Entities\BookYear;
use Modules\Finance\Entities\Journal;
use Modules\Finance\Entities\JournalDetail;
use Modules\Finance\Repositories\Journal\JournalEloquent;
use Modules\Finance\Repositories\Expenditure\ExpenditureTypeEloquent;
use Modules\Finance\Repositories\Expenditure\ExpenditureEloquent;
use Modules\Finance\Repositories\Reference\CodeEloquent;
use Modules\Finance\Http\Requests\ExpenditureRequest;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use View;
use Exception;

class ExpenditureController extends Controller
{
    use DepartmentTrait;
    use HelperTrait;
    use PdfTrait;

    private $subject = 'Data Transaksi Pengeluaran';
    private $subject_requested = 'Data Pemohon Lain';

    function __construct(
        JournalEloquent $journalEloquent, 
        ExpenditureTypeEloquent $expenditureTypeEloquent, 
        ExpenditureEloquent $expenditureEloquent, 
        CodeEloquent $codeEloquent
    )
    {
        $this->journalEloquent = $journalEloquent;
        $this->expenditureTypeEloquent = $expenditureTypeEloquent;
        $this->expenditureEloquent = $expenditureEloquent;
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
        $data['payments'] = ExpenditureType::select('name')->where('is_active', 1)->groupBy('name')->get();
        $data['codes_debit'] = $this->codeEloquent->combobox(1,'1-1');
        $data['bookyear'] = $this->getActiveBookYear();
        if ($data['bookyear']->id > 0)
        {
            return view('finance::pages.expenses.expenditure_trans', $data);
        } else {
            $data['error'] = 'Belum ada <b>Tahun Buku</b> yang dibuat, gunakan menu<br/> Data Master &#8594; Tahun Buku untuk membuat baru.';
            return view('errors.400', $data);
        }
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function requested(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        switch ($request->requested_by) 
        {
            case 2:
                $data['requests'] = $request->all();
                return view('finance::pages.expenses.expenditure_view_student', $data);
                break;
            case 3:
                return view('finance::pages.expenses.expenditure_view_other');
                break;
            default:
                $data['sections'] = Reference::where('category', 'hr_section')->get();
                return view('finance::pages.expenses.expenditure_view_employee', $data);
                break;
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(ExpenditureRequest $request)
    {
        $validated = $request->validated();
        try 
        {
            $request->merge([
                'trans_date' => $this->formatDate($request->trans_date, 'sys'),
                'logged' => auth()->user()->email,
            ]);
            $bookyear = $this->getActiveBookYear();
            $uuid = strtotime('now');
            $total = 0;
            for ($i=0; $i < count($request->rows); $i++)
            {
                $total += $request->rows[$i]['credit'];
            }
            if ($total > 0)
            {
                if ($request->id < 1) 
                {
                    $number = $bookyear->number;
                    $number += 1;
                    $cash_no = sprintf('%06d', $number);
                    DB::transaction(function () use ($request, $cash_no, $total, $bookyear, $uuid) {
                        // store to journal
                        $journal = $this->journalEloquent->store($request->trans_date, 'Pengeluaran Biaya Beban ' . $request->remark, $cash_no, $bookyear->id, 'expense', $request->department_id);
                        // store journal detail
                        for ($i=0; $i < count($request->rows); $i++)
                        {
                            $this->journalEloquent->createDetail($journal->id, $request->rows[$i]['id'], $request->rows[$i]['credit'], 0, $uuid);
                        }
                        $this->journalEloquent->createDetail($journal->id, $request->debit_account, 0, $total, $uuid);
                        // increment number in bookyear                  
                        BookYear::where('id', $request->bookyear_id)->increment('number');
                        // store requested
                        $request->merge([
                            'department_id' => $request->department_id,
                            'journal_id' => $journal->id,
                            'employee_id' => $request->employee_id > 0 ? $request->employee_id : null,
                            'student_id' => $request->student_id > 0 ? $request->student_id : null,
                            'requested_id' => $request->requested_id > 0 ? $request->requested_id : null,
                            'total' => $total,
                            'employee' => auth()->user()->name,
                        ]);
                        $expenditure = $this->expenditureEloquent->create($request, $this->subject);
                        // expenditure detail
                        for ($i=0; $i < count($request->rows); $i++)
                        {
                            $remark = isset($request->rows[$i]['remark']) ? $request->rows[$i]['remark'] : '-';
                            $this->expenditureEloquent->createDetail($expenditure->id, $request->rows[$i]['id'], $remark, $request->rows[$i]['credit']);
                        }
                        // increment number in bookyear                  
                        BookYear::where('id', $bookyear->id)->increment('number');
                    });
                    $response = $this->getResponse('store', '', $this->subject);
                } else {
                    if (empty($request->reason))
                    {
                        throw new Exception('Alasan Ubah Data wajib diisi.', 1);
                    } else {
                        $request->merge([
                            'total' => $total,
                            'employee' => auth()->user()->name,
                        ]);
                        DB::transaction(function () use ($request, $bookyear, $uuid, $total) {
                            $uuid = strtotime('now');
                            // update journal
                            Journal::where('id', $request->journal_id)->update([
                                'journal_date' => $request->trans_date,
                                'transaction' => 'Pengeluaran Biaya Beban ' . $request->remark,
                                'bookyear_id' => $bookyear->id,
                                'logged' => auth()->user()->email,
                            ]);
                            // delete details
                            DB::table('finance.expenditure_details')->where('expenditure_id', $request->id)->delete();
                            // delete journal detail
                            JournalDetail::where('journal_id', $request->journal_id)->delete();
                            // store journal detail
                            for ($i=0; $i < count($request->rows); $i++)
                            {
                                $remark = isset($request->rows[$i]['remark']) ? $request->rows[$i]['remark'] : '-';
                                $this->journalEloquent->createDetail($request->journal_id, $request->rows[$i]['id'], $request->rows[$i]['credit'], 0, $uuid);
                                $this->expenditureEloquent->createDetail($request->id, $request->rows[$i]['id'], $remark, $request->rows[$i]['credit']);
                            }
                            $this->journalEloquent->createDetail($request->journal_id, $request->debit_account, 0, $total, $uuid);
                            // update expenditure
                            $this->expenditureEloquent->update($request, $this->subject);
                        });
                        $response = $this->getResponse('store', '', $this->subject);
                    }
                }
            } else {
                $response = $this->getResponse('warning', 'Total tidak boleh 0.');
            }
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), $this->subject);
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
        return response()->json($this->expenditureEloquent->show($id));
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return JSON
     */
    public function data(Request $request)
    {
        return $this->expenditureEloquent->data($request);
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return JSON
     */
    public function dataDetail(Request $request)
    {
        return $this->expenditureEloquent->dataDetail($request);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return JSON
     */
    public function dataJournal($id)
    {
        return response()->json($this->expenditureEloquent->dataJournal($id));
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return JSON
     */
    public function dataRequested(Request $request)
    {
        return response()->json(DB::table('finance.requested_users')->get());
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function storeRequested(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
        ]);
        try {
            if (!isset($request->id))
            {
                DB::table('finance.requested_users')->insert([
                    'name' => $request->name,
                    'remark' => $request->remark,
                    'logged' => auth()->user()->email,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                DB::table('finance.requested_users')->where('id', $request->id)->update([
                    'name' => $request->name,
                    'remark' => $request->remark,
                    'logged' => auth()->user()->email,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
            $response = $this->getResponse('store', '', $this->subject_requested);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), $this->subject_requested);
        }
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return JSON
     */
    public function destroyRequested(Request $request)
    {
        try {
            DB::table('finance.requested_users')->where('id', $request->id)->delete();
            $response = $this->getResponse('destroy', '', $this->subject_requested);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), $this->subject_requested);
        }
        return response()->json($response);
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdf(Request $request)
    {
        $data['expenditures'] = json_decode($request->data);
        $data['details'] = $this->expenditureEloquent->dataJournal(0);
        // 
        $view = View::make('finance::pages.expenses.expenditures_pdf', $data);
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
        $data['expenditures'] = json_decode($request->data);
        $data['details'] = $this->expenditureEloquent->dataJournal(0);
        $data['profile'] = $this->getInstituteProfile();
        // 
        $view = View::make('finance::pages.expenses.expenditures_xlsx', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake();
        $filename = date('Ymdhis') . '_' . $name . '.xlsx';
        //
        Storage::disk('local')->put('public/downloads/'.$filename, $view->render());
        echo $filename;
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function printReceipt(Request $request)
    {
        $payload = json_decode($request->data);
        $data['profile'] = $this->getInstituteProfile();
        $data['receipt_majors'] = $this->expenditureEloquent->show($payload->id);
        $data['expenditures'] = $this->expenditureEloquent->dataJournal($payload->id);
        $data['values'] = array(
            'counted' => $data['receipt_majors']->total > 0 ? $this->counted(str_replace(',','',str_replace('Rp', '', $data['receipt_majors']->total))) : 'nol',
        );
        $name = Str::lower(config('app.name')) .'_kuitansi_pembayaran';
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // layout
        $header = View::make('finance::layouts.header_expenditure', $data)->render();
        Storage::disk('local')->put('public/tempo/header_'.$hashfile . '.html', $header);
        $body = View::make('finance::pages.expenses.receipt_expenditure_pdf', $data)->render();
        Storage::disk('local')->put('public/tempo/body_'.$hashfile . '.html', $body);
        $footer = View::make('finance::layouts.footer_expenditure', $data)->render();
        Storage::disk('local')->put('public/tempo/footer_'.$hashfile . '.html', $footer);
        //        
        $this->pdfCustomHeadFoot($hashfile, $filename, 'landscape', 'A5', '-B 1.5cm -L 1cm -R 1cm -T 5.8cm -B 4cm');
        echo $filename;
    }
}
