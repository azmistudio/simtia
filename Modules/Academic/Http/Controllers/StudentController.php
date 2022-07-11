<?php

namespace Modules\Academic\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use App\Models\Reference;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\PdfTrait;
use App\Http\Traits\ReferenceTrait;
use Modules\Academic\Entities\AdmissionProspect;
use Modules\Academic\Entities\Columns;
use Modules\Academic\Entities\ColumnOption;
use Modules\Academic\Entities\ColumnStudent;
use Modules\Academic\Entities\Students;
use Modules\Academic\Http\Requests\StudentRequest;
use Modules\Academic\Repositories\Student\StudentEloquent;
use Modules\Academic\Repositories\Student\ColumnStudentEloquent;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;
use View;
use Exception;

class StudentController extends Controller
{
    use HelperTrait;
    use PdfTrait;
    use ReferenceTrait;

    private $subject = 'Data Santri';

    function __construct(StudentEloquent $studentEloquent, columnStudentEloquent $columnStudentEloquent)
    {
        $this->studentEloquent = $studentEloquent;
        $this->columnStudentEloquent = $columnStudentEloquent;
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
        $data['tribes'] = Reference::where('category', 'hr_tribe')->get();
        $data['student_status'] = Reference::where('category', 'hr_student_status')->get();
        $data['parent_status'] = Reference::where('category', 'hr_parent_status')->get();
        $data['economics'] = Reference::where('category', 'hr_economic')->get();
        $data['educations'] = Reference::where('category', 'hr_education')->get();
        $data['jobs'] = Reference::where('category', 'hr_job')->get();
        $data['bloods'] = Reference::where('category', 'hr_blood')->get();
        $data['child_status'] = Reference::where('category', 'hr_child_status')->get();
        $data['columns'] = Columns::select('id','name','type')->where('is_active', 1)->orderBy('department_id','asc')->orderBy('order','asc')->get();
        $data['column_opts'] = ColumnOption::select('id','column_id','name')->where('is_active', 1)->orderBy('column_id','asc')->orderBy('order','asc')->get();
        return view('academic::pages.students.student', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function store(StudentRequest $request)
    {
        $validated = $request->validated();
        if ($request->file('photo')) 
        {
            $imagePath = $request->file('photo');
            $imageName = date('YmdHis') . '-' . $this->filter_filename($imagePath->getClientOriginalName());
            $path = $request->file('photo')->storeAs('uploads/student', $imageName, 'public');
        }
        try 
        {
            $request->merge([
                'name' => Str::lower($request->name),
                'surname' => Str::lower($request->surname),
                'dob' => Carbon::createFromFormat('d/m/Y', $request->dob),
                'child_brother' => $request->child_brother ?: 0,
                'child_brother_sum' => $request->child_brother_sum ?: 0,
                'child_step_sum' => $request->child_step_sum ?: 0,
                'weight' => $request->weight ?: 0,
                'height' => $request->height ?: 0,
                'distance' => $request->distance ?: 0,
                'email' => Str::lower($request->email),
                'father_dob' => Carbon::createFromFormat('d/m/Y', $request->father_dob),
                'mother_dob' => Carbon::createFromFormat('d/m/Y', $request->mother_dob),
                'father_income' => isset($request->father_income) ? Str::remove('Rp', Str::remove(',', $request->father_income)) : 0.00,
                'mother_income' => isset($request->mother_income) ? Str::remove('Rp', Str::remove(',', $request->mother_income)) : 0.00,
                'is_active' => $request->is_active ?: 1,
                'logged' => auth()->user()->email,
            ]);
            //
            if (isset($imageName)) 
            {
                $request->merge([
                    'photo' => $imageName
                ]);
            }
            $this->columnStudentEloquent->destroy($request->id, $this->subject);
            $this->columnStudentEloquent->create($request, $request->id);
            $this->studentEloquent->update($request, $this->subject);
            $response = $this->getResponse('store', '', $this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'NIS (Nomor Induk Santri)');
        }
        return response()->json($response);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $query_student = collect(Students::find($id));
        $query_additionals = ColumnStudent::where('student_id', $id)->get();
        if (count($query_additionals) > 0)
        {
            $response = $query_student->merge(['columns' => $query_additionals]);
        } else {
            $response = $query_student;
        }
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try 
        {
            DB::transaction(function () use ($id) {
                $this->columnStudentEloquent->destroy($id, $this->subject);
                $admission_id = Students::where('id', $id)->pluck('prospect_student_id')->first();
                AdmissionProspect::where('id', $admission_id)->update(['student_id' => null]);
                $this->studentEloquent->destroy($id, $this->subject);
            });
            $response = $this->getResponse('destroy', '', $this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), $this->subject);
        }
        return response()->json($response);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function data(Request $request)
    {
        return response()->json($this->studentEloquent->data($request));
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function list(Request $request)
    {
        return response()->json($this->studentEloquent->list($request));
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function comboGrid(Request $request)
    {
        return response()->json($this->studentEloquent->comboGrid($request));
    }

    /**
     * Export resource to Excel Document.
     * @return Excel
     */
    public function toExcel(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(strval(Str::of($this->subject)->snake()));
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        //
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(50);
        $sheet->getColumnDimension('G')->setWidth(30);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(40);
        $sheet->getColumnDimension('K')->setWidth(40);
        $sheet->getColumnDimension('L')->setWidth(20);
        $sheet->getColumnDimension('M')->setWidth(20);
        $sheet->getColumnDimension('N')->setWidth(20);
        $sheet->getColumnDimension('O')->setWidth(20);
        $sheet->getColumnDimension('P')->setWidth(20);
        $sheet->getColumnDimension('Q')->setWidth(20);
        $sheet->getColumnDimension('R')->setWidth(20);
        $sheet->getColumnDimension('S')->setWidth(40);
        $sheet->getColumnDimension('T')->setWidth(20);
        $sheet->getColumnDimension('U')->setWidth(20);
        $sheet->getColumnDimension('V')->setWidth(20);
        $sheet->getColumnDimension('W')->setWidth(20);
        $sheet->getColumnDimension('X')->setWidth(20);
        $sheet->getColumnDimension('Y')->setWidth(20);
        $sheet->getColumnDimension('Z')->setWidth(20);
        $sheet->getColumnDimension('AA')->setWidth(20);
        $sheet->getColumnDimension('AB')->setWidth(30);
        $sheet->getColumnDimension('AC')->setWidth(30);
        $sheet->getColumnDimension('AD')->setWidth(30);
        $sheet->getColumnDimension('AE')->setWidth(30);
        $sheet->getColumnDimension('AF')->setWidth(20);
        $sheet->getColumnDimension('AG')->setWidth(20);
        $sheet->getColumnDimension('AH')->setWidth(30);
        $sheet->getColumnDimension('AI')->setWidth(20);
        $sheet->getColumnDimension('AJ')->setWidth(20);
        $sheet->getColumnDimension('AK')->setWidth(30);
        $sheet->getColumnDimension('AL')->setWidth(20);
        $sheet->getColumnDimension('AM')->setWidth(40);
        $sheet->getColumnDimension('AN')->setWidth(30);
        $sheet->getColumnDimension('AO')->setWidth(20);
        $sheet->getColumnDimension('AP')->setWidth(20);
        $sheet->getColumnDimension('AQ')->setWidth(30);
        $sheet->getColumnDimension('AR')->setWidth(20);
        $sheet->getColumnDimension('AS')->setWidth(20);
        $sheet->getColumnDimension('AT')->setWidth(20);
        $sheet->getColumnDimension('AU')->setWidth(20);
        $sheet->getColumnDimension('AV')->setWidth(30);
        $sheet->getColumnDimension('AW')->setWidth(30);
        $sheet->getColumnDimension('AX')->setWidth(20);
        //
        $sheet->mergeCells('A4:A5');
        $sheet->mergeCells('B4:B5');
        $sheet->mergeCells('C4:C5');
        $sheet->mergeCells('D4:D5');
        $sheet->mergeCells('E4:E5');
        $sheet->mergeCells('F4:F5');
        $sheet->mergeCells('G4:G5');
        $sheet->mergeCells('H4:H5');
        $sheet->mergeCells('I4:I5');
        $sheet->mergeCells('J4:J5');
        $sheet->mergeCells('K4:K5');
        $sheet->mergeCells('L4:L5');
        $sheet->mergeCells('M4:M5');
        $sheet->mergeCells('N4:N5');
        $sheet->mergeCells('O4:O5');
        $sheet->mergeCells('P4:P5');
        $sheet->mergeCells('Q4:Q5');
        $sheet->mergeCells('R4:R5');
        $sheet->mergeCells('S4:S5');
        $sheet->mergeCells('T4:T5');
        $sheet->mergeCells('U4:U5');
        $sheet->mergeCells('V4:V5');
        $sheet->mergeCells('W4:W5');
        $sheet->mergeCells('X4:X5');
        $sheet->mergeCells('Y4:Y5');
        $sheet->mergeCells('Z4:Z5');
        $sheet->mergeCells('AA4:AA5');
        $sheet->mergeCells('AB4:AB5');
        $sheet->mergeCells('AC4:AC5');
        $sheet->mergeCells('AD4:AL4');
        $sheet->mergeCells('AM4:AU4');
        $sheet->mergeCells('AV4:AV5');
        $sheet->mergeCells('AW4:AW5');
        $sheet->mergeCells('AX4:AX5');
        //
        $sheet->setCellValue('A1', Str::upper($request->session()->get('institute')));
        $sheet->setCellValue('A2', 'DATA SANTRI - ' . config('app.name'));
        $sheet->setCellValue('A4', 'NO.');
        $sheet->setCellValue('B4', 'DEPARTEMEN');
        $sheet->setCellValue('C4', 'TAHUN AJARAN');
        $sheet->setCellValue('D4', 'TINGKAT/KELAS');
        $sheet->setCellValue('E4', 'NIS');
        $sheet->setCellValue('F4', 'NAMA');
        $sheet->setCellValue('G4', 'PANGGILAN');
        $sheet->setCellValue('H4', 'JENIS KELAMIN');
        $sheet->setCellValue('I4', 'TAHUN MASUK');
        $sheet->setCellValue('J4', 'TEMPAT, TANGGAL LAHIR');
        $sheet->setCellValue('K4', 'ALAMAT');
        $sheet->setCellValue('L4', 'KODE POS');
        $sheet->setCellValue('M4', 'JARAK');
        $sheet->setCellValue('N4', 'TELEPON');
        $sheet->setCellValue('O4', 'HANDPHONE');
        $sheet->setCellValue('P4', 'EMAIL');
        $sheet->setCellValue('Q4', 'STATUS');
        $sheet->setCellValue('R4', 'KONDISI');
        $sheet->setCellValue('S4', 'KESEHATAN');
        $sheet->setCellValue('T4', 'SUKU');
        $sheet->setCellValue('U4', 'WARGA');
        $sheet->setCellValue('V4', 'BERAT');
        $sheet->setCellValue('W4', 'TINGGI');
        $sheet->setCellValue('X4', 'GOL. DARAH');
        $sheet->setCellValue('Y4', 'ANAK KE');
        $sheet->setCellValue('Z4', 'BERSAUDARA');
        $sheet->setCellValue('AA4', 'STATUS ANAK');
        $sheet->setCellValue('AB4', 'JML. SAUDARA KANDUNG');
        $sheet->setCellValue('AC4', 'JML. SAUDARA TIRI');
        $sheet->setCellValue('AD4', 'AYAH');
        $sheet->setCellValue('AD5', 'NAMA');
        $sheet->setCellValue('AE5', 'STATUS');
        $sheet->setCellValue('AF5', 'KELAHIRAN');
        $sheet->setCellValue('AG5', 'TGL. LAHIR');
        $sheet->setCellValue('AH5', 'EMAIL');
        $sheet->setCellValue('AI5', 'PENDIDIKAN');
        $sheet->setCellValue('AJ5', 'PEKERJAAN');
        $sheet->setCellValue('AK5', 'PENGHASILAN');
        $sheet->setCellValue('AL5', 'HANDPHONE');
        $sheet->setCellValue('AM4', 'IBU');
        $sheet->setCellValue('AM5', 'NAMA');
        $sheet->setCellValue('AN5', 'STATUS');
        $sheet->setCellValue('AO5', 'KELAHIRAN');
        $sheet->setCellValue('AP5', 'TGL. LAHIR');
        $sheet->setCellValue('AQ5', 'EMAIL');
        $sheet->setCellValue('AR5', 'PENDIDIKAN');
        $sheet->setCellValue('AS5', 'PEKERJAAN');
        $sheet->setCellValue('AT5', 'PENGHASILAN');
        $sheet->setCellValue('AU5', 'HANDPHONE');
        $sheet->setCellValue('AV4', 'NAMA WALI');
        $sheet->setCellValue('AW4', 'ALAMAT');
        $sheet->setCellValue('AX4', 'STATUS AKTIF');
        //
        $add_columns = array();
        $idDepartment = collect(json_decode($request->data))->pluck('department_id')->toArray();
        $columns = Columns::select('id','name','type')->where('department_id',$idDepartment[0])->where('is_active', 1)->orderBy('id','asc')->get();
        $total_col = count($columns);
        if ($total_col > 0) 
        {
            $index_col = $sheet->getColumnDimensionByColumn($total_col + 50)->getColumnIndex();
            $row = 5;
            $cell = $index_col.$row;
            $range = 'A4:'.$cell;
            //        
            $c = 1;
            foreach ($columns as $col) 
            {
                $add_columns[] = array($col->id, $col->type);
                $sheet->mergeCellsByColumnAndRow($c + 50, 4, $c + 50, 5);
                $sheet->setCellValueByColumnAndRow($c + 50, 4, Str::upper($col->name));
                $sheet->getColumnDimensionByColumn($c + 50)->setWidth(25);
                $c++;
            }
        } else {
            $range = 'A4:AX5';
        }
        //
        $baris = 6;
        $number = 1;
        $data = json_decode($request->data);
        $idArray = array();
        foreach ($data as $d) 
        { 
            $idArray[] = $d->id;
        }
        $query = $this->studentEloquent->showIn($idArray);
        foreach ($query as $q) 
        {
            $sheet->setCellValue('A'.$baris,$number);
            $sheet->setCellValue('B'.$baris,$q->department);
            $sheet->setCellValue('C'.$baris,$q->school_year);
            $sheet->setCellValue('D'.$baris,Str::upper($q->grade) . ' - ' . ucwords($q->class));
            $sheet->setCellValue('E'.$baris,$q->student_no);
            $sheet->setCellValue('F'.$baris,$q->name);
            $sheet->setCellValue('G'.$baris,$q->surname);
            $sheet->setCellValue('H'.$baris,$q->gender);
            $sheet->setCellValue('I'.$baris,$q->year_entry);
            $sheet->setCellValue('J'.$baris,$q->pob .', '. $q->dob->format('d/m/Y'));
            $sheet->setCellValue('K'.$baris,$q->address);
            $sheet->setCellValue('L'.$baris,$q->postal_code);
            $sheet->setCellValue('M'.$baris,$q->distance . 'KM');
            $sheet->setCellValue('N'.$baris,$q->phone);
            $sheet->setCellValue('O'.$baris,$q->mobile);
            $sheet->setCellValue('P'.$baris,$q->email); 
            $sheet->setCellValue('Q'.$baris,$this->getReferences('hr_student_status')[$q->student_status]); 
            $sheet->setCellValue('R'.$baris,$this->getReferences('hr_economic')[$q->economic]); 
            $sheet->setCellValue('S'.$baris,$q->medical); 
            $sheet->setCellValue('T'.$baris,$this->getReferences('hr_tribe')[$q->tribe]); 
            $sheet->setCellValue('U'.$baris,$this->getCitizen()[$q->citizen]); 
            $sheet->setCellValue('V'.$baris,$q->weight .'KG'); 
            $sheet->setCellValue('W'.$baris,$q->height .'CM'); 
            $sheet->setCellValue('X'.$baris,$this->getReferences('hr_blood')[$q->blood]); 
            $sheet->setCellValue('Y'.$baris,$q->child_no); 
            $sheet->setCellValue('Z'.$baris,$q->child_brother);
            $sheet->setCellValue('AA'.$baris,$this->getReferences('hr_child_status')[$q->child_status]);
            $sheet->setCellValue('AB'.$baris,$q->child_brother_sum);
            $sheet->setCellValue('AC'.$baris,$q->child_step_sum);
            $sheet->setCellValue('AD'.$baris,$q->father);
            $sheet->setCellValue('AE'.$baris,$this->getReferences('hr_parent_status')[$q->father_status]);
            $sheet->setCellValue('AF'.$baris,$q->father_pob);
            $sheet->setCellValue('AG'.$baris,$q->father_dob->format('d/m/Y'));
            $sheet->setCellValue('AH'.$baris,$q->father_email);
            $sheet->setCellValue('AI'.$baris,$this->getReferences('hr_education')[$q->father_education]);
            $sheet->setCellValue('AJ'.$baris,$this->getReferences('hr_job')[$q->father_job]);
            $sheet->setCellValue('AK'.$baris,'Rp'.number_format($q->father_income,2));
            $sheet->setCellValue('AL'.$baris,$q->father_mobile);
            $sheet->setCellValue('AM'.$baris,$q->mother);
            $sheet->setCellValue('AN'.$baris,$this->getReferences('hr_parent_status')[$q->mother_status]);
            $sheet->setCellValue('AO'.$baris,$q->mother_pob);
            $sheet->setCellValue('AP'.$baris,$q->mother_dob->format('d/m/Y'));
            $sheet->setCellValue('AQ'.$baris,$q->mother_email);
            $sheet->setCellValue('AR'.$baris,$this->getReferences('hr_education')[$q->mother_education]);
            $sheet->setCellValue('AS'.$baris,$this->getReferences('hr_job')[$q->mother_job]);
            $sheet->setCellValue('AT'.$baris,'Rp'.number_format($q->mother_income,2));
            $sheet->setCellValue('AU'.$baris,$q->mother_mobile);
            $sheet->setCellValue('AV'.$baris,$q->parent_guardian);
            $sheet->setCellValue('AW'.$baris,$q->parent_address);
            $sheet->setCellValue('AX'.$baris,$q->is_active);
            //
            if (count($add_columns) > 0)
            {
                for ($i = 0; $i < count($add_columns); $i++)
                {
                    $query_col = ColumnStudent::where('student_id', $q->id)->where('column_id',$add_columns[$i][0])->first();
                    if (!is_null($query_col))
                    {
                        if ($query_col->type == 1)
                        {
                            $sheet->setCellValueByColumnAndRow($i + 51, $baris, Str::upper(optional($query_col)->values));
                        } else {
                            $sheet->setCellValueByColumnAndRow($i + 51, $baris, Str::upper(optional(optional($query_col)->getColumnOption)->name));
                        }
                    }
                }
            }
            //
            $baris++;
            $number++;
        }
        //
        $sheet->getStyle('A1:A2')->applyFromArray($this->PHPExcelCommonStyle()['title']);
        $sheet->getStyle($range)->applyFromArray($this->PHPExcelCommonStyle()['header']);
        $sheet->getStyle('A6:A'.$baris = $baris - 1)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('B6:B'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('C6:C'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('D6:D'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('E6:E'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('F6:F'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('G6:G'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('H6:H'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('I6:I'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('J6:J'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('K6:K'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('L6:L'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('M6:M'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('N6:N'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('O6:O'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('P6:P'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('Q6:Q'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('R6:R'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('S6:S'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('T6:T'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('U6:U'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('V6:V'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('W6:W'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('X6:X'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('Y6:Y'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('Z6:Z'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('AA6:AA'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('AB6:AB'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('AC6:AC'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('AD6:AD'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('AE6:AE'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('AF6:AF'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('AG6:AG'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('AH6:AH'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('AI6:AI'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('AJ6:AJ'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('AK6:AK'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
        $sheet->getStyle('AL6:AL'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('AM6:AM'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('AN6:AN'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('AO6:AO'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('AP6:AP'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('AQ6:AQ'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('AR6:AR'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('AS6:AS'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('AT6:AT'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
        $sheet->getStyle('AU6:AU'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('AV6:AV'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('AW6:AW'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('AX6:AX'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        if (count($add_columns) > 0)
        {
            $start_col = $sheet->getColumnDimensionByColumn(51)->getColumnIndex();
            $index_col = $sheet->getColumnDimensionByColumn($total_col + 50)->getColumnIndex();
            $row = 6;
            $range = $start_col.$row.':'.$index_col;
            $sheet->getStyle($range.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        }
        //
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake();
        $filename = date('Ymdhis') . '_' . $name . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save('storage/downloads/'.$filename);
        echo $filename;
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdf(Request $request)
    {
        $idArray = collect(json_decode($request->data))->pluck('id')->toArray();
        $query['models'] = $this->studentEloquent->showIn($idArray);
        //
        $view = View::make('academic::pages.students.student_pdf', $query);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake();
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfLandscape($hashfile, $filename);
        echo $filename;
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function print(Request $request)
    {
        $payload = json_decode($request->data);
        $data['profile'] = $this->getInstituteProfile();           
        $data['model'] = Students::find($payload->id);
        $add_columns = ColumnStudent::where('student_id', $payload->id)->get();
        if (count($add_columns) > 0)
        {
            $data['columns'] = $add_columns;
        }
        // 
        $view = View::make('academic::pages.students.student_detail_pdf', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_' . $data['model']->id;
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortrait($hashfile, $filename);
        echo $filename;
    }

    /**
     * Display a listing of data for combogrid.
     * @return JSON
     */
    public function comboGridPlacement(Request $request)
    {
        return response()->json($this->studentEloquent->combogridPlacement($request));
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function dataRoom(Request $request)
    {
        return response()->json($this->studentEloquent->dataRoom($request));
    }
}
