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
use Modules\Academic\Entities\Admission;
use Modules\Academic\Entities\AdmissionProspect;
use Modules\Academic\Entities\AdmissionProspectGroupView;
use Modules\Academic\Entities\Columns;
use Modules\Academic\Entities\ColumnOption;
use Modules\Academic\Entities\ColumnProspectStudent;
use Modules\Academic\Http\Requests\AdmissionProspectRequest;
use Modules\Academic\Repositories\Academic\ColumnProspectEloquent;
use Modules\Academic\Repositories\Admission\ProspectEloquent;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;
use View;
use Exception;

class AdmissionProspectController extends Controller
{
    use HelperTrait;
    use PdfTrait;
    use ReferenceTrait;

    private $subject = 'Data Calon Santri';

    function __construct(ProspectEloquent $prospectEloquent, ColumnProspectEloquent $columnProspectEloquent)
    {
        $this->prospectEloquent = $prospectEloquent;
        $this->columnProspectEloquent = $columnProspectEloquent;
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
        return view('academic::pages.admissions.admission_prospect', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @param $id
     * @return Renderable
     */
    public function store(AdmissionProspectRequest $request)
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
                'student_no' => '-',
                'name' => Str::lower($request->name),
                'surname' => Str::lower($request->surname),
                'dob' => $this->formatDate($request->dob,'sys'),
                'child_brother' => $request->child_brother ?: 0,
                'child_brother_sum' => $request->child_brother_sum ?: 0,
                'child_step_sum' => $request->child_step_sum ?: 0,
                'weight' => $request->weight ?: 0,
                'height' => $request->height ?: 0,
                'distance' => $request->distance ?: 0,
                'email' => Str::lower($request->email),
                'father_dob' => $this->formatDate($request->father_dob,'sys'),
                'mother_dob' => $this->formatDate($request->mother_dob,'sys'),
                'father_income' => isset($request->father_income) ? Str::remove('Rp', Str::remove(',', $request->father_income)) : 0.00,
                'mother_income' => isset($request->mother_income) ? Str::remove('Rp', Str::remove(',', $request->mother_income)) : 0.00,
                'donation_1' => isset($request->donation_1) ? Str::remove('Rp', Str::remove(',', $request->donation_1)) : 0.00,
                'donation_2' => isset($request->donation_2) ? Str::remove('Rp', Str::remove(',', $request->donation_2)) : 0.00,
                'exam_01' => isset($request->exam_01) ? $request->exam_01 : 0.00,
                'exam_02' => isset($request->exam_02) ? $request->exam_02 : 0.00,
                'exam_03' => isset($request->exam_03) ? $request->exam_03 : 0.00,
                'exam_04' => isset($request->exam_04) ? $request->exam_04 : 0.00,
                'exam_05' => isset($request->exam_05) ? $request->exam_05 : 0.00,
                'exam_06' => isset($request->exam_06) ? $request->exam_06 : 0.00,
                'exam_07' => isset($request->exam_07) ? $request->exam_07 : 0.00,
                'exam_08' => isset($request->exam_08) ? $request->exam_08 : 0.00,
                'exam_09' => isset($request->exam_09) ? $request->exam_09 : 0.00,
                'exam_10' => isset($request->exam_10) ? $request->exam_10 : 0.00,
                'is_active' => $request->is_active ?: 1,
                'logged' => auth()->user()->email,
            ]);
            if ($request->id < 1) 
            {
                $admissionCode = Admission::where('id', $request->admission_id)->first();
                $lastRegistration = AdmissionProspect::select('registration_no')->count();
                $newRegistration = $lastRegistration > 0 ? Str::upper($admissionCode->prefix) . '-' . sprintf('%05d', $lastRegistration + 1) : Str::upper($admissionCode->prefix) . '-' . sprintf('%05d', 1);
                //
                $request->merge([
                    'registration_no' => $newRegistration,
                    'year_entry' => date('Y'),
                    'religion' => 1,
                    'language' => 'Bahasa',
                    'photo' => isset($imageName) ? $imageName : '',
                ]);
                // check quota
                $quota = AdmissionProspectGroupView::where('id', $request->prospect_group_id)->first();
                if ($quota->capacity == $quota->occupied)
                {
                    throw new Exception('Kuota Kelompok sudah terpenuhi, silahkan pilih Kelompok lainnya.', 1);
                } else {
                    $query = $this->prospectEloquent->create($request, $this->subject);
                    $this->columnProspectEloquent->create($request, $query->id);
                    $response = $this->getResponse('store', '', $this->subject);
                }
            } else {
                $action = 'Ubah Simpan';
                if (isset($imageName)) 
                {
                    $request->merge([
                        'photo' => $imageName
                    ]);
                }
                $is_col_exist = ColumnProspectStudent::where('prospect_student_id', $request->id)->first();
                if (isset($is_col_exist))
                {
                    $this->columnProspectEloquent->destroy($request->id, $this->subject);
                    $this->columnProspectEloquent->create($request, $request->id);
                } else {
                    $this->columnProspectEloquent->create($request, $request->id);
                }
                $this->prospectEloquent->update($request, $this->subject);
                $response = $this->getResponse('store', '', $this->subject);
            }
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Calon Santri');
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
        $query_additionals = ColumnProspectStudent::where('prospect_student_id', $id)->get();
        $query_prospective = collect(AdmissionProspect::find($id));
        if (count($query_additionals) > 0)
        {
            $response = $query_prospective->merge(['columns' => $query_additionals]);
        } else {
            $response = $query_prospective;
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
            $this->columnProspectEloquent->destroy($id, $this->subject);
            $this->prospectEloquent->destroy($id, $this->subject);
            $response = $this->getResponse('destroy', '', $this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Calon Santri');
        }
        return response()->json($response);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function data(Request $request)
    {
        return response()->json($this->prospectEloquent->data($request));
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function dataView(Request $request)
    {
        return response()->json($this->prospectEloquent->dataView($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function print(Request $request)
    {
        $payload = json_decode($request->data);
        $data['model'] = AdmissionProspect::find($payload->id);
        $add_columns = ColumnProspectStudent::where('prospect_student_id', $payload->id)->get();
        if (count($add_columns) > 0)
        {
            $data['columns'] = $add_columns;
        }
        $data['profile'] = $this->getInstituteProfile();
        //
        $view = View::make('academic::pages.admissions.prospective_student_detail_pdf', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() .'_'. $data['model']->id;
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
    public function toExcel(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(strval(Str::of($this->subject)->snake()));
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        //
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(40);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(30);
        $sheet->getColumnDimension('K')->setWidth(30);
        $sheet->getColumnDimension('L')->setWidth(20);
        $sheet->getColumnDimension('M')->setWidth(20);
        $sheet->getColumnDimension('N')->setWidth(20);
        $sheet->getColumnDimension('O')->setWidth(20);
        $sheet->getColumnDimension('P')->setWidth(20);
        $sheet->getColumnDimension('Q')->setWidth(20);
        $sheet->getColumnDimension('R')->setWidth(20);
        $sheet->getColumnDimension('S')->setWidth(20);
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
        $sheet->getColumnDimension('AF')->setWidth(30);
        $sheet->getColumnDimension('AG')->setWidth(20);
        $sheet->getColumnDimension('AH')->setWidth(30);
        $sheet->getColumnDimension('AI')->setWidth(20);
        $sheet->getColumnDimension('AJ')->setWidth(20);
        $sheet->getColumnDimension('AK')->setWidth(20);
        $sheet->getColumnDimension('AL')->setWidth(20);
        $sheet->getColumnDimension('AM')->setWidth(30);
        $sheet->getColumnDimension('AN')->setWidth(30);
        $sheet->getColumnDimension('AO')->setWidth(30);
        $sheet->getColumnDimension('AP')->setWidth(20);
        $sheet->getColumnDimension('AQ')->setWidth(20);
        $sheet->getColumnDimension('AR')->setWidth(20);
        $sheet->getColumnDimension('AS')->setWidth(20);
        $sheet->getColumnDimension('AT')->setWidth(20);
        $sheet->getColumnDimension('AU')->setWidth(20);
        $sheet->getColumnDimension('AV')->setWidth(20);
        $sheet->getColumnDimension('AW')->setWidth(30);
        $sheet->getColumnDimension('AX')->setWidth(20);
        $sheet->getColumnDimension('AY')->setWidth(20);
        $sheet->getColumnDimension('AZ')->setWidth(20);
        $sheet->getColumnDimension('BA')->setWidth(20);
        $sheet->getColumnDimension('BB')->setWidth(20);
        $sheet->getColumnDimension('BC')->setWidth(20);
        $sheet->getColumnDimension('BD')->setWidth(20);
        $sheet->getColumnDimension('BE')->setWidth(20);
        $sheet->getColumnDimension('BF')->setWidth(20);
        $sheet->getColumnDimension('BG')->setWidth(20);
        $sheet->getColumnDimension('BH')->setWidth(20);
        $sheet->getColumnDimension('BI')->setWidth(20);
        $sheet->getColumnDimension('BJ')->setWidth(20);
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
        $sheet->mergeCells('AY4:AY5');
        $sheet->mergeCells('AZ4:AZ5');
        $sheet->mergeCells('BA4:BA5');
        $sheet->mergeCells('BB4:BB5');
        $sheet->mergeCells('BC4:BC5');
        $sheet->mergeCells('BD4:BD5');
        $sheet->mergeCells('BE4:BE5');
        $sheet->mergeCells('BF4:BF5');
        $sheet->mergeCells('BG4:BG5');
        $sheet->mergeCells('BH4:BH5');
        $sheet->mergeCells('BI4:BI5');
        $sheet->mergeCells('BJ4:BJ5');
        //
        $sheet->setCellValue('A1', Str::upper($request->session()->get('institute')));
        $sheet->setCellValue('A2', 'DATA CALON SANTRI - SIMTIA');
        $sheet->setCellValue('A4', 'NO.');
        $sheet->setCellValue('B4', 'DEPARTEMEN');
        $sheet->setCellValue('C4', 'PROSES PENERIMAAN');
        $sheet->setCellValue('D4', 'KLP. CALON SANTRI');
        $sheet->setCellValue('E4', 'NO. PENDAFTARAN');
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
        $sheet->setCellValue('AX4', 'SUMBANGAN #1');
        $sheet->setCellValue('AY4', 'SUMBANGAN #2');
        $sheet->setCellValue('AZ4', 'UJIAN #1');
        $sheet->setCellValue('BA4', 'UJIAN #2');
        $sheet->setCellValue('BB4', 'UJIAN #3');
        $sheet->setCellValue('BC4', 'UJIAN #4');
        $sheet->setCellValue('BD4', 'UJIAN #5');
        $sheet->setCellValue('BE4', 'UJIAN #6');
        $sheet->setCellValue('BF4', 'UJIAN #7');
        $sheet->setCellValue('BG4', 'UJIAN #8');
        $sheet->setCellValue('BH4', 'UJIAN #9');
        $sheet->setCellValue('BI4', 'UJIAN #10');
        $sheet->setCellValue('BJ4', 'STATUS AKTIF');
        //
        $add_columns = array();
        $idDepartment = collect(json_decode($request->data))->pluck('department_id')->toArray();
        $columns = Columns::select('id','name','type')->where('department_id',$idDepartment[0])->where('is_active', 1)->orderBy('id','asc')->get();
        $total_col = count($columns);
        if ($total_col > 0) 
        {
            $index_col = $sheet->getColumnDimensionByColumn($total_col + 62)->getColumnIndex();
            $row = 5;
            $cell = $index_col.$row;
            $range = 'A4:'.$cell;
            //        
            $c = 1;
            foreach ($columns as $col) 
            {
                $add_columns[] = array($col->id, $col->type);
                $sheet->mergeCellsByColumnAndRow($c + 62, 4, $c + 62, 5);
                $sheet->setCellValueByColumnAndRow($c + 62, 4, Str::upper($col->name));
                $sheet->getColumnDimensionByColumn($c + 62)->setWidth(25);
                $c++;
            }
        } else {
            $range = 'A4:BJ5';
        }
        //
        $baris = 6;
        $number = 1;
        $idArray = collect(json_decode($request->data))->pluck('id')->toArray();
        $query = $this->prospectEloquent->showIn($idArray);
        foreach ($query as $q) 
        {
            $sheet->setCellValue('A'.$baris,$number);
            $sheet->setCellValue('B'.$baris,$q->department);
            $sheet->setCellValue('C'.$baris,$q->admission_name);
            $sheet->setCellValue('D'.$baris,$q->getProspectGroup->group);
            $sheet->setCellValue('E'.$baris,$q->registration_no);
            $sheet->setCellValue('F'.$baris,$q->name);
            $sheet->setCellValue('G'.$baris,$q->surname);
            $sheet->setCellValue('H'.$baris,$this->getGender()[$q->gender]);
            $sheet->setCellValue('I'.$baris,$q->year_entry);
            $sheet->setCellValue('J'.$baris,$q->pob .', '. $q->dob->format('d/m/Y'));
            $sheet->setCellValue('K'.$baris,$q->address);
            $sheet->setCellValue('L'.$baris,$q->postal_code);
            $sheet->setCellValue('M'.$baris,$q->distance.'KM');
            $sheet->setCellValue('N'.$baris,$q->phone);
            $sheet->setCellValue('O'.$baris,$q->mobile);
            $sheet->setCellValue('P'.$baris,$q->email); 
            $sheet->setCellValue('Q'.$baris,$this->getReferences('hr_student_status')[$q->student_status]); 
            $sheet->setCellValue('R'.$baris,$this->getReferences('hr_economic')[$q->economic]); 
            $sheet->setCellValue('S'.$baris,$q->medical); 
            $sheet->setCellValue('T'.$baris,$this->getReferences('hr_tribe')[$q->tribe]); 
            $sheet->setCellValue('U'.$baris,$this->getCitizen()[$q->citizen]); 
            $sheet->setCellValue('V'.$baris,$q->weight.'KG'); 
            $sheet->setCellValue('W'.$baris,$q->height.'CM'); 
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
            $sheet->setCellValue('AX'.$baris,'Rp'.number_format($q->donation_1,2));
            $sheet->setCellValue('AY'.$baris,'Rp'.number_format($q->donation_2,2));
            $sheet->setCellValue('AZ'.$baris,$q->exam_01);
            $sheet->setCellValue('BA'.$baris,$q->exam_02);
            $sheet->setCellValue('BB'.$baris,$q->exam_03);
            $sheet->setCellValue('BC'.$baris,$q->exam_04);
            $sheet->setCellValue('BD'.$baris,$q->exam_05);
            $sheet->setCellValue('BE'.$baris,$q->exam_06);
            $sheet->setCellValue('BF'.$baris,$q->exam_07);
            $sheet->setCellValue('BG'.$baris,$q->exam_08);
            $sheet->setCellValue('BH'.$baris,$q->exam_09);
            $sheet->setCellValue('BI'.$baris,$q->exam_10);
            $sheet->setCellValue('BJ'.$baris,$q->is_active);
            //
            if (count($add_columns) > 0)
            {
                for ($i = 0; $i < count($add_columns); $i++)
                {
                    $query_col = ColumnProspectStudent::where('prospect_student_id', $q->id)->where('column_id',$add_columns[$i][0])->first();
                    if (isset($query_col))
                    {
                        $sheet->setCellValueByColumnAndRow($i + 63, $baris, $query_col->type == 2 ? Str::upper(optional(optional($query_col)->getColumnOption)->name) : Str::upper($query_col->values));
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
        $sheet->getStyle('J6:J'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('K6:K'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('L6:L'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('M6:M'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('N6:N'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('O6:O'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('P6:P'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('Q6:Q'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('R6:R'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('S6:S'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('T6:T'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
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
        $sheet->getStyle('AE6:AE'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
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
        $sheet->getStyle('AT6:AT'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('AU6:AU'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
        $sheet->getStyle('AV6:AV'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('AW6:AW'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('AX6:AX'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
        $sheet->getStyle('AY6:AY'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyRight']);
        $sheet->getStyle('AZ6:AZ'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('BA6:BA'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('BB6:BB'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('BC6:BC'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('BD6:BD'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('BE6:BE'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('BF6:BF'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('BG6:BG'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('BH6:BH'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('BI6:BI'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('BJ6:BJ'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        if (count($add_columns) > 0)
        {
            $start_col = $sheet->getColumnDimensionByColumn(63)->getColumnIndex();
            $index_col = $sheet->getColumnDimensionByColumn($total_col + 62)->getColumnIndex();
            $row = 6;
            $range = $start_col.$row.':'.$index_col;
            $sheet->getStyle($range.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        }
        //
        $filename = date('Ymdhis') . '_' . Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() .'.xlsx';
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
        $query['models'] = $this->prospectEloquent->showIn($idArray);
        //
        $view = View::make('academic::pages.admissions.prospective_student_pdf', $query);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake();
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfLandscape($hashfile, $filename);
        echo $filename;
    }

    /**
     * Display a listing of data for combogrid.
     * @return JSON
     */
    public function comboGrid(Request $request)
    {
        return response()->json($this->prospectEloquent->combogrid($request));
    }
}
