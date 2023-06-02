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
use App\Http\Traits\ReferenceTrait;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\PdfTrait;
use Modules\Finance\Entities\BookYear;
use Modules\Finance\Repositories\Journal\JournalEloquent;   
use Modules\Finance\Repositories\Receipt\ReceiptTypeEloquent;
use Modules\Finance\Repositories\Receipt\ReceiptMajorEloquent;
use Modules\Finance\Repositories\Receipt\ReceiptVoluntaryEloquent;
use Modules\Finance\Repositories\Receipt\ReceiptOtherEloquent;
use Modules\Finance\Repositories\Receipt\PaymentMajorEloquent;
use Modules\Finance\Repositories\Reference\CodeEloquent; 
use Modules\Finance\Repositories\Reference\BookYearEloquent; 
use Modules\Finance\Repositories\Audit\AuditEloquent; 
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use View;
use Exception;

class ReportController extends Controller
{
    use DepartmentTrait;
    use ReferenceTrait;
    use HelperTrait;
    use PdfTrait;

    function __construct(
        JournalEloquent $journalEloquent, 
        ReceiptTypeEloquent $receiptTypeEloquent, 
        ReceiptMajorEloquent $receiptMajorEloquent, 
        ReceiptVoluntaryEloquent $receiptVoluntaryEloquent, 
        ReceiptOtherEloquent $receiptOtherEloquent, 
        PaymentMajorEloquent $paymentMajorEloquent, 
        CodeEloquent $codeEloquent,
        BookYearEloquent $bookYearEloquent,
        AuditEloquent $auditEloquent
    )
    {
        $this->journalEloquent = $journalEloquent;
        $this->receiptTypeEloquent = $receiptTypeEloquent;
        $this->receiptMajorEloquent = $receiptMajorEloquent;
        $this->receiptVoluntaryEloquent = $receiptVoluntaryEloquent;
        $this->receiptOtherEloquent = $receiptOtherEloquent;
        $this->paymentMajorEloquent = $paymentMajorEloquent;
        $this->codeEloquent = $codeEloquent;
        $this->bookYearEloquent = $bookYearEloquent;
        $this->auditEloquent = $auditEloquent;
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
        return view('finance::reports.index', $data);
    }

    /**
     * Validate result.
     * @return Resource
     */
    public function validate(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $start_date = BookYear::find($request->bookyear_id)->start_date;
        $start_period = date("Ym", strtotime($start_date));
        $end_period = date("Ym", strtotime('+11 months', strtotime($start_date)));
        if (strlen($request->start) > 2 && strlen($request->end) > 2)
        {
            $end_date = $this->dateBefore(date('Y-m-d', strtotime('+1 year', strtotime($start_date))));
            if (
                $this->formatDate($request->start,'sys') >= $start_date && 
                $this->formatDate($request->end,'sys') <= $end_date &&
                $this->formatDate($request->start,'sys') <= $end_date && 
                $this->formatDate($request->end,'sys') >= $start_date
            )
            {
                $response = $this->getResponse('info', 'Tanggal valid');
            } else {
                $response = $this->getResponse('warning', 'Parameter tanggal pencarian tidak di dalam periode Tahun Buku ' . $this->formatDate($start_date,'local') .' s/d '. $this->formatDate($end_date,'local') .'.');
            }
        } else {
            $start_p = date('Ym', strtotime($request->start_year.'-'.$request->start));;
            $end_p = date('Ym', strtotime($request->end_year.'-'.$request->end));
            if ($start_p < $start_period)
            {
                $response = $this->getResponse('warning', 'Parameter dari bulan lebih kecil dari bulan mulai tahun buku');
            } elseif ($end_p > $end_period) {
                $response = $this->getResponse('warning', 'Parameter sampai bulan lebih besar dari bulan akhir tahun buku');
            } elseif ($start_p > $end_p) {
                $response = $this->getResponse('warning', 'Parameter bulan dari dan sampai tidak sesuai.');
            } else {
                $response = $this->getResponse('info', 'Tanggal valid');
            }
        }
        return response()->json($response);
    }

