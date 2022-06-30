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
use App\Http\Traits\ReferenceTrait;
use Modules\Academic\Entities\SchoolYear;
use Modules\Academic\Repositories\Presence\PresenceLessonEloquent;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use Carbon\Carbon;
use View;
use Exception;

class ReportPresenceLessonController extends Controller
{
    use HelperTrait;
    use PdfTrait;
    use ReferenceTrait;

    function __construct(PresenceLessonEloquent $presenceLessonEloquent)
    {
        $this->presenceLessonEloquent = $presenceLessonEloquent;
    }

    /* Lesson Presence Student */

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function presenceLesson(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $window = explode(".", $request->w);
        $data['InnerHeight'] = $window[0];
        $data['InnerWidth'] = $window[1];
        //
        return view('academic::reports.presences.presence_lesson', $data);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function presenceLessonData(Request $request)
    {
        return response()->json($this->presenceLessonEloquent->reportData($request));
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function presenceLessonDataInfo(Request $request)
    {
        return response()->json($this->presenceLessonEloquent->reportDataInfo($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function presenceLessonToPdf(Request $request)
    {
        $payload = json_decode($request->data);
        $vals['payload'] = $payload;
        $vals['presences'] = $this->presenceLessonEloquent->queryReportData($payload->start_date, $payload->end_date, $payload->student_id, 0)->get();
        $vals['profile'] = $this->getInstituteProfile();
        $view = View::make('academic::reports.presences.presence_lesson_pdf', $vals);
        $name = Str::lower(config('app.name')) .'_laporan_presensi_pelajaran';
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
    public function presenceLessonToExcel(Request $request)
    {
        $payload = json_decode($request->data);
        //
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('laporan_presensi_pelajaran');
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        //
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(50);
        $sheet->getColumnDimension('F')->setWidth(30);
        $sheet->getColumnDimension('G')->setWidth(30);
        $sheet->getColumnDimension('H')->setWidth(50);
        //
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        $sheet->mergeCells('A3:H3');
        //
        $sheet->setCellValue('A1', Str::upper($request->session()->get('institute')));
        $sheet->setCellValue('A2', 'LAPORAN PRESENSI PELAJARAN SANTRI - ' . $payload->start_date .' s.d '. $payload->end_date);
        $sheet->setCellValue('A3', 'DEPARTEMEN ' . $payload->department . ' - TAHUN AJARAN ' . $payload->schoolyear . ' - TINGKAT '. $payload->grade);
        $sheet->setCellValue('A5', 'NIS: ' . $payload->studentno);
        $sheet->setCellValue('A6', 'Nama Santri: '. $payload->student);
        $sheet->setCellValue('A8', 'NO.');
        $sheet->setCellValue('B8', 'TANGGAL');
        $sheet->setCellValue('C8', 'JAM');
        $sheet->setCellValue('D8', 'KELAS');
        $sheet->setCellValue('E8', 'CATATAN');
        $sheet->setCellValue('F8', 'PELAJARAN');
        $sheet->setCellValue('G8', 'GURU');
        $sheet->setCellValue('H8', 'MATERI');
        //
        $baris = 9;
        $number = 1;
        foreach ($payload->rows as $row) 
        {
            $sheet->setCellValue('A'.$baris,$number);
            $sheet->setCellValue('B'.$baris,$row->date);
            $sheet->setCellValue('C'.$baris,$row->time);
            $sheet->setCellValue('D'.$baris,$row->class);
            $sheet->setCellValue('E'.$baris,$row->remark);
            $sheet->setCellValue('F'.$baris,$row->lesson);
            $sheet->setCellValue('G'.$baris,$row->employee);
            $sheet->setCellValue('H'.$baris,$row->subject);
            $baris++;
            $number++;
        }
        //
        $sheet->getStyle('A1:A2')->applyFromArray($this->PHPExcelCommonStyle()['title']);
        $sheet->getStyle('A3:A3')->applyFromArray($this->PHPExcelCommonStyle()['subTitle']);
        $sheet->getStyle('A5:A6')->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        $sheet->getStyle('A8:H8')->applyFromArray($this->PHPExcelCommonStyle()['header']);
        $sheet->getStyle('A9:A'.$baris = $baris - 1)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('B9:B'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('C9:C'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('D9:D'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('E9:E'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('F9:F'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('G9:G'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('H9:H'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft'])->getAlignment()->setWrapText(true);

        $baris_info = $baris + 2;
        $range = 'A'.$baris_info.':C'.$baris + 5;
        $range_info = 'C'.$baris_info.':C'.$baris_info + 4;
        $sheet->setCellValue('A'.$baris_info, 'Jumlah Kehadiran');
        $sheet->setCellValue('C'.$baris_info, $payload->sum_present);
        $sheet->setCellValue('A'.$baris_info = $baris_info + 1, 'Jumlah Ketidakhadiran');
        $sheet->setCellValue('C'.$baris_info, $payload->sum_absent);
        $sheet->setCellValue('A'.$baris_info = $baris_info + 1, 'Jumlah Seharusnya');
        $sheet->setCellValue('C'.$baris_info, $payload->sum_required);
        $sheet->setCellValue('A'.$baris_info = $baris_info + 1, 'Persentase Kehadiran');
        $sheet->setCellValue('C'.$baris_info, $payload->sum_percent);
        
        $sheet->getStyle($range)->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        $sheet->getStyle($range_info)->applyFromArray(['alignment' => [ 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, ]]);

        //
        $filename = date('Ymdhis') . '_' . Str::lower(config('app.name')) . '_laporan_presensi_pelajaran.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save('storage/downloads/'.$filename);
        echo $filename;
    }
    
    /* Lesson Presence Student Class */
    
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function presenceLessonClass(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $window = explode(".", $request->w);
        $data['InnerHeight'] = $window[0];
        $data['InnerWidth'] = $window[1];
        //
        return view('academic::reports.presences.presence_lesson_class', $data);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function presenceLessonClassData(Request $request)
    {
        return response()->json($this->presenceLessonEloquent->reportPresenceLessonClass($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function presenceLessonClassToPdf(Request $request)
    {
        $payload = json_decode($request->data);
        $vals['payload'] = $payload;
        $vals['profile'] = $this->getInstituteProfile();
        $view = View::make('academic::reports.presences.presence_lesson_class_pdf', $vals);
        $name = Str::lower(config('app.name')) .'_laporan_presensi_pelajaran_kelas';
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
    public function presenceLessonClassToExcel(Request $request)
    {
        $payload = json_decode($request->data);
        //
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('lap_presensi_pelajaran_kelas');
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        //
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(8);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(50);
        $sheet->getColumnDimension('J')->setWidth(40);
        //
        $sheet->mergeCells('A1:J1');
        $sheet->mergeCells('A2:J2');
        $sheet->mergeCells('A3:J3');
        //
        $sheet->setCellValue('A1', Str::upper($request->session()->get('institute')));
        $sheet->setCellValue('A2', 'LAPORAN PRESENSI PELAJARAN SANTRI PER KELAS - ' . $payload->start_date .' s.d '. $payload->end_date);
        $sheet->setCellValue('A3', 'DEPARTEMEN ' . $payload->department . ' - TAHUN AJARAN ' . $payload->schoolyear . ' - TINGKAT '. $payload->grade . ' - KELAS '. $payload->class . ' - PELAJARAN '. Str::upper($payload->lesson));
        $sheet->setCellValue('A5', 'NO.');
        $sheet->setCellValue('B5', 'NIS');
        $sheet->setCellValue('C5', 'NAMA');
        $sheet->setCellValue('D5', 'JML.HADIR');
        $sheet->setCellValue('E5', 'JML.ABSEN');
        $sheet->setCellValue('F5', 'JML.TOTAL');
        $sheet->setCellValue('G5', '%');
        $sheet->setCellValue('H5', 'NO.HP');
        $sheet->setCellValue('I5', 'ORANG TUA/WALI');
        $sheet->setCellValue('J5', 'NO. HP ORANG TUA');
        //
        $baris = 6;
        $number = 1;
        foreach ($payload->rows as $row) 
        {
            $sheet->setCellValue('A'.$baris,$number);
            $sheet->setCellValue('B'.$baris,$row->student_no);
            $sheet->setCellValue('C'.$baris,$row->is_active == 1 ? $row->student : '*'.$row->student);
            $sheet->setCellValue('D'.$baris,$row->sum_present);
            $sheet->setCellValue('E'.$baris,$row->sum_absent);
            $sheet->setCellValue('F'.$baris,$row->sum_total);
            $sheet->setCellValue('G'.$baris,$row->sum_percent);
            $sheet->setCellValue('H'.$baris,$row->mobile);
            $sheet->setCellValue('I'.$baris,$row->parent);
            $sheet->setCellValue('J'.$baris,$row->parent_mobile);
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
        $sheet->getStyle('D6:D'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('E6:E'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('F6:F'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('G6:G'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('H6:H'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('I6:I'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('J6:J'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);

        $baris_info = $baris + 2;
        $sheet->setCellValue('A'.$baris_info, 'Keterangan: *Status Santri tidak aktif lagi');
        //
        $filename = date('Ymdhis') . '_' . Str::lower(config('app.name')) . '_laporan_presensi_pelajaran_kelas.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save('storage/downloads/'.$filename);
        echo $filename;
    }
    
    /* Lesson Presence Teacher */

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function presenceLessonTeacher(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $window = explode(".", $request->w);
        $data['InnerHeight'] = $window[0];
        $data['InnerWidth'] = $window[1];
        //
        return view('academic::reports.presences.presence_lesson_teacher', $data);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function presenceLessonTeacherData(Request $request)
    {
        return response()->json($this->presenceLessonEloquent->reportPresenceLessonTeacher($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function presenceLessonTeacherToPdf(Request $request)
    {
        $payload = json_decode($request->data);
        $vals['payload'] = $payload;
        $vals['profile'] = $this->getInstituteProfile();
        $vals['status'] = $this->getReferences('hr_teacher_status');
        $view = View::make('academic::reports.presences.presence_lesson_teacher_pdf', $vals);
        $name = Str::lower(config('app.name')) .'_laporan_presensi_pelajaran_pengajar';
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
    public function presenceLessonTeacherToExcel(Request $request)
    {
        $payload = json_decode($request->data);
        $employees = explode(' - ', $payload->employee);
        $t_status = $this->getReferences('hr_teacher_status');
        //
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('lap_presensi_pelajaran_guru');
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        //
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(8);
        $sheet->getColumnDimension('H')->setWidth(12);
        $sheet->getColumnDimension('I')->setWidth(30);
        $sheet->getColumnDimension('J')->setWidth(30);
        //
        $sheet->mergeCells('A1:J1');
        $sheet->mergeCells('A2:J2');
        $sheet->mergeCells('A3:J3');
        //
        $sheet->setCellValue('A1', Str::upper($request->session()->get('institute')));
        $sheet->setCellValue('A2', 'LAPORAN PRESENSI PELAJARAN GURU - ' . $payload->start_date .' s.d '. $payload->end_date);
        $sheet->setCellValue('A3', 'DEPARTEMEN ' . $payload->department . ' - TAHUN AJARAN ' . $payload->schoolyear);
        $sheet->setCellValue('A5', 'NIP');
        $sheet->setCellValue('A6', 'NAMA');
        $sheet->setCellValue('C5', ': '.$employees[0]);
        $sheet->setCellValue('C6', ': '.$employees[1]);

        $sheet->setCellValue('A8', 'NO.');
        $sheet->setCellValue('B8', 'TANGGAL');
        $sheet->setCellValue('C8', 'JAM');
        $sheet->setCellValue('D8', 'KELAS');
        $sheet->setCellValue('E8', 'PELAJARAN');
        $sheet->setCellValue('F8', 'STATUS');
        $sheet->setCellValue('G8', 'TELAT');
        $sheet->setCellValue('H8', 'JAM');
        $sheet->setCellValue('I8', 'MATERI');
        $sheet->setCellValue('J8', 'KETERANGAN');
        //
        $baris = 9;
        $number = 1;

        $tm_status = array();
        $t_times = array();
        $sub_total = array();
        $sub_times = array();
        $total = 0;

        foreach ($payload->rows as $row) 
        {
            $sheet->setCellValue('A'.$baris,$number);
            $sheet->setCellValue('B'.$baris,$row->date);
            $sheet->setCellValue('C'.$baris,$row->time);
            $sheet->setCellValue('D'.$baris,$row->class);
            $sheet->setCellValue('E'.$baris,$row->lesson);
            $sheet->setCellValue('F'.$baris,$row->status);
            $sheet->setCellValue('G'.$baris,$row->late);
            $sheet->setCellValue('H'.$baris,$row->times);
            $sheet->setCellValue('I'.$baris,$row->subject);
            $sheet->setCellValue('J'.$baris,$row->remark);
            $baris++;
            $number++;
            $tm_status[] = strtoupper($row->status); 
            $t_times[] = array('status' => strtoupper($row->status), 'times' => $row->minutes);
        }
        //
        $baris_info = $baris + 1;
        $baris_next = $baris_info + 1;
        $sheet->setCellValue('A'.$baris_info, 'Status')->mergeCells('A'.$baris_info.':B'.$baris_info);
        $sheet->setCellValue('C'.$baris_info, 'Pertemuan');
        $sheet->setCellValue('D'.$baris_info, 'Jumlah Jam');
        $sub_total = array_count_values($tm_status);
        foreach ($t_status as $key => $value) 
        {
            $sum = isset($sub_total[strtoupper($value)]) ? $sub_total[strtoupper($value)] : 0;
            foreach ($t_times as $time)
            {
                if (strtoupper($value) == $time['status'])
                {
                    $total += $time['times'];
                }
            }
            $sheet->setCellValue('A'.$baris_next, $value);
            $sheet->setCellValue('C'.$baris_next, $sum);
            $sheet->setCellValue('D'.$baris_next, $total / 60);

            $sheet->getStyle('A'.$baris_next.':B'.$baris_next)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
            $sheet->getStyle('C'.$baris_next.':C'.$baris_next)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
            $sheet->getStyle('D'.$baris_next.':D'.$baris_next)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
            $baris_next++;
        }
        //
        $sheet->getStyle('A1:A2')->applyFromArray($this->PHPExcelCommonStyle()['title']);
        $sheet->getStyle('A3:A3')->applyFromArray($this->PHPExcelCommonStyle()['subTitle']);
        $sheet->getStyle('A5:C6')->applyFromArray($this->PHPExcelCommonStyle()['bold']);
        $sheet->getStyle('A8:J8')->applyFromArray($this->PHPExcelCommonStyle()['header']);
        $sheet->getStyle('A9:A'.$baris = $baris - 1)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('B9:B'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('C9:C'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('D9:D'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('E9:E'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('F9:F'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('G9:G'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('H9:H'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('I9:I'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft'])->getAlignment()->setWrapText(true);
        $sheet->getStyle('J9:J'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('A'.$baris_info.':D'.$baris_info)->applyFromArray($this->PHPExcelCommonStyle()['header']);
        //
        $filename = date('Ymdhis') . '_' . Str::lower(config('app.name')) . '_laporan_presensi_pelajaran_guru.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save('storage/downloads/'.$filename);
        echo $filename;
    }
    
    /* Lesson Presence Student Absent */
    
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function presenceLessonAbsent(Request $request)
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
        return view('academic::reports.presences.presence_lesson_absent', $data);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function presenceLessonAbsentData(Request $request)
    {
        return response()->json($this->presenceLessonEloquent->reportPresenceLessonAbsent($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function presenceLessonAbsentToPdf(Request $request)
    {
        $payload = json_decode($request->data);
        $vals['payload'] = $payload;
        $vals['profile'] = $this->getInstituteProfile();
        $view = View::make('academic::reports.presences.presence_lesson_absent_pdf', $vals);
        $name = Str::lower(config('app.name')) .'_laporan_presensi_pelajaran_santri_absen';
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
    public function presenceLessonAbsentToExcel(Request $request)
    {
        $payload = json_decode($request->data);
        //
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('lap_presensi_pelajaran_absen');
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        //
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(30);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(50);
        $sheet->getColumnDimension('K')->setWidth(30);
        //
        $sheet->mergeCells('A1:K1');
        $sheet->mergeCells('A2:K2');
        $sheet->mergeCells('A3:K3');
        //
        $sheet->setCellValue('A1', Str::upper($request->session()->get('institute')));
        $sheet->setCellValue('A2', 'LAPORAN PRESENSI PELAJARAN SANTRI TIDAK HADIR - ' . $payload->start_date .' s.d '. $payload->end_date);
        $sheet->setCellValue('A3', 'DEPARTEMEN ' . $payload->department . ' - TAHUN AJARAN ' . $payload->schoolyear . ' - TINGKAT '. $payload->grade . ' - KELAS '. $payload->class);
        $sheet->setCellValue('A5', 'NO.');
        $sheet->setCellValue('B5', 'NIS');
        $sheet->setCellValue('C5', 'NAMA');
        $sheet->setCellValue('D5', 'KELAS');
        $sheet->setCellValue('E5', 'PELAJARAN');
        $sheet->setCellValue('F5', 'PRESENSI');
        $sheet->setCellValue('G5', 'TANGGAL');
        $sheet->setCellValue('H5', 'KETERANGAN');
        $sheet->setCellValue('I5', 'NO. HP');
        $sheet->setCellValue('J5', 'ORANG TUA/WALI');
        $sheet->setCellValue('K5', 'HP ORANG TUA');
        //
        $baris = 6;
        $number = 1;

        foreach ($payload->rows as $row) 
        {
            $sheet->setCellValue('A'.$baris,$number);
            $sheet->setCellValue('B'.$baris,$row->student_no);
            $sheet->setCellValue('C'.$baris,$row->student);
            $sheet->setCellValue('D'.$baris,$row->class);
            $sheet->setCellValue('E'.$baris,$row->lesson);
            $sheet->setCellValue('F'.$baris,$row->presence);
            $sheet->setCellValue('G'.$baris,$row->date);
            $sheet->setCellValue('H'.$baris,$row->remark);
            $sheet->setCellValue('I'.$baris,$row->mobile);
            $sheet->setCellValue('J'.$baris,$row->parent);
            $sheet->setCellValue('K'.$baris,$row->parent_mobile);
            $baris++;
            $number++;
        }
        //
        $sheet->getStyle('A1:A2')->applyFromArray($this->PHPExcelCommonStyle()['title']);
        $sheet->getStyle('A3:A3')->applyFromArray($this->PHPExcelCommonStyle()['subTitle']);
        $sheet->getStyle('A5:K5')->applyFromArray($this->PHPExcelCommonStyle()['header']);
        $sheet->getStyle('A6:A'.$baris = $baris - 1)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('B6:B'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('C6:C'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('D6:D'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('E6:E'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('F6:F'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('G6:G'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('H6:H'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('I6:I'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('J6:J'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('K6:K'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        //
        $filename = date('Ymdhis') . '_' . Str::lower(config('app.name')) . '_laporan_presensi_pelajaran_absen.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save('storage/downloads/'.$filename);
        echo $filename;
    }

    /* Lesson Presence Teaching Reflection */
    
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function presenceLessonReflection(Request $request)
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
        return view('academic::reports.presences.presence_lesson_reflection', $data);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function presenceLessonReflectionData(Request $request)
    {
        return response()->json($this->presenceLessonEloquent->reportPresenceLessonReflection($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function presenceLessonReflectionToPdf(Request $request)
    {
        $payload = json_decode($request->data);
        $vals['payload'] = $payload;
        $vals['profile'] = $this->getInstituteProfile();
        $view = View::make('academic::reports.presences.presence_lesson_reflection_pdf', $vals);
        $name = Str::lower(config('app.name')) .'_laporan_refleksi_mengajar';
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
    public function presenceLessonReflectionToExcel(Request $request)
    {
        $payload = json_decode($request->data);
        //
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('laporan_refleksi_mengajar');
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        //
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(30);
        $sheet->getColumnDimension('G')->setWidth(50);
        //
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');
        $sheet->mergeCells('A3:G3');
        //
        $sheet->setCellValue('A1', Str::upper($request->session()->get('institute')));
        $sheet->setCellValue('A2', 'LAPORAN REFLEKSI MENGAJAR - ' . $payload->start_date .' s.d '. $payload->end_date);
        $sheet->setCellValue('A3', 'DEPARTEMEN ' . $payload->department . ' - TAHUN AJARAN ' . $payload->schoolyear . ' - TINGKAT '. $payload->grade . ' - KELAS '. $payload->class . ' - SEMESTER '. $payload->semester . ' - PELAJARAN '. $payload->lesson);
        $sheet->setCellValue('A5', 'NO.');
        $sheet->setCellValue('B5', 'TANGGAL');
        $sheet->setCellValue('C5', 'JAM');
        $sheet->setCellValue('D5', 'KELAS');
        $sheet->setCellValue('E5', 'STATUS');
        $sheet->setCellValue('F5', 'PELAJARAN');
        $sheet->setCellValue('G5', 'REFLEKSI');
        //
        $baris = 6;
        $number = 1;

        foreach ($payload->rows as $row) 
        {
            $sheet->setCellValue('A'.$baris,$number);
            $sheet->setCellValue('B'.$baris,$row->date);
            $sheet->setCellValue('C'.$baris,$row->time);
            $sheet->setCellValue('D'.$baris,$row->class);
            $sheet->setCellValue('E'.$baris,$row->status);
            $sheet->setCellValue('F'.$baris,$row->lesson);
            $sheet->setCellValue('G'.$baris,'Materi: ' . $row->subject . "\n" . 'Rencana: ' . $row->plan . "\n" . 'Ket. Kehadiran: ' . $row->remark)->getStyle('G'.$baris)->getAlignment()->setWrapText(true);
            $baris++;
            $number++;
        }
        //
        $sheet->getStyle('A1:A2')->applyFromArray($this->PHPExcelCommonStyle()['title']);
        $sheet->getStyle('A3:A3')->applyFromArray($this->PHPExcelCommonStyle()['subTitle']);
        $sheet->getStyle('A5:G5')->applyFromArray($this->PHPExcelCommonStyle()['header']);
        $sheet->getStyle('A6:A'.$baris = $baris - 1)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('B6:B'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('C6:C'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('D6:D'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('E6:E'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('F6:F'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('G6:G'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        //
        $filename = date('Ymdhis') . '_' . Str::lower(config('app.name')) . '_laporan_refleksi_mengajar.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save('storage/downloads/'.$filename);
        echo $filename;
    }

    /* Lesson Presence Student Stat */

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function presenceLessonStat(Request $request)
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
        return view('academic::reports.presences.presence_lesson_stat', $data);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function presenceLessonStatData(Request $request)
    {
        return response()->json($this->presenceLessonEloquent->reportPresenceLessonStat($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function presenceLessonStatToPdf(Request $request)
    {
        $payload = json_decode($request->data);
        $vals['payload'] = $payload;
        $vals['profile'] = $this->getInstituteProfile();
        $view = View::make('academic::reports.presences.presence_lesson_stat_pdf', $vals);
        $name = Str::lower(config('app.name')) .'_statistik_presensi_pelajaran';
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortraits($hashfile, $filename);
        echo $filename;
    }

    
    /* Lesson Presence Student Stat Class */

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function presenceLessonStatClass(Request $request)
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
        return view('academic::reports.presences.presence_lesson_stat_class', $data);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function presenceLessonStatClassData(Request $request)
    {
        return response()->json($this->presenceLessonEloquent->reportPresenceLessonStatClass($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function presenceLessonStatClassToPdf(Request $request)
    {
        $payload = json_decode($request->data);
        $vals['payload'] = $payload;
        $vals['profile'] = $this->getInstituteProfile();
        $view = View::make('academic::reports.presences.presence_lesson_stat_class_pdf', $vals);
        $name = Str::lower(config('app.name')) .'_statistik_presensi_pelajaran_kelas';
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortraits($hashfile, $filename);
        echo $filename;
    }
}
