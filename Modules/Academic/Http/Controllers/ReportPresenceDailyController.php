<?php

namespace Modules\Academic\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Reference;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\PdfTrait;
use App\Http\Traits\DepartmentTrait;
use App\Http\Traits\ReferenceTrait;
use Modules\Academic\Entities\SchoolYear;
use Modules\Academic\Entities\Semester;
use Modules\Academic\Repositories\Presence\PresenceDailyEloquent;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use Carbon\Carbon;
use View;
use Exception;

class ReportPresenceDailyController extends Controller
{
    use HelperTrait;
    use PdfTrait;
    use ReferenceTrait;
    use DepartmentTrait;

    function __construct(PresenceDailyEloquent $presenceDailyEloquent)
    {
        $this->presenceDailyEloquent = $presenceDailyEloquent;
    }

    /* Daily Presence Student */

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function presenceDaily(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $window = explode(".", $request->w);
        $data['InnerHeight'] = $window[0];
        $data['InnerWidth'] = $window[1];
        //
        return view('academic::reports.presences.presence_daily', $data);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function presenceDailyData(Request $request)
    {
        return response()->json($this->presenceDailyEloquent->reportData($request->start_date, $request->end_date, $request->student_id));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function presenceDailyToPdf(Request $request)
    {
        $payload = json_decode($request->data);
        $vals['payload'] = $payload;
        $vals['presences'] = $this->presenceDailyEloquent->reportData($payload->start_date, $payload->end_date, $payload->student_id);
        $vals['profile'] = $this->getInstituteProfile();
        $view = View::make('academic::reports.presences.presence_daily_pdf', $vals);
        $name = Str::lower(config('app.name')) .'_laporan_presensi_harian';
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortrait($hashfile, $filename);
        echo $filename;
    }

    /**
     * Export resource to Excel Document.
     * @return Excel
     */
    public function presenceDailyToExcel(Request $request)
    {
        $payload = json_decode($request->data);
        //
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('laporan_presensi_harian');
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        //
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(10);
        $sheet->getColumnDimension('H')->setWidth(10);
        $sheet->getColumnDimension('I')->setWidth(10);
        //
        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');
        $sheet->mergeCells('A3:I3');
        //
        $sheet->setCellValue('A1', Str::upper($request->session()->get('institute')));
        $sheet->setCellValue('A2', 'LAPORAN PRESENSI HARIAN SANTRI - ' . $payload->start_date .' s.d '. $payload->end_date);
        $sheet->setCellValue('A3', 'DEPARTEMEN ' . $payload->department . ' - TAHUN AJARAN ' . $payload->schoolyear . ' - TINGKAT '. $payload->grade);
        $sheet->setCellValue('A5', 'NIS: ' . $payload->studentno);
        $sheet->setCellValue('A6', 'Nama Santri: '. $payload->student);
        $sheet->setCellValue('A8', 'NO.');
        $sheet->setCellValue('B8', 'TANGGAL');
        $sheet->setCellValue('C8', 'SEMESTER');
        $sheet->setCellValue('D8', 'KELAS');
        $sheet->setCellValue('E8', 'HADIR');
        $sheet->setCellValue('F8', 'IJIN');
        $sheet->setCellValue('G8', 'SAKIT');
        $sheet->setCellValue('H8', 'ALPA');
        $sheet->setCellValue('I8', 'CUTI');
        //
        $baris = 9;
        $number = 1;
        foreach ($payload->rows as $row) 
        {
            $sheet->setCellValue('A'.$baris,$number);
            $sheet->setCellValue('B'.$baris,$row->date);
            $sheet->setCellValue('C'.$baris,$row->semester);
            $sheet->setCellValue('D'.$baris,$row->class);
            $sheet->setCellValue('E'.$baris,$row->present);
            $sheet->setCellValue('F'.$baris,$row->permit);
            $sheet->setCellValue('G'.$baris,$row->sick);
            $sheet->setCellValue('H'.$baris,$row->absent);
            $sheet->setCellValue('I'.$baris,$row->leave);
            $baris++;
            $number++;
        }
        $sheet->setCellValue('D'.$baris,'Jumlah');
        $lastrow = $baris - 1;
        $sheet->setCellValue('E'.$baris,'=SUM(E7:E'.$lastrow.')');
        $sheet->setCellValue('F'.$baris,'=SUM(F7:F'.$lastrow.')');
        $sheet->setCellValue('G'.$baris,'=SUM(G7:G'.$lastrow.')');
        $sheet->setCellValue('H'.$baris,'=SUM(H7:H'.$lastrow.')');
        $sheet->setCellValue('I'.$baris,'=SUM(I7:I'.$lastrow.')');
        //
        $sheet->getStyle('A1:A2')->applyFromArray($this->PHPExcelCommonStyle()['title']);
        $sheet->getStyle('A3:A3')->applyFromArray($this->PHPExcelCommonStyle()['subTitle']);
        $sheet->getStyle('A5:A6')->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        $sheet->getStyle('A8:I8')->applyFromArray($this->PHPExcelCommonStyle()['header']);
        $sheet->getStyle('A9:A'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('B9:B'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('C9:C'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('D9:D'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('E9:E'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('F9:F'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('F9:F'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('G9:G'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('H9:H'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('I9:I'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        //
        $filename = date('Ymdhis') . '_' . Str::lower(config('app.name')) . '_laporan_presensi_harian.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save('storage/downloads/'.$filename);
        echo $filename;
    }

    /* Daily Presence Student per Class */

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function presenceDailyClass(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $window = explode(".", $request->w);
        $data['InnerHeight'] = $window[0];
        $data['InnerWidth'] = $window[1];
        //
        return view('academic::reports.presences.presence_daily_class', $data);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function presenceDailyClassData(Request $request)
    {
        return response()->json($this->presenceDailyEloquent->reportPresenceDailyClass($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function presenceDailyClassToPdf(Request $request)
    {
        $payload = json_decode($request->data);
        $request = new Request();
        $request->start_date = $payload->start_date;
        $request->end_date = $payload->end_date;
        $request->class_id = $payload->class_id;
        // 
        $vals['payload'] = $payload;
        $vals['presences'] = $this->presenceDailyEloquent->reportPresenceDailyClass($request);
        $vals['profile'] = $this->getInstituteProfile();
        $view = View::make('academic::reports.presences.presence_daily_class_pdf', $vals);
        $name = Str::lower(config('app.name')) .'_laporan_presensi_harian_kelas';
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
    public function presenceDailyClassToExcel(Request $request)
    {
        $payload = json_decode($request->data);
        //
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('laporan_presensi_harian_kelas');
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        //
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(10);
        $sheet->getColumnDimension('H')->setWidth(10);
        //
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        $sheet->mergeCells('A3:H3');
        //
        $sheet->setCellValue('A1', Str::upper($request->session()->get('institute')));
        $sheet->setCellValue('A2', 'LAPORAN PRESENSI HARIAN PER KELAS - ' . $payload->start_date .' s.d '. $payload->end_date);
        $sheet->setCellValue('A3', 'DEPARTEMEN ' . $payload->department . ' - TAHUN AJARAN ' . $payload->schoolyear . ' - TINGKAT '. $payload->grade);
        $sheet->setCellValue('A5', 'NO.');
        $sheet->setCellValue('B5', 'NIS');
        $sheet->setCellValue('C5', 'NAMA');
        $sheet->setCellValue('D5', 'HADIR');
        $sheet->setCellValue('E5', 'IJIN');
        $sheet->setCellValue('F5', 'SAKIT');
        $sheet->setCellValue('G5', 'ALPA');
        $sheet->setCellValue('H5', 'CUTI');
        //
        $baris = 6;
        $number = 1;
        foreach ($payload->rows as $row) 
        {
            $sheet->setCellValue('A'.$baris,$number);
            $sheet->setCellValue('B'.$baris,$row->student_no);
            $sheet->setCellValue('C'.$baris,$row->student);
            $sheet->setCellValue('D'.$baris,$row->present);
            $sheet->setCellValue('E'.$baris,$row->permit);
            $sheet->setCellValue('F'.$baris,$row->sick);
            $sheet->setCellValue('G'.$baris,$row->absent);
            $sheet->setCellValue('H'.$baris,$row->leave);
            $baris++;
            $number++;
        }
        //
        $sheet->getStyle('A1:A2')->applyFromArray($this->PHPExcelCommonStyle()['title']);
        $sheet->getStyle('A3:A3')->applyFromArray($this->PHPExcelCommonStyle()['subTitle']);
        $sheet->getStyle('A5:H5')->applyFromArray($this->PHPExcelCommonStyle()['header']);
        $sheet->getStyle('A6:A'.$baris = $baris - 1)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('B6:B'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('C6:C'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('D6:D'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('E6:E'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('F6:F'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('F6:F'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('G6:G'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('H6:H'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        //
        $filename = date('Ymdhis') . '_' . Str::lower(config('app.name')) . '_laporan_presensi_harian_kelas.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save('storage/downloads/'.$filename);
        echo $filename;
    }

    /* Daily Presence Student Absent */

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function presenceDailyAbsent(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $window = explode(".", $request->w);
        $data['InnerHeight'] = $window[0];
        $data['InnerWidth'] = $window[1];
        //
        $data['schoolyears'] = SchoolYear::orderByDesc('id')->get();
        return view('academic::reports.presences.presence_daily_absent', $data);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function presenceDailyAbsentData(Request $request)
    {
        return response()->json($this->presenceDailyEloquent->reportPresenceDailyAbsent($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function presenceDailyAbsentToPdf(Request $request)
    {
        $payload = json_decode($request->data);
        $vals['payload'] = $payload;
        $vals['profile'] = $this->getInstituteProfile();
        $view = View::make('academic::reports.presences.presence_daily_absent_pdf', $vals);
        $name = Str::lower(config('app.name')) .'_laporan_presensi_harian_absen';
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
    public function presenceDailyAbsentToExcel(Request $request)
    {
        $payload = json_decode($request->data);
        //
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('laporan_presensi_santri_absen');
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        //
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(50);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(10);
        $sheet->getColumnDimension('H')->setWidth(10);
        $sheet->getColumnDimension('I')->setWidth(10);
        $sheet->getColumnDimension('J')->setWidth(10);
        //
        $sheet->mergeCells('A1:J1');
        $sheet->mergeCells('A2:J2');
        $sheet->mergeCells('A3:J3');
        //
        $sheet->setCellValue('A1', Str::upper($request->session()->get('institute')));
        $sheet->setCellValue('A2', 'LAPORAN PRESENSI HARIAN SANTRI TIDAK HADIR - ' . $payload->start_date .' s.d '. $payload->end_date);
        $sheet->setCellValue('A3', 'DEPARTEMEN ' . $payload->department . ' - TAHUN AJARAN ' . $payload->schoolyear . ' - TINGKAT '. $payload->grade);
        $sheet->setCellValue('A5', 'NO.');
        $sheet->setCellValue('B5', 'NIS');
        $sheet->setCellValue('C5', 'NAMA');
        $sheet->setCellValue('D5', 'KELAS');
        $sheet->setCellValue('E5', 'ORANG TUA WALI');
        $sheet->setCellValue('F5', 'HADIR');
        $sheet->setCellValue('G5', 'IJIN');
        $sheet->setCellValue('H5', 'SAKIT');
        $sheet->setCellValue('I5', 'ALPA');
        $sheet->setCellValue('J5', 'CUTI');
        //
        $baris = 6;
        $number = 1;
        foreach ($payload->rows as $row) 
        {
            $sheet->setCellValue('A'.$baris,$number);
            $sheet->setCellValue('B'.$baris,$row->student_no);
            $sheet->setCellValue('C'.$baris,$row->student);
            $sheet->setCellValue('D'.$baris,$row->class);
            $sheet->setCellValue('E'.$baris,Str::replace('<br/>', "\n", $row->parent))->getStyle('E'.$baris)->getAlignment()->setWrapText(true);
            $sheet->setCellValue('F'.$baris,Str::replace('</b>','',Str::replace('<b>','',$row->present)));
            $sheet->setCellValue('G'.$baris,Str::replace('</b>','',Str::replace('<b>','',$row->permit)));
            $sheet->setCellValue('H'.$baris,Str::replace('</b>','',Str::replace('<b>','',$row->sick)));
            $sheet->setCellValue('I'.$baris,Str::replace('</b>','',Str::replace('<b>','',$row->absent)));
            $sheet->setCellValue('J'.$baris,Str::replace('</b>','',Str::replace('<b>','',$row->leave)));
            $baris++;
            $number++;
        }
        //
        $sheet->getStyle('A1:A2')->applyFromArray($this->PHPExcelCommonStyle()['title']);
        $sheet->getStyle('A3:A3')->applyFromArray($this->PHPExcelCommonStyle()['subTitle']);
        $sheet->getStyle('A5:J5')->applyFromArray($this->PHPExcelCommonStyle()['header']);
        $sheet->getStyle('A6:A'.$baris = $baris - 1)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('B6:B'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('C6:C'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('D6:D'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('E6:E'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('F6:F'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenterBig']);
        $sheet->getStyle('F6:F'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenterBig']);
        $sheet->getStyle('G6:G'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenterBig']);
        $sheet->getStyle('H6:H'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenterBig']);
        $sheet->getStyle('I6:I'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenterBig']);
        $sheet->getStyle('J6:J'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenterBig']);
        //
        $filename = date('Ymdhis') . '_' . Str::lower(config('app.name')) . '_laporan_presensi_harian_tidak_hadir.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save('storage/downloads/'.$filename);
        echo $filename;
    }

    /* Daily Presence Stat */
    
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function presenceStat(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $window = explode(".", $request->w);
        $data['InnerHeight'] = $window[0];
        $data['InnerWidth'] = $window[1];
        //
        $data['schoolyears'] = SchoolYear::orderByDesc('id')->get();
        return view('academic::reports.presences.presence_stat', $data);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function presenceStatData(Request $request)
    {
        return response()->json($this->presenceDailyEloquent->reportPresenceStat($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function presenceStatToPdf(Request $request)
    {
        $payload = json_decode($request->data);
        $vals['payload'] = $payload;
        $vals['profile'] = $this->getInstituteProfile();
        $view = View::make('academic::reports.presences.presence_stat_pdf', $vals);
        $name = Str::lower(config('app.name')) .'_statistik_presensi_harian';
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortraits($hashfile, $filename);
        echo $filename;
    }

    /* Daily Presence Stat Class */
    
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function presenceStatClass(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $window = explode(".", $request->w);
        $data['InnerHeight'] = $window[0];
        $data['InnerWidth'] = $window[1];
        //
        $data['schoolyears'] = SchoolYear::orderByDesc('id')->get();
        return view('academic::reports.presences.presence_stat_class', $data);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function presenceStatClassData(Request $request)
    {
        return response()->json($this->presenceDailyEloquent->reportPresenceStatClass($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function presenceStatClassToPdf(Request $request)
    {
        $payload = json_decode($request->data);
        $vals['payload'] = $payload;
        $vals['profile'] = $this->getInstituteProfile();
        $view = View::make('academic::reports.presences.presence_stat_class_pdf', $vals);
        $name = Str::lower(config('app.name')) .'_statistik_presensi_harian_kelas';
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortraits($hashfile, $filename);
        echo $filename;
    }
}