    /* Transaction */

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexTransaction(Request $request)
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
        $data['bookyears'] = BookYear::orderByDesc('book_year')->get();
        $data['departments'] = $this->listDepartment();
        return view('finance::reports.finances.transaction', $data);
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return JSON
     */
    public function dataTransaction(Request $request)
    {
        return response()->json($this->journalEloquent->dataTransaction($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfTransaction(Request $request)
    {
        $payload = json_decode($request->data);
        $data['requests'] = $payload;
        $data['profile'] = $this->getInstituteProfile();
        // 
        $view = View::make('finance::reports.finances.transaction_pdf', $data);
        $name = Str::lower(config('app.name')) .'_transaksi_keuangan';
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
    public function toExcelTransaction(Request $request)
    {
        $payload = json_decode($request->data);
        //
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('laporan_transaksi_keuangan');
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        //
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(100);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        // 
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');
        $sheet->mergeCells('A3:F3');
        //
        $sheet->setCellValue('A1', config('app.name') .' '. Str::upper($request->session()->get('institute')));
        $sheet->setCellValue('A2', 'LAPORAN TRANSAKSI KEUANGAN - ' . $payload->start .' s.d '. $payload->end);
        $sheet->setCellValue('A3', 'TAHUN BUKU ' . $payload->bookyear . ' - MATA UANG RUPIAH (Rp)');
        $sheet->setCellValue('A5', 'NO.');
        $sheet->setCellValue('B5', 'DEPARTEMEN');
        $sheet->setCellValue('C5', 'NO. JURNAL/TANGGAL');
        $sheet->setCellValue('D5', 'PETUGAS');
        $sheet->setCellValue('E5', 'TRANSAKSI');
        $sheet->setCellValue('F5', 'DEBIT');
        $sheet->setCellValue('G5', 'KREDIT');
        //
        $baris = 6;
        $number = 1;
        foreach ($payload->rows as $row) 
        {
            $sheet->setCellValue('A'.$baris,$number);
            $sheet->setCellValue('B'.$baris,$row->department_name);
            $sheet->setCellValue('C'.$baris,$row->journal);
            $sheet->setCellValue('D'.$baris,$row->employee);
            $sheet->setCellValue('E'.$baris,$row->transaction);
            $sheet->setCellValue('F'.$baris,$this->removeFormat($row->debit));
            $sheet->setCellValue('G'.$baris,$this->removeFormat($row->credit));
            $baris++;
            $number++;
        }
        $sheet->setCellValue('E'.$baris,'TOTAL');
        $lastrow = $baris - 1;
        $sheet->setCellValue('F'.$baris,'=SUM(F6:F'.$lastrow.')');
        $sheet->setCellValue('G'.$baris,'=SUM(G6:G'.$lastrow.')');
        //
        $sheet->getStyle('A1:A2')->applyFromArray($this->PHPExcelCommonStyle()['title']);
        $sheet->getStyle('A3:A3')->applyFromArray($this->PHPExcelCommonStyle()['subTitle']);
        $sheet->getStyle('A5:G5')->applyFromArray($this->PHPExcelCommonStyle()['header']);
        $sheet->getStyle('A6:A'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('B6:B'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('C6:C'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('D6:D'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('E6:E'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('F6:F'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
        $sheet->getStyle('F6:F'.$baris)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
        $sheet->getStyle('G6:G'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
        $sheet->getStyle('G6:G'.$baris)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
        //
        $name = Str::lower(config('app.name')) .'_laporan_transaksi_keuangan';
        $filename = date('Ymdhis') . '_' . $name . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save('storage/downloads/'.$filename);
        echo $filename;
    }

    /* General Ledger */

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexLedger(Request $request)
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
        $data['bookyears'] = BookYear::orderByDesc('book_year')->get();
        return view('finance::reports.finances.ledger', $data);
    }

    /**
     * Display a spesific view of the resource.
     * @return Renderable
     */
    public function indexLedgerView(Request $request)
    {
        $data['profile'] = $this->getInstituteProfile();
        $data['requests'] = $request->all();
        $data['balance'] = isset($request->is_data) ? true : false;
        if ($request->is_detail == 2)
        {
            $end_balance = $this->dateBefore($request->start);
            $data['balance_date'] = $this->formatDate($end_balance,'month');
            $data['accounts'] = $this->codeEloquent->list($data['balance']);
            $data['end_balances'] = $this->codeEloquent->listBalance($this->formatDate($request->start,'sys'), $request->bookyear_id);
            $data['account_details'] = $this->journalEloquent->list($this->formatDate($request->start,'sys'), $this->formatDate($request->end,'sys'), $request->bookyear_id, 0);
            $data['subtotals'] = $this->journalEloquent->listTotal($this->formatDate($request->start,'sys'), $this->formatDate($request->end,'sys'), $request->bookyear_id, 0);
            return view('finance::reports.finances.ledger_view', $data);
        } else {
            $data['accounts'] = $this->codeEloquent->listSummary($this->formatDate($request->start,'sys'), $this->formatDate($request->end,'sys'), $request->bookyear_id);
            return view('finance::reports.finances.ledger_summary_view', $data);
        }
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfLedger(Request $request)
    {
        $payload = json_decode($request->data);
        $data['requests'] = array(
            'start' => $payload->form[1]->value,
            'end' => $payload->form[2]->value,
            'bookyear' => $payload->bookyear,
        );
        $data['balance'] = isset($payload->form[4]) ? true : false;
        $data['profile'] = $this->getInstituteProfile();
        if ($payload->form[3]->value == 2)
        {
            $end_balance = $this->dateBefore($payload->form[1]->value);
            $data['balance_date'] = $this->formatDate($end_balance,'month');
            $data['accounts'] = $this->codeEloquent->list($data['balance']);
            $data['end_balances'] = $this->codeEloquent->listBalance($this->formatDate($payload->form[1]->value,'sys'), $payload->form[0]->value);
            $data['account_details'] = $this->journalEloquent->list($this->formatDate($payload->form[1]->value,'sys'), $this->formatDate($payload->form[2]->value,'sys'), $payload->form[0]->value, 0);
            $data['subtotals'] = $this->journalEloquent->listTotal($this->formatDate($payload->form[1]->value,'sys'), $this->formatDate($payload->form[2]->value,'sys'), $payload->form[0]->value, 0);
            $view = View::make('finance::reports.finances.ledger_detail_pdf', $data);
            $subject = config('app.name') . '_rincian_buku_besar';
        } else {
            $data['accounts'] = $this->codeEloquent->listSummary($this->formatDate($payload->form[1]->value,'sys'), $this->formatDate($payload->form[2]->value,'sys'), $payload->form[0]->value);
            $view = View::make('finance::reports.finances.ledger_pdf', $data);
            $subject = Str::lower(config('app.name')) . '_ringkasan_buku_besar';
        }
        //
        $hashfile = md5(date('Ymdhis') . '_' . $subject);
        $filename = date('Ymdhis') . '_' . $subject . '.pdf';
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfLandscape($hashfile, $filename);
        echo $filename;
    }

    /**
     * Export resource to Excel Document.
     * @return Excel
     */
    public function toExcelLedger(Request $request)
    {
        $payload = json_decode($request->data);
        $balance = isset($payload->form[4]) ? true : false;
        //
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        //
        if ($payload->form[3]->value == '2')
        {
            $balances = array();
            $end_balance = $this->dateBefore($payload->form[1]->value);
            $balance_date = $this->formatDate($end_balance,'month');
            $accounts = $this->codeEloquent->list($balance);
            $end_balances = $this->codeEloquent->listBalance($this->formatDate($payload->form[1]->value,'sys'), $payload->form[0]->value);
            $account_details = $this->journalEloquent->list($this->formatDate($payload->form[1]->value,'sys'), $this->formatDate($payload->form[2]->value,'sys'), $payload->form[0]->value, 0);
            $subtotals = $this->journalEloquent->listTotal($this->formatDate($payload->form[1]->value,'sys'), $this->formatDate($payload->form[2]->value,'sys'), $payload->form[0]->value, 0);
            //
            $sheet->setTitle('rincian_buku_besar');
            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(40);
            $sheet->getColumnDimension('C')->setWidth(30);
            $sheet->getColumnDimension('D')->setWidth(100);
            $sheet->getColumnDimension('E')->setWidth(30);
            $sheet->getColumnDimension('F')->setWidth(30);
            $sheet->getColumnDimension('G')->setWidth(30);
            //
            $sheet->mergeCells('A1:F1');
            $sheet->mergeCells('A2:F2');
            $sheet->mergeCells('A3:F3');
            //
            $sheet->setCellValue('A1', config('app.name') .' '. Str::upper($request->session()->get('institute')));
            $sheet->setCellValue('A2', 'RINCIAN BUKU BESAR - ' . $payload->form[1]->value .' s.d '. $payload->form[2]->value);
            $sheet->setCellValue('A3', 'TAHUN BUKU ' . $payload->bookyear . ' - MATA UANG RUPIAH (Rp)');
            //
            $sheet->setCellValue('A5', 'TANGGAL');
            $sheet->setCellValue('B5', 'TIPE TRANSAKSI');
            $sheet->setCellValue('C5', 'DEPARTEMEN');
            $sheet->setCellValue('D5', 'KETERANGAN');
            $sheet->setCellValue('E5', 'DEBIT');
            $sheet->setCellValue('F5', 'KREDIT');
            $sheet->setCellValue('G5', 'SALDO AKHIR');
            //
            $baris = 6;
            foreach ($accounts as $account) 
            {   
                $sheet->setCellValue('A'.$baris,$account->code . ' | ' . $account->name)->getStyle('A'.$baris.':A'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bold']);
                foreach ($end_balances as $end_balance)
                {
                    if ($end_balance->account_id == $account->id)
                    {
                        $balances[] = array(
                            'account_id' => $end_balance->account_id, 
                            'end_balance'=> $end_balance->end_balance,
                        ); 
                        $sheet->setCellValue('A'.$baris = $baris + 1,'   '.$balance_date);
                        $sheet->setCellValue('B'.$baris, 'Saldo per ' . $balance_date);
                        $sheet->setCellValue('G'.$baris, number_format($end_balance->end_balance,2));
                    }
                }
                foreach ($account_details as $account_detail)
                {
                    if ($account_detail->account_id == $account->id)
                    {
                        foreach ($balances as $balance)
                        {
                            if ($balance['account_id'] == $account->id)
                            {
                                $sheet->setCellValue('A'.$baris = $baris + 1,'   '.$account_detail->journal_date);
                                $sheet->setCellValue('B'.$baris, $account_detail->source);
                                $sheet->setCellValue('C'.$baris, $account_detail->department);
                                $sheet->setCellValue('D'.$baris, $account_detail->remark);
                                $sheet->setCellValue('E'.$baris, number_format($account_detail->debit,2));
                                $sheet->setCellValue('F'.$baris, number_format($account_detail->credit,2));
                                $sheet->setCellValue('G'.$baris, number_format(($balance['end_balance'] + $account_detail->debit) - $account_detail->credit,2));
                            }
                        }
                    }
                }
                foreach ($subtotals as $total)
                {
                    if ($total->account_id == $account->id)
                    {
                        $sheet->setCellValue('E'.$baris = $baris + 1,number_format($total->debit,2));
                        $sheet->setCellValue('F'.$baris,number_format($total->credit,2));
                        $sheet->getStyle('E'.$baris.':E'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bold']);
                        $sheet->getStyle('F'.$baris.':F'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bold']);
                    }
                }
                $baris++;
            }
            //
            $sheet->getStyle('A1:A2')->applyFromArray($this->PHPExcelCommonStyle()['title']);
            $sheet->getStyle('A3:A3')->applyFromArray($this->PHPExcelCommonStyle()['subTitle']);
            $sheet->getStyle('A5:G5')->applyFromArray($this->PHPExcelCommonStyle()['header']);
            $sheet->getStyle('A6:A'.$baris = $baris - 1)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
            $sheet->getStyle('B6:B'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
            $sheet->getStyle('C6:C'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
            $sheet->getStyle('D6:D'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
            $sheet->getStyle('E6:E'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
            $sheet->getStyle('E6:E'.$baris)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
            $sheet->getStyle('F6:F'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
            $sheet->getStyle('F6:F'.$baris)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
            $sheet->getStyle('G6:G'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
            $sheet->getStyle('G6:G'.$baris)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
            $filename = date('Ymdhis') . '_' . Str::lower(config('app.name')) . '_rincian_buku_besar.xlsx';
        } else {
            $accounts = $this->codeEloquent->listSummary($this->formatDate($payload->form[1]->value,'sys'), $this->formatDate($payload->form[2]->value,'sys'), $payload->form[0]->value);
            //
            $sheet->setTitle('ringkasan_buku_besar');
            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(50);
            $sheet->getColumnDimension('C')->setWidth(30);
            $sheet->getColumnDimension('D')->setWidth(30);
            $sheet->getColumnDimension('E')->setWidth(30);
            $sheet->getColumnDimension('F')->setWidth(30);
            //
            $sheet->mergeCells('A1:F1');
            $sheet->mergeCells('A2:F2');
            $sheet->mergeCells('A3:F3');
            //
            $sheet->setCellValue('A1', Str::upper($request->session()->get('institute')));
            $sheet->setCellValue('A2', 'RINGKASAN BUKU BESAR - ' . $payload->form[1]->value .' s.d '. $payload->form[2]->value);
            $sheet->setCellValue('A3', 'TAHUN BUKU ' . $payload->bookyear . ' - MATA UANG RUPIAH (Rp)');
            //
            $sheet->setCellValue('A5', 'KODE');
            $sheet->setCellValue('B5', 'NAMA');
            $sheet->setCellValue('C5', 'SALDO AWAL');
            $sheet->setCellValue('D5', 'PERUBAHAN DEBIT');
            $sheet->setCellValue('E5', 'PERUBAHAN KREDIT');
            $sheet->setCellValue('F5', 'SALDO AKHIR');
            //
            $baris = 6;
            foreach ($accounts as $row) 
            {
                if ($balance)
                {
                    if ($row->beg_balance > 0 || $row->trx_debit > 0 || $row->trx_credit > 0 || $row->end_balance > 0)
                    {
                        $sheet->setCellValue('A'.$baris,$row->parent > 0 ? '    '.$row->code : ' '. $row->code);
                        $sheet->setCellValue('B'.$baris,$row->parent > 0 ? '   '.$row->name : $row->name);
                        $sheet->setCellValue('C'.$baris,$row->beg_balance);
                        $sheet->setCellValue('D'.$baris,$row->trx_debit);
                        $sheet->setCellValue('E'.$baris,$row->trx_credit);
                        $sheet->setCellValue('F'.$baris,$row->end_balance);

                        $sheet->getStyle('A'.$baris.':A'.$baris)->applyFromArray($row->parent > 0 ? $this->PHPExcelCommonStyle()['normal'] : $this->PHPExcelCommonStyle()['bold']);
                        $sheet->getStyle('B'.$baris.':B'.$baris)->applyFromArray($row->parent > 0 ? $this->PHPExcelCommonStyle()['normal'] : $this->PHPExcelCommonStyle()['bold']);
                        $sheet->getStyle('C'.$baris.':C'.$baris)->applyFromArray($row->parent > 0 ? $this->PHPExcelCommonStyle()['normal'] : $this->PHPExcelCommonStyle()['bold']);
                        $sheet->getStyle('D'.$baris.':D'.$baris)->applyFromArray($row->parent > 0 ? $this->PHPExcelCommonStyle()['normal'] : $this->PHPExcelCommonStyle()['bold']);
                        $sheet->getStyle('E'.$baris.':E'.$baris)->applyFromArray($row->parent > 0 ? $this->PHPExcelCommonStyle()['normal'] : $this->PHPExcelCommonStyle()['bold']);
                        $sheet->getStyle('F'.$baris.':F'.$baris)->applyFromArray($row->parent > 0 ? $this->PHPExcelCommonStyle()['normal'] : $this->PHPExcelCommonStyle()['bold']);
                        $baris++;
                    } 
                } else {
                    $sheet->setCellValue('A'.$baris,$row->parent > 0 ? '    '.$row->code : ' '. $row->code);
                    $sheet->setCellValue('B'.$baris,$row->parent > 0 ? '   '.$row->name : $row->name);
                    $sheet->setCellValue('C'.$baris,$row->beg_balance);
                    $sheet->setCellValue('D'.$baris,$row->trx_debit);
                    $sheet->setCellValue('E'.$baris,$row->trx_credit);
                    $sheet->setCellValue('F'.$baris,$row->end_balance);

                    $sheet->getStyle('A'.$baris.':A'.$baris)->applyFromArray($row->parent > 0 ? $this->PHPExcelCommonStyle()['normal'] : $this->PHPExcelCommonStyle()['bold']);
                    $sheet->getStyle('B'.$baris.':B'.$baris)->applyFromArray($row->parent > 0 ? $this->PHPExcelCommonStyle()['normal'] : $this->PHPExcelCommonStyle()['bold']);
                    $sheet->getStyle('C'.$baris.':C'.$baris)->applyFromArray($row->parent > 0 ? $this->PHPExcelCommonStyle()['normal'] : $this->PHPExcelCommonStyle()['bold']);
                    $sheet->getStyle('D'.$baris.':D'.$baris)->applyFromArray($row->parent > 0 ? $this->PHPExcelCommonStyle()['normal'] : $this->PHPExcelCommonStyle()['bold']);
                    $sheet->getStyle('E'.$baris.':E'.$baris)->applyFromArray($row->parent > 0 ? $this->PHPExcelCommonStyle()['normal'] : $this->PHPExcelCommonStyle()['bold']);
                    $sheet->getStyle('F'.$baris.':F'.$baris)->applyFromArray($row->parent > 0 ? $this->PHPExcelCommonStyle()['normal'] : $this->PHPExcelCommonStyle()['bold']);
                    $baris++;
                }
            }
            //
            $sheet->getStyle('A1:A2')->applyFromArray($this->PHPExcelCommonStyle()['title']);
            $sheet->getStyle('A3:A3')->applyFromArray($this->PHPExcelCommonStyle()['subTitle']);
            $sheet->getStyle('A5:F5')->applyFromArray($this->PHPExcelCommonStyle()['header']);
            $sheet->getStyle('A6:A'.$baris = $baris - 1)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
            $sheet->getStyle('B6:B'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
            $sheet->getStyle('C6:C'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
            $sheet->getStyle('C6:C'.$baris)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
            $sheet->getStyle('D6:D'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
            $sheet->getStyle('D6:D'.$baris)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
            $sheet->getStyle('E6:E'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
            $sheet->getStyle('E6:E'.$baris)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
            $sheet->getStyle('F6:F'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
            $sheet->getStyle('F6:F'.$baris)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
            $filename = date('Ymdhis') . '_' . Str::lower(config('app.name')) . '_ringkasan_buku_besar.xlsx';
        }
        //
        $writer = new Xlsx($spreadsheet);
        $writer->save('storage/downloads/'.$filename);
        echo $filename;
    }

    /* Profit Loss */

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexProfitLoss(Request $request)
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
        $data['bookyears'] = BookYear::get();
        return view('finance::reports.finances.profit_loss', $data);
    }

    /**
     * Display a spesific view of the resource.
     * @return Renderable
     */
    public function indexProfitLossView(Request $request)
    {
        $data['profile'] = $this->getInstituteProfile();
        $data['requests'] = $request->all();
        $data['is_total'] = isset($request->is_total) ? true : false;
        $data['is_zero'] = isset($request->is_zero) ? true : false;
        $data['startdate'] = $this->formatDate($this->formatDate($request->start,'sys'),'month');
        $data['lastdate'] = $this->formatDate($this->formatDate($request->end,'sys'),'month');
        $data['profits'] = $this->codeEloquent->listProfitLoss(4, $this->formatDate($request->start,'sys'), $this->formatDate($request->end,'sys'), $request->bookyear_id);
        $data['losses'] = $this->codeEloquent->listProfitLoss(5, $this->formatDate($request->start,'sys'), $this->formatDate($request->end,'sys'), $request->bookyear_id);
        return view('finance::reports.finances.profit_loss_view', $data);
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfProfitLoss(Request $request)
    {
        $payload = json_decode($request->data);
        $data['requests'] = array(
            'start' => $payload->form[1]->value,
            'end' => $payload->form[2]->value,
            'bookyear' => $payload->bookyear,
        );
        $data['startdate'] = $this->formatDate($this->formatDate($payload->form[1]->value,'sys'),'month');
        $data['lastdate'] = $this->formatDate($this->formatDate($payload->form[2]->value,'sys'),'month');
        $data['is_total'] = $this->subExist('is_total', $payload->form);
        $data['is_zero'] = $this->subExist('is_zero', $payload->form);
        $data['profile'] = $this->getInstituteProfile();
        $data['profits'] = $this->codeEloquent->listProfitLoss(4, $this->formatDate($payload->form[1]->value,'sys'), $this->formatDate($payload->form[2]->value,'sys'), $payload->form[0]->value);
        $data['losses'] = $this->codeEloquent->listProfitLoss(5, $this->formatDate($payload->form[1]->value,'sys'), $this->formatDate($payload->form[2]->value,'sys'), $payload->form[0]->value);
        // 
        $view = View::make('finance::reports.finances.profit_loss_pdf', $data);
        $subject = Str::lower(config('app.name')) . '_laporan_laba_rugi';
        $hashfile = md5(date('Ymdhis') . '_' . $subject);
        $filename = date('Ymdhis') . '_' . $subject . '.pdf';
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortraits($hashfile, $filename);
        echo $filename;
    }

    /**
     * Export resource to Excel Document.
     * @return Excel
     */
    public function toExcelProfitLoss(Request $request)
    {
        $payload = json_decode($request->data);
        $startdate = $this->formatDate($this->formatDate($payload->form[1]->value,'sys'),'month');
        $lastdate = $this->formatDate($this->formatDate($payload->form[2]->value,'sys'),'month');
        $is_total = $this->subExist('is_total', $payload->form);
        $is_zero = $this->subExist('is_zero', $payload->form);
        $profile = $this->getInstituteProfile();
        $profits = $this->codeEloquent->listProfitLoss(4, $this->formatDate($payload->form[1]->value,'sys'), $this->formatDate($payload->form[2]->value,'sys'), $payload->form[0]->value);
        $losses = $this->codeEloquent->listProfitLoss(5, $this->formatDate($payload->form[1]->value,'sys'), $this->formatDate($payload->form[2]->value,'sys'), $payload->form[0]->value);
        $debits = 0;
        $credits = 0;
        //
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        //
        $sheet->setTitle('laporan_laba_rugi');
        $sheet->getColumnDimension('A')->setWidth(75);
        $sheet->getColumnDimension('B')->setWidth(30);

        $sheet->mergeCells('A1:B1');
        $sheet->mergeCells('A2:B2');
        $sheet->mergeCells('A3:B3');
        //
        $sheet->setCellValue('A1', config('app.name') .' '. Str::upper($request->session()->get('institute')));
        $sheet->setCellValue('A2', 'LAPORAN LABA/RUGI - ' . $payload->form[1]->value .' s.d '. $payload->form[2]->value);
        $sheet->setCellValue('A3', 'TAHUN BUKU ' . $payload->bookyear . ' - MATA UANG RUPIAH (Rp)');
        $sheet->setCellValue('A5', 'DESKRIPSI');
        $sheet->setCellValue('B5', $startdate .' s.d '. $lastdate);
        // profits
        $sheet->setCellValue('A6', 'PENDAPATAN')->getStyle('A6:A6')->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        $baris = 7;
        foreach ($profits as $profit) 
        {
            if ($profit->parent > 0) 
            {
                $debits += $profit->debit;
            }
            if (!$is_total)
            {
                if ($is_zero)
                {
                    $sheet->setCellValue('A'.$baris,$profit->parent > 0 ? '     '.$profit->name : '   '.$profit->name);
                    $sheet->setCellValue('B'.$baris,$profit->debit);
                    $sheet->getStyle('A'.$baris.':A'.$baris)->applyFromArray($profit->parent > 0 ? $this->PHPExcelCommonStyle()['normal'] : $this->PHPExcelCommonStyle()['bold']);
                    $sheet->getStyle('B'.$baris.':B'.$baris)->applyFromArray($profit->parent > 0 ? $this->PHPExcelCommonStyle()['normal'] : $this->PHPExcelCommonStyle()['bold']);
                    $baris++;
                } else {
                    if ($profit->debit > 0)
                    {
                        $sheet->setCellValue('A'.$baris,$profit->parent > 0 ? '     '.$profit->name : '   '.$profit->name);
                        $sheet->setCellValue('B'.$baris,$profit->debit);
                        $sheet->getStyle('A'.$baris.':A'.$baris)->applyFromArray($profit->parent > 0 ? $this->PHPExcelCommonStyle()['normal'] : $this->PHPExcelCommonStyle()['bold']);
                        $sheet->getStyle('B'.$baris.':B'.$baris)->applyFromArray($profit->parent > 0 ? $this->PHPExcelCommonStyle()['normal'] : $this->PHPExcelCommonStyle()['bold']);
                        $baris++;
                    }
                }
            }
        }
        $sheet->setCellValue('A'.$baris, 'Jumlah Pendapatan');
        $sheet->setCellValue('B'.$baris, $debits);
        $sheet->getStyle('A'.$baris.':A'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        $sheet->getStyle('B'.$baris.':B'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        // losses
        $sheet->setCellValue('A'.$baris = $baris + 2, 'LABA KOTOR')->getStyle('A'.$baris.':A'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        $sheet->setCellValue('B'.$baris, $debits)->getStyle('B'.$baris.':B'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        $sheet->setCellValue('A'.$baris = $baris + 2, 'BIAYA')->getStyle('A'.$baris.':A'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        $baris_next = $baris + 1;
        foreach ($losses as $loss)
        {
            if ($loss->parent > 0)
            {
                $credits += $loss->credit;
            }
            if (!$is_total)
            {
                if ($is_zero)
                {
                    $sheet->setCellValue('A'.$baris_next,$loss->parent > 0 ? '     '.$loss->name : '   '.$loss->name);
                    $sheet->setCellValue('B'.$baris_next,$loss->credit);
                    $sheet->getStyle('A'.$baris_next.':A'.$baris_next)->applyFromArray($loss->parent > 0 ? $this->PHPExcelCommonStyle()['normal'] : $this->PHPExcelCommonStyle()['bold']);
                    $sheet->getStyle('B'.$baris_next.':B'.$baris_next)->applyFromArray($loss->parent > 0 ? $this->PHPExcelCommonStyle()['normal'] : $this->PHPExcelCommonStyle()['bold']);
                    $baris_next++;
                } else {
                    if ($loss->credit != 0)
                    {
                        $sheet->setCellValue('A'.$baris_next,$loss->parent > 0 ? '     '.$loss->name : '   '.$loss->name);
                        $sheet->setCellValue('B'.$baris_next,$loss->credit);
                        $sheet->getStyle('A'.$baris_next.':A'.$baris_next)->applyFromArray($loss->parent > 0 ? $this->PHPExcelCommonStyle()['normal'] : $this->PHPExcelCommonStyle()['bold']);
                        $sheet->getStyle('B'.$baris_next.':B'.$baris_next)->applyFromArray($loss->parent > 0 ? $this->PHPExcelCommonStyle()['normal'] : $this->PHPExcelCommonStyle()['bold']);
                        $baris_next++;
                    }
                }
            }
        }
        $sheet->setCellValue('A'.$baris_next, 'Jumlah Biaya');
        $sheet->setCellValue('B'.$baris_next, $credits);
        $sheet->getStyle('A'.$baris_next.':A'.$baris_next)->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        $sheet->getStyle('B'.$baris_next.':B'.$baris_next)->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        // balance
        $sheet->setCellValue('A'.$baris_next = $baris_next + 2, 'LABA BERSIH');
        $sheet->setCellValue('B'.$baris_next, ($debits - $credits));
        $sheet->getStyle('A'.$baris_next.':A'.$baris_next)->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        $sheet->getStyle('B'.$baris_next.':B'.$baris_next)->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        //
        $sheet->getStyle('A1:A2')->applyFromArray($this->PHPExcelCommonStyle()['title']);
        $sheet->getStyle('A3:A3')->applyFromArray($this->PHPExcelCommonStyle()['subTitle']);
        $sheet->getStyle('A5:B5')->applyFromArray($this->PHPExcelCommonStyle()['header']);
        $sheet->getStyle('A6:A'.$baris_next)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('B6:B'.$baris_next)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
        $sheet->getStyle('B6:B'.$baris_next)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
        $filename = date('Ymdhis') . '_' . Str::lower(config('app.name')) . '_laporan_laba_rugi.xlsx';
        //
        $writer = new Xlsx($spreadsheet);
        $writer->save('storage/downloads/'.$filename);
        echo $filename;
    }

    /* Balance Sheet */

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexBalanceSheet(Request $request)
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
        $data['bookyears'] = BookYear::get();
        return view('finance::reports.finances.balance_sheet', $data);
    }

    /**
     * Display a spesific view of the resource.
     * @return Renderable
     */
    public function indexBalanceSheetView(Request $request)
    {
        $bookyear = $this->getActiveBookYear();
        $data['profile'] = $this->getInstituteProfile();
        $data['requests'] = $request->all();
        $data['is_total'] = isset($request->is_total) ? true : false;
        $data['is_zero'] = isset($request->is_zero) ? true : false;
        $data['startdate'] = BookYear::find($request->bookyear_id);
        $data['lastdate'] = $this->formatDate($this->formatDate($request->end,'sys'),'month');
        //
        $data['cashbanks'] = $this->codeEloquent->accountBalance('1-1', $this->formatDate($request->end,'sys'), 0);
        $data['receivables'] = $this->codeEloquent->accountBalance('1-2', $this->formatDate($request->end,'sys'), 0);
        $data['assets'] = $this->codeEloquent->accountBalance('1-3', $this->formatDate($request->end,'sys'), 0);
        $data['depretiations'] = $this->codeEloquent->accountBalance('1-4', $this->formatDate($request->end,'sys'), 0);
        $data['liabilities'] = $this->codeEloquent->accountBalance('2-', $this->formatDate($request->end,'sys'), 0);
        $data['equities'] = $this->codeEloquent->accountBalance('3-1', $this->formatDate($request->end,'sys'), 0);
        $data['profits'] = $this->codeEloquent->listProfitLoss(4, $bookyear->start_date, $this->formatDate($request->end,'sys'), $bookyear->id);
        $data['losses'] = $this->codeEloquent->listProfitLoss(5, $bookyear->start_date, $this->formatDate($request->end,'sys'), $bookyear->id);
        return view('finance::reports.finances.balance_sheet_view', $data);
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfBalanceSheet(Request $request)
    {
        $bookyear = $this->getActiveBookYear();
        $payload = json_decode($request->data);
        $data['requests'] = array(
            'start' => $payload->form[1]->value,
            'end' => $payload->form[2]->value,
            'bookyear' => $payload->bookyear,
        );
        $data['startdate'] = BookYear::find($payload->form[0]->value);
        $data['lastdate'] = $this->formatDate($this->formatDate($payload->form[2]->value,'sys'),'month');
        $data['is_total'] = $this->subExist('is_total', $payload->form);
        $data['is_zero'] = $this->subExist('is_zero', $payload->form);
        $data['profile'] = $this->getInstituteProfile();
        $data['cashbanks'] = $this->codeEloquent->accountBalance('1-1', $this->formatDate($payload->form[2]->value,'sys'), 0);
        $data['receivables'] = $this->codeEloquent->accountBalance('1-2', $this->formatDate($payload->form[2]->value,'sys'), 0);
        $data['assets'] = $this->codeEloquent->accountBalance('1-3', $this->formatDate($payload->form[2]->value,'sys'), 0);
        $data['depretiations'] = $this->codeEloquent->accountBalance('1-4', $this->formatDate($payload->form[2]->value,'sys'), 0);
        $data['liabilities'] = $this->codeEloquent->accountBalance('2-', $this->formatDate($payload->form[2]->value,'sys'), 0);
        $data['equities'] = $this->codeEloquent->accountBalance('3-1', $this->formatDate($payload->form[2]->value,'sys'), 0);
        $data['profits'] = $this->codeEloquent->listProfitLoss(4, $bookyear->start_date, $this->formatDate($payload->form[2]->value,'sys'), $bookyear->id);
        $data['losses'] = $this->codeEloquent->listProfitLoss(5, $bookyear->start_date, $this->formatDate($payload->form[2]->value,'sys'), $bookyear->id);
        //
        $subject = Str::lower(config('app.name')) . '_laporan_neraca';
        $view = View::make('finance::reports.finances.balance_sheet_pdf', $data);
        $hashfile = md5(date('Ymdhis') . '_' . $subject);
        $filename = date('Ymdhis') . '_' . $subject . '.pdf';
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortraits($hashfile, $filename);
        echo $filename;
    }

    /**
     * Export resource to Excel Document.
     * @return Excel
     */
    public function toExcelBalanceSheet(Request $request)
    {
        $bookyear = $this->getActiveBookYear();
        $payload = json_decode($request->data);
        $data['requests'] = array(
            'start' => $payload->form[1]->value,
            'end' => $payload->form[2]->value,
            'bookyear' => $payload->bookyear,
        );
        $data['startdate'] = BookYear::find($payload->form[0]->value);
        $data['lastdate'] = $this->formatDate($this->formatDate($payload->form[2]->value,'sys'),'month');
        $data['is_total'] = $this->subExist('is_total', $payload->form);
        $data['is_zero'] = $this->subExist('is_zero', $payload->form);
        $data['profile'] = $this->getInstituteProfile();
        $data['cashbanks'] = $this->codeEloquent->accountBalance('1-1', $this->formatDate($payload->form[2]->value,'sys'), 0);
        $data['receivables'] = $this->codeEloquent->accountBalance('1-2', $this->formatDate($payload->form[2]->value,'sys'), 0);
        $data['assets'] = $this->codeEloquent->accountBalance('1-3', $this->formatDate($payload->form[2]->value,'sys'), 0);
        $data['depretiations'] = $this->codeEloquent->accountBalance('1-4', $this->formatDate($payload->form[2]->value,'sys'), 0);
        $data['liabilities'] = $this->codeEloquent->accountBalance('2-', $this->formatDate($payload->form[2]->value,'sys'), 0);
        $data['equities'] = $this->codeEloquent->accountBalance('3-1', $this->formatDate($payload->form[2]->value,'sys'), 0);
        $data['profits'] = $this->codeEloquent->listProfitLoss(4, $bookyear->start_date, $this->formatDate($payload->form[2]->value,'sys'), $bookyear->id);
        $data['losses'] = $this->codeEloquent->listProfitLoss(5, $bookyear->start_date, $this->formatDate($payload->form[2]->value,'sys'), $bookyear->id);
        //
        $subject = Str::lower(config('app.name')) . '_laporan_neraca';
        $filename = date('Ymdhis') . '_' . $subject . '.xlsx';
        $view = View::make('finance::reports.finances.balance_sheet_xlsx', $data);
        Storage::disk('local')->put('public/downloads/'.$filename, $view->render());
        echo $filename;
    }

    /* Trial Balance Sheet */

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexTrialBalance(Request $request)
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
        $data['bookyears'] = BookYear::get();
        return view('finance::reports.finances.trial_balance', $data);
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return JSON
     */
    public function dataTrialBalance(Request $request)
    {
        return response()->json($this->journalEloquent->dataTrialBalance($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfTrialBalance(Request $request)
    {
        $payload = json_decode($request->data);
        $data['requests'] = $payload;
        $data['profile'] = $this->getInstituteProfile();
        //
        $view = View::make('finance::reports.finances.trial_balance_pdf', $data);
        $name = Str::lower(config('app.name')) .'_neraca_percobaan';
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        //
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortraits($hashfile, $filename);
        echo $filename;
    }

    /**
     * Export resource to Excel Document.
     * @return Excel
     */
    public function toExcelTrialBalance(Request $request)
    {
        $payload = json_decode($request->data);
        //
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('laporan_neraca_percobaan');
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        //
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(60);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        //
        $sheet->mergeCells('A1:D1');
        $sheet->mergeCells('A2:D2');
        $sheet->mergeCells('A3:D3');
        //
        $sheet->setCellValue('A1', config('app.name') .' '. Str::upper($request->session()->get('institute')));
        $sheet->setCellValue('A2', 'NERACA PERCOBAAN - ' . $payload->start .' s.d '. $payload->end);
        $sheet->setCellValue('A3', 'TAHUN BUKU ' . $payload->bookyear . ' - MATA UANG RUPIAH (Rp)');
        $sheet->setCellValue('A5', 'KODE');
        $sheet->setCellValue('B5', 'NAMA');
        $sheet->setCellValue('C5', 'DEBIT');
        $sheet->setCellValue('D5', 'KREDIT');
        //
        $baris = 6;
        $number = 1;
        foreach ($payload->rows as $row) 
        {
            $sheet->setCellValue('A'.$baris,$row->code);
            $sheet->setCellValue('B'.$baris,$row->name);
            $sheet->setCellValue('C'.$baris,$this->removeFormat($row->debit));
            $sheet->setCellValue('D'.$baris,$this->removeFormat($row->credit));
            $baris++;
            $number++;
        }
        $sheet->setCellValue('B'.$baris,'TOTAL')->getStyle('B'.$baris.':B'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        $lastrow = $baris - 1;
        $sheet->setCellValue('C'.$baris,'=SUM(C6:C'.$lastrow.')')->getStyle('C'.$baris.':C'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        $sheet->setCellValue('D'.$baris,'=SUM(D6:D'.$lastrow.')')->getStyle('D'.$baris.':D'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        //
        $sheet->getStyle('A1:A2')->applyFromArray($this->PHPExcelCommonStyle()['title']);
        $sheet->getStyle('A3:A3')->applyFromArray($this->PHPExcelCommonStyle()['subTitle']);
        $sheet->getStyle('A5:D5')->applyFromArray($this->PHPExcelCommonStyle()['header']);
        $sheet->getStyle('A6:A'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('B6:B'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('C6:C'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
        $sheet->getStyle('C6:C'.$baris)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
        $sheet->getStyle('D6:D'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
        $sheet->getStyle('D6:D'.$baris)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
        //
        $filename = date('Ymdhis') . '_' . Str::lower(config('app.name')) . '_laporan_neraca_percobaan.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save('storage/downloads/'.$filename);
        echo $filename;
    }

    /* Equity Change */

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexEquityChange(Request $request)
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
        $data['bookyears'] = BookYear::get();
        return view('finance::reports.finances.equity_change', $data);
    }

    /**
     * Display a spesific view of the resource.
     * @return Renderable
     */
    public function indexEquityChangeView(Request $request)
    {
        $params = explode('-', $request->bookyear_id);
        $bookyear = BookYear::find($params[0]);
        $data['profile'] = $this->getInstituteProfile();
        $data['requests'] = $request->all();
        $data['startdate'] = $this->formatDate($this->formatDate($request->start,'sys'),'month');
        $data['lastdate'] = $this->formatDate($this->formatDate($request->end,'sys'),'iso');
        $data['equities'] = $this->journalEloquent->equityChange($bookyear->start_date, $this->formatDate($request->end,'sys'), $request->bookyear_id);
        $data['month'] = $this->formatDate($this->formatDate($request->end,'sys'),'monthyear');
        return view('finance::reports.finances.equity_change_view', $data);
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfEquityChange(Request $request)
    {
        $payload = json_decode($request->data);
        $data['requests'] = array(
            'start' => $payload->form[1]->value,
            'end' => $payload->form[2]->value,
            'bookyear' => $payload->bookyear,
        );
        $data['startdate'] = $this->formatDate($this->formatDate($payload->form[1]->value,'sys'),'month');
        $data['lastdate'] = $this->formatDate($this->formatDate($payload->form[2]->value,'sys'),'iso');
        $data['profile'] = $this->getInstituteProfile();
        $data['month'] = $this->formatDate($this->formatDate($payload->form[2]->value,'sys'),'monthyear');
        $data['equities'] = $this->journalEloquent->equityChange($this->formatDate($payload->form[1]->value,'sys'), $this->formatDate($payload->form[2]->value,'sys'), $payload->form[0]->value);
        // 
        $subject = Str::lower(config('app.name')) . '_laporan_perubahan_ekuitas';
        $view = View::make('finance::reports.finances.equity_change_pdf', $data);
        $hashfile = md5(date('Ymdhis') . '_' . $subject);
        $filename = date('Ymdhis') . '_' . $subject . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortrait($hashfile, $filename);
        echo $filename;
    }

    /**
     * Export resource to Excel Document.
     * @return Excel
     */
    public function toExcelEquityChange(Request $request)
    {
        $profile = $this->getInstituteProfile();
        $payload = json_decode($request->data);
        $startdate = $this->formatDate($this->formatDate($payload->form[1]->value,'sys'),'month');
        $lastdate = $this->formatDate($this->formatDate($payload->form[2]->value,'sys'),'iso');
        $equities = $this->journalEloquent->equityChange($this->formatDate($payload->form[1]->value,'sys'), $this->formatDate($payload->form[2]->value,'sys'), $payload->form[0]->value);
        $month = $this->formatDate($this->formatDate($payload->form[2]->value,'sys'),'monthyear');
        $subtotal = $equities[1]->value + $equities[2]->value + $equities[3]->value;
        $total = $equities[0]->value + $subtotal;
        //
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        //
        $sheet->setTitle('laporan_perubahan_ekuitas');
        $sheet->getColumnDimension('A')->setWidth(75);
        $sheet->getColumnDimension('B')->setWidth(30);

        $sheet->mergeCells('A1:B1');
        $sheet->mergeCells('A2:B2');
        $sheet->mergeCells('A3:B3');
        //
        $sheet->setCellValue('A1', config('app.name') .' '. Str::upper($request->session()->get('institute')));
        $sheet->setCellValue('A2', 'LAPORAN PERUBAHAN EKUITAS PEMILIK - ' . $payload->form[1]->value .' s.d '. $payload->form[2]->value);
        $sheet->setCellValue('A3', 'TAHUN BUKU ' . $payload->bookyear . ' - MATA UANG RUPIAH (Rp)');
        $sheet->setCellValue('A5', 'DESKRIPSI');
        $sheet->setCellValue('B5', 'NILAI');
        // profits
        $sheet->setCellValue('A6', 'Ekuitas Pemilik awal periode ' . $month)->getStyle('A6:A6')->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        $sheet->setCellValue('B6', $equities[0]->value)->getStyle('B6:B6')->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        $sheet->setCellValue('A7', 'Penambahan Ekuitas Pemilik')->getStyle('A7:A7')->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        $sheet->setCellValue('A8', '  Pendapatan Bersih pada '. $month);
        $sheet->setCellValue('B8', $equities[1]->value);
        $sheet->setCellValue('A9', '  Investasi Kurun Periode '. $month);
        $sheet->setCellValue('B9', $equities[2]->value);
        $sheet->setCellValue('A10', '  Penarikan pada '. $month);
        $sheet->setCellValue('B10', $equities[3]->value);
        $sheet->setCellValue('A11', 'Total Penambahan Ekuitas Pemilik')->getStyle('A11:A11')->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        $sheet->setCellValue('B11', $subtotal)->getStyle('B11:B11')->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        $sheet->setCellValue('A12', 'Ekuitas Pemilik per ' . $lastdate)->getStyle('A12:A12')->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        $sheet->setCellValue('B12', $total)->getStyle('B12:B12')->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        //
        $sheet->getStyle('A1:A2')->applyFromArray($this->PHPExcelCommonStyle()['title']);
        $sheet->getStyle('A3:A3')->applyFromArray($this->PHPExcelCommonStyle()['subTitle']);
        $sheet->getStyle('A5:B5')->applyFromArray($this->PHPExcelCommonStyle()['header']);
        $sheet->getStyle('A6:A12')->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('B6:B12')->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
        $sheet->getStyle('B6:B12')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
        $filename = date('Ymdhis') . '_' . Str::lower(config('app.name')) . '_laporan_perubahan_ekuitas.xlsx';
        //
        $writer = new Xlsx($spreadsheet);
        $writer->save('storage/downloads/'.$filename);
        echo $filename;
    }
    
    /* Cashflow */

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexCashFlow(Request $request)
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
        $data['bookyears'] = BookYear::get();
        return view('finance::reports.finances.cash_flow', $data);
    }

    /**
     * Display a spesific view of the resource.
     * @return Renderable
     */
    public function indexCashFlowView(Request $request)
    {
        $bookyear = BookYear::find($request->bookyear_id);
        $dates = explode('/', $request->end);
        $data['profile'] = $this->getInstituteProfile();
        $data['requests'] = $request->all();
        $data['startdate'] = $this->formatDate($this->formatDate($request->start,'sys'),'month');
        $data['lastdate'] = $this->formatDate($this->formatDate($request->end,'sys'),'iso');
        $data['start_date'] = $this->formatDate($dates[2].'-'.$dates[1].'-01','iso');
        $data['end_date'] = $this->formatDate($this->formatDate($request->end,'sys'),'iso');
        //
        $data['incomes'] = $this->journalEloquent->cashflowIncomes($dates[2].'-'.$dates[1].'-01', $this->formatDate($request->end,'sys'), $request->bookyear_id);
        $data['expense'] = $this->journalEloquent->cashflowExpense($dates[2].'-'.$dates[1].'-01', $this->formatDate($request->end,'sys'), $request->bookyear_id);
        $data['receivables'] = $this->journalEloquent->cashflowReceivables($dates[2].'-'.$dates[1].'-01', $this->formatDate($request->end,'sys'), $request->bookyear_id);
        $data['receivables_reduce'] = $this->journalEloquent->cashflowReceivablesReduce($dates[2].'-'.$dates[1].'-01', $this->formatDate($request->end,'sys'), $request->bookyear_id);
        $data['payable_reduce'] = $this->journalEloquent->cashflowPayableReduce($dates[2].'-'.$dates[1].'-01', $this->formatDate($request->end,'sys'), $request->bookyear_id);
        $data['payable_raise'] = $this->journalEloquent->cashflowPayableRaise($dates[2].'-'.$dates[1].'-01', $this->formatDate($request->end,'sys'), $request->bookyear_id);
        $data['equities'] = $this->journalEloquent->cashflowEquities($dates[2].'-'.$dates[1].'-01', $this->formatDate($request->end,'sys'), $request->bookyear_id);
        $data['equities_withdrawal'] = $this->journalEloquent->cashflowEquitiesWithdrawal($dates[2].'-'.$dates[1].'-01', $this->formatDate($request->end,'sys'), $request->bookyear_id);
        $data['investments'] = $this->journalEloquent->cashflowInvestment($dates[2].'-'.$dates[1].'-01', $this->formatDate($request->end,'sys'), $request->bookyear_id);
        $data['begin_balance'] = $this->journalEloquent->cashflowBeginBalance($this->formatDate($request->start,'sys'), $this->formatDate($request->end,'sys'), $request->bookyear_id);
        return view('finance::reports.finances.cash_flow_view', $data);
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfCashFlow(Request $request)
    {
        $payload = json_decode($request->data);
        $bookyear = BookYear::find($payload->form[0]->value);
        $dates = explode('/', $payload->form[2]->value);
        $data['requests'] = array(
            'start' => $payload->form[1]->value,
            'end' => $payload->form[2]->value,
            'bookyear' => $payload->bookyear,
        );
        $data['startdate'] = $this->formatDate($this->formatDate($payload->form[1]->value,'sys'),'month');
        $data['enddate'] = $this->formatDate($this->formatDate($payload->form[2]->value,'sys'),'iso');
        $data['start_date'] = $this->formatDate($dates[2].'-'.$dates[1].'-01','iso');
        $data['end_date'] = $this->formatDate($this->formatDate($payload->form[2]->value,'sys'),'iso');
        //
        $data['profile'] = $this->getInstituteProfile();
        $data['incomes'] = $this->journalEloquent->cashflowIncomes($dates[2].'-'.$dates[1].'-01', $this->formatDate($payload->form[2]->value,'sys'), $payload->form[0]->value);
        $data['expense'] = $this->journalEloquent->cashflowExpense($dates[2].'-'.$dates[1].'-01', $this->formatDate($payload->form[2]->value,'sys'), $payload->form[0]->value);
        $data['receivables'] = $this->journalEloquent->cashflowReceivables($dates[2].'-'.$dates[1].'-01', $this->formatDate($payload->form[2]->value,'sys'), $payload->form[0]->value);
        $data['receivables_reduce'] = $this->journalEloquent->cashflowReceivablesReduce($dates[2].'-'.$dates[1].'-01', $this->formatDate($payload->form[2]->value,'sys'), $payload->form[0]->value);
        $data['payable_reduce'] = $this->journalEloquent->cashflowPayableReduce($dates[2].'-'.$dates[1].'-01', $this->formatDate($payload->form[2]->value,'sys'), $payload->form[0]->value);
        $data['payable_raise'] = $this->journalEloquent->cashflowPayableRaise($dates[2].'-'.$dates[1].'-01', $this->formatDate($payload->form[2]->value,'sys'), $payload->form[0]->value);
        $data['equities'] = $this->journalEloquent->cashflowEquities($dates[2].'-'.$dates[1].'-01', $this->formatDate($payload->form[2]->value,'sys'), $payload->form[0]->value);
        $data['equities_withdrawal'] = $this->journalEloquent->cashflowEquitiesWithdrawal($dates[2].'-'.$dates[1].'-01', $this->formatDate($payload->form[2]->value,'sys'), $payload->form[0]->value);
        $data['investments'] = $this->journalEloquent->cashflowInvestment($dates[2].'-'.$dates[1].'-01', $this->formatDate($payload->form[2]->value,'sys'), $payload->form[0]->value);
        $data['begin_balance'] = $this->journalEloquent->cashflowBeginBalance($this->formatDate($payload->form[1]->value,'sys'), $this->formatDate($payload->form[2]->value,'sys'), $payload->form[0]->value);        
        // 
        $subject = Str::lower(config('app.name')) . '_laporan_arus_kas';
        $view = View::make('finance::reports.finances.cash_flow_pdf', $data);
        $hashfile = md5(date('Ymdhis') . '_' . $subject);
        $filename = date('Ymdhis') . '_' . $subject . '.pdf';
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortrait($hashfile, $filename);
        echo $filename;
    }

    /**
     * Export resource to Excel Document.
     * @return Excel
     */
    public function toExcelCashFlow(Request $request)
    {
        $payload = json_decode($request->data);
        $bookyear = BookYear::find($payload->form[0]->value);
        $dates = explode('/', $payload->form[2]->value);
        $startdate = $this->formatDate($this->formatDate($payload->form[1]->value,'sys'),'month');
        $enddate = $this->formatDate($this->formatDate($payload->form[2]->value,'sys'),'iso');
        $start_date = $this->formatDate($dates[2].'-'.$dates[1].'-01','iso');
        $end_date = $this->formatDate($this->formatDate($payload->form[2]->value,'sys'),'iso');
        $profile = $this->getInstituteProfile();
        //        
        $incomes = $this->journalEloquent->cashflowIncomes($dates[2].'-'.$dates[1].'-01', $this->formatDate($payload->form[2]->value,'sys'), $payload->form[0]->value);
        $expense = $this->journalEloquent->cashflowExpense($dates[2].'-'.$dates[1].'-01', $this->formatDate($payload->form[2]->value,'sys'), $payload->form[0]->value);
        $receivables = $this->journalEloquent->cashflowReceivables($dates[2].'-'.$dates[1].'-01', $this->formatDate($payload->form[2]->value,'sys'), $payload->form[0]->value);
        $receivables_reduce = $this->journalEloquent->cashflowReceivablesReduce($dates[2].'-'.$dates[1].'-01', $this->formatDate($payload->form[2]->value,'sys'), $payload->form[0]->value);
        $payable_reduce = $this->journalEloquent->cashflowPayableReduce($dates[2].'-'.$dates[1].'-01', $this->formatDate($payload->form[2]->value,'sys'), $payload->form[0]->value);
        $payable_raise = $this->journalEloquent->cashflowPayableRaise($dates[2].'-'.$dates[1].'-01', $this->formatDate($payload->form[2]->value,'sys'), $payload->form[0]->value);
        $equities = $this->journalEloquent->cashflowEquities($dates[2].'-'.$dates[1].'-01', $this->formatDate($payload->form[2]->value,'sys'), $payload->form[0]->value);
        $equities_withdrawal = $this->journalEloquent->cashflowEquitiesWithdrawal($dates[2].'-'.$dates[1].'-01', $this->formatDate($payload->form[2]->value,'sys'), $payload->form[0]->value);
        $investments = $this->journalEloquent->cashflowInvestment($dates[2].'-'.$dates[1].'-01', $this->formatDate($payload->form[2]->value,'sys'), $payload->form[0]->value);
        $begin_balance = $this->journalEloquent->cashflowBeginBalance($this->formatDate($payload->form[1]->value,'sys'), $this->formatDate($payload->form[2]->value,'sys'), $payload->form[0]->value);        
        //
        $total_income = 0;
        $total_receivable = 0;
        $total_receivable_reduce = 0;
        $total_equity = 0;
        $total_equity_withdrawal = 0;
        $total_investment = 0;
        //
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        //
        $sheet->setTitle('laporan_arus_kas');
        $sheet->getColumnDimension('A')->setWidth(75);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->mergeCells('A1:B1');
        $sheet->mergeCells('A2:B2');
        $sheet->mergeCells('A3:B3');
        //
        $sheet->setCellValue('A1', config('app.name') .' '. Str::upper($request->session()->get('institute')));
        $sheet->setCellValue('A2', 'LAPORAN ARUS KAS - '. $startdate . ' s/d ' . $enddate);
        $sheet->setCellValue('A3', 'TAHUN BUKU ' . $payload->bookyear . ' - MATA UANG RUPIAH (Rp)');
        //        
        $sheet->setCellValue('A5', 'DESKRIPSI');
        $sheet->setCellValue('B5', 'NILAI');
        $baris = 7;
        // assets
        $sheet->setCellValue('A6', 'Aktifitas Operasi')->getStyle('A6:A6')->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        foreach ($incomes as $income)
        {
            $total_income += $income->value;
            $sheet->setCellValue('A'.$baris,'    Kas diterima dari '.$income->name);
            $sheet->setCellValue('B'.$baris,$income->value);
            $baris++;
        }
        $sheet->setCellValue('A'.$baris, '    Pembayaran Beban');
        $sheet->setCellValue('B'.$baris, $expense);
        $sheet->setCellValue('A'.$baris = $baris + 1, 'Arus Kas Bersih dari Aktifitas Operasi')->getStyle('A'.$baris.':A'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        $sheet->setCellValue('B'.$baris, $total_income + $expense)->getStyle('B'.$baris.':B'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        //
        $baris_2 = $baris + 2;
        $baris_2_ = $baris + 3;
        $sheet->setCellValue('A'.$baris_2, 'Aktifitas Keuangan')->getStyle('A'.$baris_2.':A'.$baris_2)->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        foreach ($receivables as $receivable)
        {
            $total_receivable += $receivable->value; 
            $sheet->setCellValue('A'.$baris_2_,'    Penambahan Piutang Usaha');
            $sheet->setCellValue('B'.$baris_2_,$receivable->value);
            $baris_2_++;
        }
        $baris_3 = $baris_2_;
        foreach ($receivables_reduce as $receivable)
        {
            $total_receivable_reduce += $receivable->value;
            $sheet->setCellValue('A'.$baris_2_,'    Pengurangan Piutang Usaha');
            $sheet->setCellValue('B'.$baris_2_,$receivable->value);
            $baris_3++;
        }
        $sheet->setCellValue('A'.$baris_3, '    Penurunan Hutang');
        $sheet->setCellValue('B'.$baris_3, $payable_reduce);
        $sheet->setCellValue('A'.$baris_3 = $baris_3 + 1, '    Kenaikan Hutang');
        $sheet->setCellValue('B'.$baris_3, $payable_raise);
        $sheet->setCellValue('A'.$baris_3 = $baris_3 + 1, 'Arus Kas Bersih dari Aktifitas Keuangan')->getStyle('A'.$baris_3.':A'.$baris_3)->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        $sheet->setCellValue('B'.$baris_3, $total_receivable + $total_receivable_reduce + $payable_reduce + $payable_raise)->getStyle('B'.$baris_3.':B'.$baris_3)->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        //
        $baris_4 = $baris_3 + 2;
        $sheet->setCellValue('A'.$baris_4, 'Aktifitas Investasi')->getStyle('A'.$baris_4.':A'.$baris_4)->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        foreach ($equities as $equity)
        {
            $total_equity += $equity->value;
            $sheet->setCellValue('A'.$baris_4 = $baris_4 + 1,'    Kas diterima dari penambahan ' . $equity->name);
            $sheet->setCellValue('B'.$baris_4,$equity->value);
            $baris_4++;
        }
        $baris_5 = $baris_4 + 1;
        foreach ($equities_withdrawal as $equity)
        {
            $total_equity_withdrawal += $equity->value;
            $sheet->setCellValue('A'.$baris_5 = $baris_5 - 1,'    Pengurangan Kas dari pengambilan ' . $equity->name);
            $sheet->setCellValue('B'.$baris_5,$equity->value);
            $baris_5++;
        }
        $baris_6 = $baris_5 + 1;
        foreach ($investments as $investment)
        {
            $total_investment += $investment->value;
            $sheet->setCellValue('A'.$baris_6,$investment->name);
            $sheet->setCellValue('B'.$baris_6,$investment->value);
            $baris_6++;
        }
        $sheet->setCellValue('A'.$baris_6 = $baris_6 - 1, 'Arus Kas bersih dari Aktifitas Investasi')->getStyle('A'.$baris_6.':A'.$baris_6)->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        $sheet->setCellValue('B'.$baris_6, $total_equity + $total_equity_withdrawal + $total_investment)->getStyle('B'.$baris_6.':B'.$baris_6)->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        $sheet->setCellValue('A'.$baris_6 = $baris_6 + 2, 'Perubahan Kas')->getStyle('A'.$baris_6.':A'.$baris_6)->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        $sheet->setCellValue('B'.$baris_6, ($total_income + $expense) + ($total_receivable + $total_receivable_reduce + $payable_reduce + $payable_raise) + ($total_equity + $total_equity_withdrawal + $total_investment))->getStyle('B'.$baris_6.':B'.$baris_6)->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        $sheet->setCellValue('A'.$baris_6 = $baris_6 + 1, 'Saldo Kas per ' . $start_date)->getStyle('A'.$baris_6.':A'.$baris_6)->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        $sheet->setCellValue('B'.$baris_6, $begin_balance->value)->getStyle('B'.$baris_6.':B'.$baris_6)->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        $sheet->setCellValue('A'.$baris_6 = $baris_6 + 1, 'Saldo Kas per ' . $end_date)->getStyle('A'.$baris_6.':A'.$baris_6)->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        $sheet->setCellValue('B'.$baris_6, $begin_balance->value + ($total_income + $expense) + ($total_receivable + $total_receivable_reduce + $payable_reduce + $payable_raise) + ($total_equity + $total_equity_withdrawal + $total_investment))->getStyle('B'.$baris_6.':B'.$baris_6)->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        //
        $sheet->getStyle('A1:A2')->applyFromArray($this->PHPExcelCommonStyle()['title']);
        $sheet->getStyle('A3:A3')->applyFromArray($this->PHPExcelCommonStyle()['subTitle']);
        $sheet->getStyle('A5:B5')->applyFromArray($this->PHPExcelCommonStyle()['header']);
        $sheet->getStyle('B6:B'.$baris_6)->applyFromArray($this->PHPExcelCommonStyle()['contentRight']);
        $sheet->getStyle('B6:B'.$baris_6)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
        $filename = date('Ymdhis') . '_' . Str::lower(config('app.name')) . '_laporan_arus_kas.xlsx';
        //
        $writer = new Xlsx($spreadsheet);
        $writer->save('storage/downloads/'.$filename);
        echo $filename;
    }

    /* Audit */

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexAudit(Request $request)
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
        $data['bookyears'] = BookYear::orderByDesc('id')->get();
        $data['departments'] = $this->listDepartment();
        return view('finance::reports.audits.audit', $data);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexAuditView(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        switch ($request->source) 
        {
            case 'major_jtt_prospect':
                $data['audits'] = $this->auditEloquent->dataAuditPaymentMajor($request->source, $request->bookyear_id, $request->department_id, $request->start_date, $request->end_date);
                $data['detail_audits'] = DB::table('finance.audit_payment_majors')->get();
                break;
            case 'receipt_jtt':
                $data['audits'] = $this->auditEloquent->dataAuditReceiptMajor($request->source, $request->bookyear_id, $request->department_id, $request->start_date, $request->end_date);
                $data['detail_audits'] = DB::table('finance.audit_receipt_majors')->get();
                break;
            case 'receipt_jtt_prospect':
                $data['audits'] = $this->auditEloquent->dataAuditReceiptMajor($request->source, $request->bookyear_id, $request->department_id, $request->start_date, $request->end_date);
                $data['detail_audits'] = DB::table('finance.audit_receipt_majors')->get();
                break;
            case 'receipt_skr':
                $data['audits'] = $this->auditEloquent->dataAuditReceiptVoluntary($request->source, $request->bookyear_id, $request->department_id, $request->start_date, $request->end_date);
                $data['detail_audits'] = DB::table('finance.audit_receipt_voluntaries')->get();
                break;
            case 'receipt_skr_prospect':
                $data['audits'] = $this->auditEloquent->dataAuditReceiptVoluntary($request->source, $request->bookyear_id, $request->department_id, $request->start_date, $request->end_date);
                $data['detail_audits'] = DB::table('finance.audit_receipt_voluntaries')->get();
                break;
            case 'receipt_other':
                $data['audits'] = $this->auditEloquent->dataAuditReceiptOther($request->source, $request->bookyear_id, $request->department_id, $request->start_date, $request->end_date);
                $data['detail_audits'] = DB::table('finance.audit_receipt_others')->get();
                break;
            case 'expense':
                $data['audits'] = $this->auditEloquent->dataAuditExpense($request->source, $request->bookyear_id, $request->department_id, $request->start_date, $request->end_date);
                $data['detail_audits'] = DB::table('finance.audit_expenditures')->get();
                break;
            case 'savingdeposit':
                $data['audits'] = $this->auditEloquent->dataAuditSaving($request->source, $request->bookyear_id, $request->department_id, $request->start_date, $request->end_date);
                $data['detail_audits'] = DB::table('finance.audit_savings')->get();
                break;
            case 'savingwithdrawal':
                $data['audits'] = $this->auditEloquent->dataAuditSaving($request->source, $request->bookyear_id, $request->department_id, $request->start_date, $request->end_date);
                $data['detail_audits'] = DB::table('finance.audit_savings')->get();
                break;
            case 'journalvoucher':
                $data['audits'] = $this->auditEloquent->dataAuditJournalVoucher($request->source, $request->bookyear_id, $request->department_id, $request->start_date, $request->end_date);
                $data['detail_audits'] = DB::table('finance.audit_journals')->get();
                $data['detail_audits_sub'] = DB::table('finance.audit_journal_details')->select(
                                                    'finance.codes.code',
                                                    'finance.codes.name',
                                                    'finance.audit_journal_details.audit_id',
                                                    'finance.audit_journal_details.is_status',
                                                    'finance.audit_journal_details.debit',
                                                    'finance.audit_journal_details.credit'
                                                )
                                                ->join('finance.codes','finance.codes.id','=','finance.audit_journal_details.account_id')
                                                ->get();
                break;
            case 'begin_balance':
                // 
                break;
            default:
                $data['audits'] = $this->auditEloquent->dataAuditPaymentMajor($request->source, $request->bookyear_id, $request->department_id, $request->start_date, $request->end_date);
                $data['detail_audits'] = DB::table('finance.audit_payment_majors')->get();
                break;
        }
        $data['requests'] = $request->all();
        return view('finance::reports.audits.audit_'.$request->source, $data);
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return JSON
     */
    public function dataAudit(Request $request)
    {
        return response()->json($this->auditEloquent->data($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfAudit(Request $request)
    {
        $payload = json_decode($request->data);
        $data['requests'] = $payload;
        switch ($payload->source) 
        {
            case 'major_jtt_prospect':
                $data['audits'] = $this->auditEloquent->dataAuditPaymentMajor($payload->source, $payload->bookyear_id, $payload->department_id, $payload->start_date, $payload->end_date);
                $data['detail_audits'] = DB::table('finance.audit_payment_majors')->get();
                break;
            case 'receipt_jtt':
                $data['audits'] = $this->auditEloquent->dataAuditReceiptMajor($payload->source, $payload->bookyear_id, $payload->department_id, $payload->start_date, $payload->end_date);
                $data['detail_audits'] = DB::table('finance.audit_receipt_majors')->get();
                break;
            case 'receipt_jtt_prospect':
                $data['audits'] = $this->auditEloquent->dataAuditReceiptMajor($payload->source, $payload->bookyear_id, $payload->department_id, $payload->start_date, $payload->end_date);
                $data['detail_audits'] = DB::table('finance.audit_receipt_majors')->get();
                break;
            case 'receipt_skr':
                $data['audits'] = $this->auditEloquent->dataAuditReceiptVoluntary($payload->source, $payload->bookyear_id, $payload->department_id, $payload->start_date, $payload->end_date);
                $data['detail_audits'] = DB::table('finance.audit_receipt_voluntaries')->get();
                break;
            case 'receipt_skr_prospect':
                $data['audits'] = $this->auditEloquent->dataAuditReceiptVoluntary($payload->source, $payload->bookyear_id, $payload->department_id, $payload->start_date, $payload->end_date);
                $data['detail_audits'] = DB::table('finance.audit_receipt_voluntaries')->get();
                break;
            case 'receipt_other':
                $data['audits'] = $this->auditEloquent->dataAuditReceiptOther($payload->source, $payload->bookyear_id, $payload->department_id, $payload->start_date, $payload->end_date);
                $data['detail_audits'] = DB::table('finance.audit_receipt_others')->get();
                break;
            case 'expense':
                $data['audits'] = $this->auditEloquent->dataAuditExpense($payload->source, $payload->bookyear_id, $payload->department_id, $payload->start_date, $payload->end_date);
                $data['detail_audits'] = DB::table('finance.audit_expenditures')->get();
                break;
            case 'savingdeposit':
                $data['audits'] = $this->auditEloquent->dataAuditSaving($payload->source, $payload->bookyear_id, $payload->department_id, $payload->start_date, $payload->end_date);
                $data['detail_audits'] = DB::table('finance.audit_savings')->get();
                break;
            case 'savingwithdrawal':
                $data['audits'] = $this->auditEloquent->dataAuditSaving($payload->source, $payload->bookyear_id, $payload->department_id, $payload->start_date, $payload->end_date);
                $data['detail_audits'] = DB::table('finance.audit_savings')->get();
                break;
            case 'journalvoucher':
                $data['audits'] = $this->auditEloquent->dataAuditJournalVoucher($payload->source, $payload->bookyear_id, $payload->department_id, $payload->start_date, $payload->end_date);
                $data['detail_audits'] = DB::table('finance.audit_journals')->get();
                $data['detail_audits_sub'] = DB::table('finance.audit_journal_details')->select(
                                                    'finance.codes.code',
                                                    'finance.codes.name',
                                                    'finance.audit_journal_details.audit_id',
                                                    'finance.audit_journal_details.is_status',
                                                    'finance.audit_journal_details.debit',
                                                    'finance.audit_journal_details.credit'
                                                )
                                                ->join('finance.codes','finance.codes.id','=','finance.audit_journal_details.account_id')
                                                ->get();
                break;
            case 'begin_balance':
                //
                break;
            default:
                $data['audits'] = $this->auditEloquent->dataAuditPaymentMajor($payload->source, $payload->bookyear_id, $payload->department_id, $payload->start_date, $payload->end_date);
                $data['detail_audits'] = DB::table('finance.audit_payment_majors')->get();
                break;
        }
        $data['profile'] = $this->getInstituteProfile();
        // 
        $view = View::make('finance::reports.audits.audit_'.$payload->source.'_pdf', $data);
        $name = Str::lower(config('app.name')) .'_laporan_audit_perubahan';
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
    public function toExcelAudit(Request $request)
    {
        $payload = json_decode($request->data);
        //
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('laporan_audit_perubahan_data');
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        //
        $sheet->setCellValue('A1', config('app.name') .' '. Str::upper($request->session()->get('institute')));
        $sheet->setCellValue('A2', 'AUDIT PERUBAHAN DATA ' . Str::upper($this->getTransactionSource()[$payload->source]) );
        $sheet->setCellValue('A3', 'DEPARTEMEN ' . $payload->department . ' - TAHUN BUKU ' . $payload->bookyear . ' - PERIODE ' . $payload->start_date . ' s/d ' . $payload->end_date);
        //
        switch ($payload->source) 
        {
            case 'major_jtt_prospect':
                $audits = $this->auditEloquent->dataAuditPaymentMajor($payload->source, $payload->bookyear_id, $payload->department_id, $payload->start_date, $payload->end_date);
                $detail_audits = DB::table('finance.audit_payment_majors')->get();
                //
                $sheet->getColumnDimension('A')->setWidth(10);
                $sheet->getColumnDimension('B')->setWidth(30);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(50);
                $sheet->getColumnDimension('F')->setWidth(30);
                //
                $sheet->mergeCells('A1:F1');
                $sheet->mergeCells('A2:F2');
                $sheet->mergeCells('A3:F3');
                //
                $sheet->setCellValue('A5', 'NO');
                $sheet->setCellValue('B5', 'STATUS DATA');
                $sheet->setCellValue('C5', 'TANGGAL');
                $sheet->setCellValue('D5', 'JUMLAH');
                $sheet->setCellValue('E5', 'KETERANGAN');
                $sheet->setCellValue('F5', 'PETUGAS');
                //
                $baris = 6;
                $number = 1;
                foreach ($audits as $audit) 
                {
                    $sheet->mergeCells('A'.$baris.':A'.$baris + 4);
                    $sheet->setCellValue('A'.$baris,$number);
                    $sheet->setCellValue('B'.$baris,'Perubahan dilakukan oleh ' . $audit->employee . ' tanggal ' . $this->formatDate($audit->audit_date,'isotime'))->mergeCells('B'.$baris.':F'.$baris);
                    $sheet->setCellValue('B'.$baris = $baris + 1, 'No. Jurnal: '. $audit->cash_no . ' - Alasan: ' . $audit->remark)->mergeCells('B'.$baris.':F'.$baris);
                    $sheet->setCellValue('B'.$baris = $baris + 1, 'Transaksi: '. $audit->transaction)->mergeCells('B'.$baris.':F'.$baris);
                    foreach ($detail_audits as $detail)
                    {
                        if ($audit->id == $detail->audit_id)
                        {
                            $sheet->setCellValue('B'.$baris = $baris + 1, $detail->is_status == 0 ? 'Data Lama' : 'Data Perubahan');
                            $sheet->setCellValue('C'.$baris, $this->formatDate($audit->audit_date,'timeiso'));
                            $sheet->setCellValue('D'.$baris, number_format($detail->total,2));
                            $sheet->setCellValue('E'.$baris, $detail->remark);
                            $sheet->setCellValue('F'.$baris, $detail->employee);
                        }
                    }
                    $baris++;
                    $number++;
                }
                $sheet->getStyle('A6:A'.$baris = $baris - 1)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
                $sheet->getStyle('B6:B'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
                $sheet->getStyle('C6:C'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
                $sheet->getStyle('D6:D'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
                $sheet->getStyle('D6:D'.$baris)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
                $sheet->getStyle('E6:E'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
                $sheet->getStyle('F6:F'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
                $sheet->getStyle('A5:F5')->applyFromArray($this->PHPExcelCommonStyle()['header']);
                break;
            case 'receipt_jtt':
                $audits = $this->auditEloquent->dataAuditReceiptMajor($payload->source, $payload->bookyear_id, $payload->department_id, $payload->start_date, $payload->end_date);
                $detail_audits = DB::table('finance.audit_receipt_majors')->get();
                //
                $sheet->getColumnDimension('A')->setWidth(10);
                $sheet->getColumnDimension('B')->setWidth(30);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(50);
                $sheet->getColumnDimension('F')->setWidth(30);
                //
                $sheet->mergeCells('A1:F1');
                $sheet->mergeCells('A2:F2');
                $sheet->mergeCells('A3:F3');
                //
                $sheet->setCellValue('A5', 'NO');
                $sheet->setCellValue('B5', 'STATUS DATA');
                $sheet->setCellValue('C5', 'TANGGAL');
                $sheet->setCellValue('D5', 'JUMLAH');
                $sheet->setCellValue('E5', 'KETERANGAN');
                $sheet->setCellValue('F5', 'PETUGAS');
                //
                $baris = 6;
                $number = 1;
                foreach ($audits as $audit) 
                {
                    $sheet->mergeCells('A'.$baris.':A'.$baris + 4);
                    $sheet->setCellValue('A'.$baris,$number);
                    $sheet->setCellValue('B'.$baris,'Perubahan dilakukan oleh ' . $audit->employee . ' tanggal ' . $this->formatDate($audit->audit_date,'isotime'))->mergeCells('B'.$baris.':F'.$baris);
                    $sheet->setCellValue('B'.$baris = $baris + 1, 'No. Jurnal: '. $audit->cash_no . ' - Alasan: ' . $audit->remark)->mergeCells('B'.$baris.':F'.$baris);
                    $sheet->setCellValue('B'.$baris = $baris + 1, 'Transaksi: '. $audit->transaction)->mergeCells('B'.$baris.':F'.$baris);
                    foreach ($detail_audits as $detail)
                    {
                        if ($audit->id == $detail->audit_id)
                        {
                            $sheet->setCellValue('B'.$baris = $baris + 1, $detail->is_status == 0 ? 'Data Lama' : 'Data Perubahan');
                            $sheet->setCellValue('C'.$baris, $this->formatDate($audit->audit_date,'timeiso'));
                            $sheet->setCellValue('D'.$baris, number_format($detail->total,2));
                            $sheet->setCellValue('E'.$baris, $detail->remark);
                            $sheet->setCellValue('F'.$baris, $detail->employee);
                        }
                    }
                    $baris++;
                    $number++;
                }
                $sheet->getStyle('A6:A'.$baris = $baris - 1)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
                $sheet->getStyle('B6:B'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
                $sheet->getStyle('C6:C'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
                $sheet->getStyle('D6:D'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
                $sheet->getStyle('D6:D'.$baris)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
                $sheet->getStyle('E6:E'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
                $sheet->getStyle('F6:F'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
                $sheet->getStyle('A5:F5')->applyFromArray($this->PHPExcelCommonStyle()['header']);
                break;
            case 'receipt_jtt_prospect':
                $audits = $this->auditEloquent->dataAuditReceiptMajor($payload->source, $payload->bookyear_id, $payload->department_id, $payload->start_date, $payload->end_date);
                $detail_audits = DB::table('finance.audit_receipt_majors')->get();
                //
                $sheet->getColumnDimension('A')->setWidth(10);
                $sheet->getColumnDimension('B')->setWidth(30);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(50);
                $sheet->getColumnDimension('F')->setWidth(30);
                //
                $sheet->mergeCells('A1:F1');
                $sheet->mergeCells('A2:F2');
                $sheet->mergeCells('A3:F3');
                //
                $sheet->setCellValue('A5', 'NO');
                $sheet->setCellValue('B5', 'STATUS DATA');
                $sheet->setCellValue('C5', 'TANGGAL');
                $sheet->setCellValue('D5', 'JUMLAH');
                $sheet->setCellValue('E5', 'KETERANGAN');
                $sheet->setCellValue('F5', 'PETUGAS');
                //
                $baris = 6;
                $number = 1;
                foreach ($audits as $audit) 
                {
                    $sheet->mergeCells('A'.$baris.':A'.$baris + 4);
                    $sheet->setCellValue('A'.$baris,$number);
                    $sheet->setCellValue('B'.$baris,'Perubahan dilakukan oleh ' . $audit->employee . ' tanggal ' . $this->formatDate($audit->audit_date,'isotime'))->mergeCells('B'.$baris.':F'.$baris);
                    $sheet->setCellValue('B'.$baris = $baris + 1, 'No. Jurnal: '. $audit->cash_no . ' - Alasan: ' . $audit->remark)->mergeCells('B'.$baris.':F'.$baris);
                    $sheet->setCellValue('B'.$baris = $baris + 1, 'Transaksi: '. $audit->transaction)->mergeCells('B'.$baris.':F'.$baris);
                    foreach ($detail_audits as $detail)
                    {
                        if ($audit->id == $detail->audit_id)
                        {
                            $sheet->setCellValue('B'.$baris = $baris + 1, $detail->is_status == 0 ? 'Data Lama' : 'Data Perubahan');
                            $sheet->setCellValue('C'.$baris, $this->formatDate($audit->audit_date,'timeiso'));
                            $sheet->setCellValue('D'.$baris, number_format($detail->total,2));
                            $sheet->setCellValue('E'.$baris, $detail->remark);
                            $sheet->setCellValue('F'.$baris, $detail->employee);
                        }
                    }
                    $baris++;
                    $number++;
                }
                $sheet->getStyle('A6:A'.$baris = $baris - 1)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
                $sheet->getStyle('B6:B'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
                $sheet->getStyle('C6:C'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
                $sheet->getStyle('D6:D'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
                $sheet->getStyle('D6:D'.$baris)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
                $sheet->getStyle('E6:E'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
                $sheet->getStyle('F6:F'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
                $sheet->getStyle('A5:F5')->applyFromArray($this->PHPExcelCommonStyle()['header']);
                break;
            case 'receipt_skr':
                $audits = $this->auditEloquent->dataAuditReceiptVoluntary($payload->source, $payload->bookyear_id, $payload->department_id, $payload->start_date, $payload->end_date);
                $detail_audits = DB::table('finance.audit_receipt_voluntaries')->get();
                //
                $sheet->getColumnDimension('A')->setWidth(10);
                $sheet->getColumnDimension('B')->setWidth(30);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(50);
                $sheet->getColumnDimension('F')->setWidth(30);
                //
                $sheet->mergeCells('A1:F1');
                $sheet->mergeCells('A2:F2');
                $sheet->mergeCells('A3:F3');
                //
                $sheet->setCellValue('A5', 'NO');
                $sheet->setCellValue('B5', 'STATUS DATA');
                $sheet->setCellValue('C5', 'TANGGAL');
                $sheet->setCellValue('D5', 'JUMLAH');
                $sheet->setCellValue('E5', 'KETERANGAN');
                $sheet->setCellValue('F5', 'PETUGAS');
                //
                $baris = 6;
                $number = 1;
                foreach ($audits as $audit) 
                {
                    $sheet->mergeCells('A'.$baris.':A'.$baris + 4);
                    $sheet->setCellValue('A'.$baris,$number);
                    $sheet->setCellValue('B'.$baris,'Perubahan dilakukan oleh ' . $audit->employee . ' tanggal ' . $this->formatDate($audit->audit_date,'isotime'))->mergeCells('B'.$baris.':F'.$baris);
                    $sheet->setCellValue('B'.$baris = $baris + 1, 'No. Jurnal: '. $audit->cash_no . ' - Alasan: ' . $audit->remark)->mergeCells('B'.$baris.':F'.$baris);
                    $sheet->setCellValue('B'.$baris = $baris + 1, 'Transaksi: '. $audit->transaction)->mergeCells('B'.$baris.':F'.$baris);
                    foreach ($detail_audits as $detail)
                    {
                        if ($audit->id == $detail->audit_id)
                        {
                            $sheet->setCellValue('B'.$baris = $baris + 1, $detail->is_status == 0 ? 'Data Lama' : 'Data Perubahan');
                            $sheet->setCellValue('C'.$baris, $this->formatDate($audit->audit_date,'timeiso'));
                            $sheet->setCellValue('D'.$baris, number_format($detail->total,2));
                            $sheet->setCellValue('E'.$baris, $detail->remark);
                            $sheet->setCellValue('F'.$baris, $detail->employee);
                        }
                    }
                    $baris++;
                    $number++;
                }
                $sheet->getStyle('A6:A'.$baris = $baris - 1)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
                $sheet->getStyle('B6:B'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
                $sheet->getStyle('C6:C'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
                $sheet->getStyle('D6:D'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
                $sheet->getStyle('D6:D'.$baris)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
                $sheet->getStyle('E6:E'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
                $sheet->getStyle('F6:F'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
                $sheet->getStyle('A5:F5')->applyFromArray($this->PHPExcelCommonStyle()['header']);
                break;
            case 'receipt_skr_prospect':
                $audits = $this->auditEloquent->dataAuditReceiptVoluntary($payload->source, $payload->bookyear_id, $payload->department_id, $payload->start_date, $payload->end_date);
                $detail_audits = DB::table('finance.audit_receipt_voluntaries')->get();
                //
                $sheet->getColumnDimension('A')->setWidth(10);
                $sheet->getColumnDimension('B')->setWidth(30);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(50);
                $sheet->getColumnDimension('F')->setWidth(30);
                //
                $sheet->mergeCells('A1:F1');
                $sheet->mergeCells('A2:F2');
                $sheet->mergeCells('A3:F3');
                //
                $sheet->setCellValue('A5', 'NO');
                $sheet->setCellValue('B5', 'STATUS DATA');
                $sheet->setCellValue('C5', 'TANGGAL');
                $sheet->setCellValue('D5', 'JUMLAH');
                $sheet->setCellValue('E5', 'KETERANGAN');
                $sheet->setCellValue('F5', 'PETUGAS');
                //
                $baris = 6;
                $number = 1;
                foreach ($audits as $audit) 
                {
                    $sheet->mergeCells('A'.$baris.':A'.$baris + 4);
                    $sheet->setCellValue('A'.$baris,$number);
                    $sheet->setCellValue('B'.$baris,'Perubahan dilakukan oleh ' . $audit->employee . ' tanggal ' . $this->formatDate($audit->audit_date,'isotime'))->mergeCells('B'.$baris.':F'.$baris);
                    $sheet->setCellValue('B'.$baris = $baris + 1, 'No. Jurnal: '. $audit->cash_no . ' - Alasan: ' . $audit->remark)->mergeCells('B'.$baris.':F'.$baris);
                    $sheet->setCellValue('B'.$baris = $baris + 1, 'Transaksi: '. $audit->transaction)->mergeCells('B'.$baris.':F'.$baris);
                    foreach ($detail_audits as $detail)
                    {
                        if ($audit->id == $detail->audit_id)
                        {
                            $sheet->setCellValue('B'.$baris = $baris + 1, $detail->is_status == 0 ? 'Data Lama' : 'Data Perubahan');
                            $sheet->setCellValue('C'.$baris, $this->formatDate($audit->audit_date,'timeiso'));
                            $sheet->setCellValue('D'.$baris, number_format($detail->total,2));
                            $sheet->setCellValue('E'.$baris, $detail->remark);
                            $sheet->setCellValue('F'.$baris, $detail->employee);
                        }
                    }
                    $baris++;
                    $number++;
                }
                $sheet->getStyle('A6:A'.$baris = $baris - 1)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
                $sheet->getStyle('B6:B'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
                $sheet->getStyle('C6:C'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
                $sheet->getStyle('D6:D'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
                $sheet->getStyle('D6:D'.$baris)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
                $sheet->getStyle('E6:E'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
                $sheet->getStyle('F6:F'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
                $sheet->getStyle('A5:F5')->applyFromArray($this->PHPExcelCommonStyle()['header']);
                break;
            case 'receipt_other':
                $audits = $this->auditEloquent->dataAuditReceiptOther($payload->source, $payload->bookyear_id, $payload->department_id, $payload->start_date, $payload->end_date);
                $detail_audits = DB::table('finance.audit_receipt_others')->get();
                //
                $sheet->getColumnDimension('A')->setWidth(10);
                $sheet->getColumnDimension('B')->setWidth(30);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(50);
                $sheet->getColumnDimension('F')->setWidth(30);
                //
                $sheet->mergeCells('A1:F1');
                $sheet->mergeCells('A2:F2');
                $sheet->mergeCells('A3:F3');
                //
                $sheet->setCellValue('A5', 'NO');
                $sheet->setCellValue('B5', 'STATUS DATA');
                $sheet->setCellValue('C5', 'TANGGAL');
                $sheet->setCellValue('D5', 'JUMLAH');
                $sheet->setCellValue('E5', 'KETERANGAN');
                $sheet->setCellValue('F5', 'PETUGAS');
                //
                $baris = 6;
                $number = 1;
                foreach ($audits as $audit) 
                {
                    $sheet->mergeCells('A'.$baris.':A'.$baris + 4);
                    $sheet->setCellValue('A'.$baris,$number);
                    $sheet->setCellValue('B'.$baris,'Perubahan dilakukan oleh ' . $audit->employee . ' tanggal ' . $this->formatDate($audit->audit_date,'isotime'))->mergeCells('B'.$baris.':F'.$baris);
                    $sheet->setCellValue('B'.$baris = $baris + 1, 'No. Jurnal: '. $audit->cash_no . ' - Alasan: ' . $audit->remark)->mergeCells('B'.$baris.':F'.$baris);
                    $sheet->setCellValue('B'.$baris = $baris + 1, 'Transaksi: '. $audit->transaction)->mergeCells('B'.$baris.':F'.$baris);
                    foreach ($detail_audits as $detail)
                    {
                        if ($audit->id == $detail->audit_id)
                        {
                            $sheet->setCellValue('B'.$baris = $baris + 1, $detail->is_status == 0 ? 'Data Lama' : 'Data Perubahan');
                            $sheet->setCellValue('C'.$baris, $this->formatDate($audit->audit_date,'timeiso'));
                            $sheet->setCellValue('D'.$baris, number_format($detail->total,2));
                            $sheet->setCellValue('E'.$baris, $detail->remark);
                            $sheet->setCellValue('F'.$baris, $detail->employee);
                        }
                    }
                    $baris++;
                    $number++;
                }
                $sheet->getStyle('A6:A'.$baris = $baris - 1)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
                $sheet->getStyle('B6:B'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
                $sheet->getStyle('C6:C'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
                $sheet->getStyle('D6:D'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
                $sheet->getStyle('D6:D'.$baris)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
                $sheet->getStyle('E6:E'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
                $sheet->getStyle('F6:F'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
                $sheet->getStyle('A5:F5')->applyFromArray($this->PHPExcelCommonStyle()['header']);
                break;
            case 'expense':
                $audits = $this->auditEloquent->dataAuditExpense($payload->source, $payload->bookyear_id, $payload->department_id, $payload->start_date, $payload->end_date);
                $detail_audits = DB::table('finance.audit_expenditures')->get();
                //
                $sheet->getColumnDimension('A')->setWidth(10);
                $sheet->getColumnDimension('B')->setWidth(30);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(50);
                $sheet->getColumnDimension('F')->setWidth(30);
                //
                $sheet->mergeCells('A1:F1');
                $sheet->mergeCells('A2:F2');
                $sheet->mergeCells('A3:F3');
                //
                $sheet->setCellValue('A5', 'NO');
                $sheet->setCellValue('B5', 'STATUS DATA');
                $sheet->setCellValue('C5', 'TANGGAL');
                $sheet->setCellValue('D5', 'JUMLAH');
                $sheet->setCellValue('E5', 'KETERANGAN');
                $sheet->setCellValue('F5', 'PETUGAS');
                //
                $baris = 6;
                $number = 1;
                foreach ($audits as $audit) 
                {
                    $sheet->mergeCells('A'.$baris.':A'.$baris + 4);
                    $sheet->setCellValue('A'.$baris,$number);
                    $sheet->setCellValue('B'.$baris,'Perubahan dilakukan oleh ' . $audit->employee . ' tanggal ' . $this->formatDate($audit->audit_date,'isotime'))->mergeCells('B'.$baris.':F'.$baris);
                    $sheet->setCellValue('B'.$baris = $baris + 1, 'No. Jurnal: '. $audit->cash_no . ' - Alasan: ' . $audit->remark)->mergeCells('B'.$baris.':F'.$baris);
                    $sheet->setCellValue('B'.$baris = $baris + 1, 'Transaksi: '. $audit->transaction)->mergeCells('B'.$baris.':F'.$baris);
                    foreach ($detail_audits as $detail)
                    {
                        if ($audit->id == $detail->audit_id)
                        {
                            $sheet->setCellValue('B'.$baris = $baris + 1, $detail->is_status == 0 ? 'Data Lama' : 'Data Perubahan');
                            $sheet->setCellValue('C'.$baris, $this->formatDate($audit->audit_date,'timeiso'));
                            $sheet->setCellValue('D'.$baris, number_format($detail->total,2));
                            $sheet->setCellValue('E'.$baris, $detail->remark);
                            $sheet->setCellValue('F'.$baris, $detail->employee);
                        }
                    }
                    $baris++;
                    $number++;
                }
                $sheet->getStyle('A6:A'.$baris = $baris - 1)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
                $sheet->getStyle('B6:B'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
                $sheet->getStyle('C6:C'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
                $sheet->getStyle('D6:D'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
                $sheet->getStyle('D6:D'.$baris)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
                $sheet->getStyle('E6:E'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
                $sheet->getStyle('F6:F'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
                $sheet->getStyle('A5:F5')->applyFromArray($this->PHPExcelCommonStyle()['header']);
                break;
            case 'savingdeposit':
                $audits = $this->auditEloquent->dataAuditSaving($payload->source, $payload->bookyear_id, $payload->department_id, $payload->start_date, $payload->end_date);
                $detail_audits = DB::table('finance.audit_savings')->get();
                //
                $sheet->getColumnDimension('A')->setWidth(10);
                $sheet->getColumnDimension('B')->setWidth(30);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(20);
                $sheet->getColumnDimension('F')->setWidth(40);
                $sheet->getColumnDimension('G')->setWidth(30);
                //
                $sheet->mergeCells('A1:G1');
                $sheet->mergeCells('A2:G2');
                $sheet->mergeCells('A3:G3');
                //
                $sheet->setCellValue('A5', 'NO');
                $sheet->setCellValue('B5', 'STATUS DATA');
                $sheet->setCellValue('C5', 'TANGGAL');
                $sheet->setCellValue('D5', 'DEBIT');
                $sheet->setCellValue('E5', 'KREDIT');
                $sheet->setCellValue('F5', 'KETERANGAN');
                $sheet->setCellValue('G5', 'PETUGAS');
                //
                $baris = 6;
                $number = 1;
                foreach ($audits as $audit) 
                {
                    $sheet->mergeCells('A'.$baris.':A'.$baris + 4);
                    $sheet->setCellValue('A'.$baris,$number);
                    $sheet->setCellValue('B'.$baris,'Perubahan dilakukan oleh ' . $audit->employee . ' tanggal ' . $this->formatDate($audit->audit_date,'isotime'))->mergeCells('B'.$baris.':F'.$baris);
                    $sheet->setCellValue('B'.$baris = $baris + 1, 'No. Jurnal: '. $audit->cash_no . ' - Alasan: ' . $audit->remark)->mergeCells('B'.$baris.':F'.$baris);
                    $sheet->setCellValue('B'.$baris = $baris + 1, 'Transaksi: '. $audit->transaction)->mergeCells('B'.$baris.':F'.$baris);
                    foreach ($detail_audits as $detail)
                    {
                        if ($audit->id == $detail->audit_id)
                        {
                            $sheet->setCellValue('B'.$baris = $baris + 1, $detail->is_status == 0 ? 'Data Lama' : 'Data Perubahan');
                            $sheet->setCellValue('C'.$baris, $this->formatDate($audit->audit_date,'timeiso'));
                            $sheet->setCellValue('D'.$baris, number_format($detail->debit,2));
                            $sheet->setCellValue('E'.$baris, number_format($detail->credit,2));
                            $sheet->setCellValue('F'.$baris, $detail->remark);
                            $sheet->setCellValue('G'.$baris, $detail->employee);
                        }
                    }
                    $baris++;
                    $number++;
                }
                $sheet->getStyle('A6:A'.$baris = $baris - 1)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
                $sheet->getStyle('B6:B'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
                $sheet->getStyle('C6:C'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
                $sheet->getStyle('D6:D'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
                $sheet->getStyle('D6:D'.$baris)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
                $sheet->getStyle('E6:E'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
                $sheet->getStyle('E6:E'.$baris)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
                $sheet->getStyle('F6:F'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
                $sheet->getStyle('G6:G'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
                $sheet->getStyle('A5:G5')->applyFromArray($this->PHPExcelCommonStyle()['header']);
                break;
            case 'savingwithdrawal':
                $audits = $this->auditEloquent->dataAuditSaving($payload->source, $payload->bookyear_id, $payload->department_id, $payload->start_date, $payload->end_date);
                $detail_audits = DB::table('finance.audit_savings')->get();
                //
                $sheet->getColumnDimension('A')->setWidth(10);
                $sheet->getColumnDimension('B')->setWidth(30);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(20);
                $sheet->getColumnDimension('F')->setWidth(40);
                $sheet->getColumnDimension('G')->setWidth(30);
                //
                $sheet->mergeCells('A1:G1');
                $sheet->mergeCells('A2:G2');
                $sheet->mergeCells('A3:G3');
                //
                $sheet->setCellValue('A5', 'NO');
                $sheet->setCellValue('B5', 'STATUS DATA');
                $sheet->setCellValue('C5', 'TANGGAL');
                $sheet->setCellValue('D5', 'DEBIT');
                $sheet->setCellValue('E5', 'KREDIT');
                $sheet->setCellValue('F5', 'KETERANGAN');
                $sheet->setCellValue('G5', 'PETUGAS');
                //
                $baris = 6;
                $number = 1;
                foreach ($audits as $audit) 
                {
                    $sheet->mergeCells('A'.$baris.':A'.$baris + 4);
                    $sheet->setCellValue('A'.$baris,$number);
                    $sheet->setCellValue('B'.$baris,'Perubahan dilakukan oleh ' . $audit->employee . ' tanggal ' . $this->formatDate($audit->audit_date,'isotime'))->mergeCells('B'.$baris.':F'.$baris);
                    $sheet->setCellValue('B'.$baris = $baris + 1, 'No. Jurnal: '. $audit->cash_no . ' - Alasan: ' . $audit->remark)->mergeCells('B'.$baris.':F'.$baris);
                    $sheet->setCellValue('B'.$baris = $baris + 1, 'Transaksi: '. $audit->transaction)->mergeCells('B'.$baris.':F'.$baris);
                    foreach ($detail_audits as $detail)
                    {
                        if ($audit->id == $detail->audit_id)
                        {
                            $sheet->setCellValue('B'.$baris = $baris + 1, $detail->is_status == 0 ? 'Data Lama' : 'Data Perubahan');
                            $sheet->setCellValue('C'.$baris, $this->formatDate($audit->audit_date,'timeiso'));
                            $sheet->setCellValue('D'.$baris, number_format($detail->debit,2));
                            $sheet->setCellValue('E'.$baris, number_format($detail->credit,2));
                            $sheet->setCellValue('F'.$baris, $detail->remark);
                            $sheet->setCellValue('G'.$baris, $detail->employee);
                        }
                    }
                    $baris++;
                    $number++;
                }
                $sheet->getStyle('A6:A'.$baris = $baris - 1)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
                $sheet->getStyle('B6:B'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
                $sheet->getStyle('C6:C'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
                $sheet->getStyle('D6:D'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
                $sheet->getStyle('D6:D'.$baris)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
                $sheet->getStyle('E6:E'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
                $sheet->getStyle('E6:E'.$baris)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
                $sheet->getStyle('F6:F'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
                $sheet->getStyle('G6:G'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
                $sheet->getStyle('A5:G5')->applyFromArray($this->PHPExcelCommonStyle()['header']);
                break;
            case 'journalvoucher':
                $audits = $this->auditEloquent->dataAuditJournalVoucher($payload->source, $payload->bookyear_id, $payload->department_id, $payload->start_date, $payload->end_date);
                $detail_audits = DB::table('finance.audit_journals')->get();
                $detail_audits_sub = DB::table('finance.audit_journal_details')->select(
                                                    'finance.codes.code',
                                                    'finance.codes.name',
                                                    'finance.audit_journal_details.audit_id',
                                                    'finance.audit_journal_details.is_status',
                                                    'finance.audit_journal_details.debit',
                                                    'finance.audit_journal_details.credit'
                                                )
                                                ->join('finance.codes','finance.codes.id','=','finance.audit_journal_details.account_id')
                                                ->get();
                //
                $sheet->getColumnDimension('A')->setWidth(10);
                $sheet->getColumnDimension('B')->setWidth(30);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(50);
                $sheet->getColumnDimension('F')->setWidth(20);
                $sheet->getColumnDimension('G')->setWidth(20);
                $sheet->getColumnDimension('H')->setWidth(40);
                //
                $sheet->setCellValue('A5', 'NO');
                $sheet->setCellValue('B5', 'STATUS DATA');
                $sheet->setCellValue('C5', 'TANGGAL');
                $sheet->setCellValue('D5', 'KETERANGAN');
                $sheet->setCellValue('E5', 'DETAIL JURNAL');
                $sheet->setCellValue('F5', 'DEBIT');
                $sheet->setCellValue('G5', 'KREDIT');
                $sheet->setCellValue('H5', 'PETUGAS'); 
                //
                $sheet->mergeCells('A1:H1');
                $sheet->mergeCells('A2:H2');
                $sheet->mergeCells('A3:H3');
                //
                $baris = 6;
                $number = 1;
                foreach ($audits as $audit)
                {
                    $sheet->setCellValue('A'.$baris,$number);
                    $sheet->setCellValue('B'.$baris,'Perubahan dilakukan oleh ' . $audit->employee . ' tanggal ' . $this->formatDate($audit->audit_date,'isotime'))->mergeCells('B'.$baris.':F'.$baris);
                    $sheet->setCellValue('B'.$baris = $baris + 1, 'No. Jurnal: '. $audit->cash_no . ' - Alasan: ' . $audit->remark)->mergeCells('B'.$baris.':F'.$baris);
                    $sheet->setCellValue('B'.$baris = $baris + 1, 'Transaksi: '. $audit->transaction)->mergeCells('B'.$baris.':F'.$baris);
                    foreach ($detail_audits as $detail)
                    {
                        if ($audit->id == $detail->audit_id)
                        {
                            foreach ($detail_audits_sub as $sub)
                            {
                                if ($detail->audit_id == $sub->audit_id && $detail->is_status == $sub->is_status)
                                {
                                    $sheet->setCellValue('B'.$baris = $baris + 1, $detail->is_status == 0 ? 'Data Lama' : 'Data Perubahan');
                                    $sheet->setCellValue('C'.$baris, $this->formatDate($audit->audit_date,'timeiso'));
                                    $sheet->setCellValue('D'.$baris, $detail->remark);
                                    $sheet->setCellValue('E'.$baris, $sub->code .' '. $sub->name);
                                    $sheet->setCellValue('F'.$baris, $sub->debit);
                                    $sheet->setCellValue('G'.$baris, $sub->credit);
                                    $sheet->setCellValue('H'.$baris, $detail->employee);
                                }
                            }
                        }
                    }
                    $baris++;
                    $number++;
                }
                $sheet->getStyle('A6:A'.$baris = $baris - 1)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
                $sheet->getStyle('B6:B'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
                $sheet->getStyle('C6:C'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
                $sheet->getStyle('D6:D'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
                $sheet->getStyle('E6:E'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
                $sheet->getStyle('F6:F'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
                $sheet->getStyle('F6:F'.$baris)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
                $sheet->getStyle('G6:G'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
                $sheet->getStyle('G6:G'.$baris)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
                $sheet->getStyle('H6:H'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
                $sheet->getStyle('A5:h5')->applyFromArray($this->PHPExcelCommonStyle()['header']);
                break;
            case 'begin_balance':
                //
                break;
            default:
                $audits = $this->auditEloquent->dataAuditPaymentMajor($payload->source, $payload->bookyear_id, $payload->department_id, $payload->start_date, $payload->end_date);
                $detail_audits = DB::table('finance.audit_payment_majors')->get();
                //
                $sheet->getColumnDimension('A')->setWidth(10);
                $sheet->getColumnDimension('B')->setWidth(30);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(50);
                $sheet->getColumnDimension('F')->setWidth(30);
                //
                $sheet->mergeCells('A1:F1');
                $sheet->mergeCells('A2:F2');
                $sheet->mergeCells('A3:F3');
                //
                $sheet->setCellValue('A5', 'NO');
                $sheet->setCellValue('B5', 'STATUS DATA');
                $sheet->setCellValue('C5', 'TANGGAL');
                $sheet->setCellValue('D5', 'JUMLAH');
                $sheet->setCellValue('E5', 'KETERANGAN');
                $sheet->setCellValue('F5', 'PETUGAS');
                //
                $baris = 6;
                $number = 1;
                foreach ($audits as $audit) 
                {
                    $sheet->mergeCells('A'.$baris.':A'.$baris + 4);
                    $sheet->setCellValue('A'.$baris,$number);
                    $sheet->setCellValue('B'.$baris,'Perubahan dilakukan oleh ' . $audit->employee . ' tanggal ' . $this->formatDate($audit->audit_date,'isotime'))->mergeCells('B'.$baris.':F'.$baris);
                    $sheet->setCellValue('B'.$baris = $baris + 1, 'No. Jurnal: '. $audit->cash_no . ' - Alasan: ' . $audit->remark)->mergeCells('B'.$baris.':F'.$baris);
                    $sheet->setCellValue('B'.$baris = $baris + 1, 'Transaksi: '. $audit->transaction)->mergeCells('B'.$baris.':F'.$baris);
                    foreach ($detail_audits as $detail)
                    {
                        if ($audit->id == $detail->audit_id)
                        {
                            $sheet->setCellValue('B'.$baris = $baris + 1, $detail->is_status == 0 ? 'Data Lama' : 'Data Perubahan');
                            $sheet->setCellValue('C'.$baris, $this->formatDate($audit->audit_date,'timeiso'));
                            $sheet->setCellValue('D'.$baris, number_format($detail->total,2));
                            $sheet->setCellValue('E'.$baris, $detail->remark);
                            $sheet->setCellValue('F'.$baris, $detail->employee);
                        }
                    }
                    $baris++;
                    $number++;
                }
                $sheet->getStyle('A6:A'.$baris = $baris - 1)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
                $sheet->getStyle('B6:B'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
                $sheet->getStyle('C6:C'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
                $sheet->getStyle('D6:D'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
                $sheet->getStyle('D6:D'.$baris)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
                $sheet->getStyle('E6:E'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
                $sheet->getStyle('F6:F'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
                $sheet->getStyle('A5:F5')->applyFromArray($this->PHPExcelCommonStyle()['header']);
                break;
        }
        //
        $sheet->getStyle('A1:A2')->applyFromArray($this->PHPExcelCommonStyle()['title']);
        $sheet->getStyle('A3:A3')->applyFromArray($this->PHPExcelCommonStyle()['subTitle']);
        //
        $filename = date('Ymdhis') . '_' . Str::lower(config('app.name')) . '_laporan_data_audit_perubahan.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save('storage/downloads/'.$filename);
        echo $filename;
    }

    // helpers
    function subExist($key, array $array): bool
    {
        $isValid = false;
        for ($i=0; $i < count($array); $i++) 
        { 
            if ($array[$i]->name == $key)
            {
                $isValid = true;
            }
        }
        return $isValid;
    }
}
