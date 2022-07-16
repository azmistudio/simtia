<?php

namespace Modules\HR\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Reference;
use App\Models\User;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\ReferenceTrait;
use App\Http\Traits\PdfTrait;
use Modules\HR\Http\Requests\EmployeeRequest;
use Modules\HR\Entities\Employee;
use Modules\HR\Repositories\HR\HREloquent;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;
use Exception;
use View;

class HRController extends Controller
{

    use HelperTrait;
    use ReferenceTrait;
    use PdfTrait;

    private $subject = 'Data Pegawai';

    function __construct(HREloquent $HREloquent)
    {
        $this->HREloquent = $HREloquent;
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
        // request
        $window = explode(".", $request->w);
        $data['InnerHeight'] = $window[0];
        $data['InnerWidth'] = $window[1];
        $data['ViewType'] = $request->t;
        // data
        $data['sections'] = Reference::where('category', 'hr_section')->get();
        $data['tribes'] = Reference::where('category', 'hr_tribe')->get();
        return view('hr::pages.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JSON
     */
    public function store(EmployeeRequest $request)
    {
        $validated = $request->validated();
        if ($request->file('photo')) 
        {
            $imagePath = $request->file('photo');
            $imageName = date('YmdHis') . '-' . $this->filter_filename($imagePath->getClientOriginalName());
            $path = $request->file('photo')->storeAs('uploads/employee', $imageName, 'public');
        }
        try 
        {
            $is_update_email = false;
            $request->merge([
                'email' => Str::lower($request->email),
                'name' => Str::lower($request->name),
                'pob' => Str::lower($request->pob),
                'dob' => $this->formatDate($request->dob,'sys'),
                'work_start' => $this->formatDate($request->work_start,'sys'),
                'is_active' => $request->has('is_active') ? $request->is_active : 1,
                'logged' => auth()->user()->email,
            ]);
            if ($request->id < 1) 
            {
                $request->merge([
                    'photo' => isset($imageName) ? $imageName : '',
                ]);
                $this->HREloquent->create($request, $this->subject);
            } else {
                $employee = Employee::find($request->id);
                if (isset($imageName)) 
                {
                    $request->merge([
                        'photo' => $imageName
                    ]);
                }
                $this->HREloquent->update($request, $this->subject);
                // update user email on table users
                if ($employee->email != $request->email)
                {
                    $user = User::where('email', $employee->email)->first();
                    if (!empty($user))
                    {
                        $user->email = $request->email;
                        $user->updated_at = Carbon::now();
                        $user->save();
                        //
                        if (auth()->user()->email == $employee->email)
                        {
                            $is_update_email = true;
                        }
                    }
                }
            }
            $response = $this->getResponse('store', '', $this->subject, $is_update_email);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'NIP');
        }
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(Employee::find($id));
    }

    /**
     * Remove the specified resource from storage.
     * @param  int  $id
     * @return JSON
     */
    public function destroy($id)
    {
        try 
        {
            $this->HREloquent->destroy($id);
            $response = $this->getResponse('destroy', $this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage());
        }
        return response()->json($response);
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function print(Request $request)
    {
        $payload = json_decode($request->data);
        $data['rows'] = Employee::find($payload->id);
        $data['profile'] = $this->getInstituteProfile();
        //
        $view = View::make('hr::pages.employee_detail_pdf', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake() . '_' . $data['rows']->employee_id;
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortrait($hashfile, $filename);
        echo $filename;
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function data(Request $request)
    {
        return response()->json($this->HREloquent->data($request));
    }

    /**
     * Display a listing of data for combogrid.
     * @return JSON
     */
    public function comboGrid(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        return response()->json($this->HREloquent->combogrid($request));
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
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(20);
        $sheet->getColumnDimension('K')->setWidth(20);
        $sheet->getColumnDimension('L')->setWidth(35);
        $sheet->getColumnDimension('M')->setWidth(20);
        $sheet->getColumnDimension('N')->setWidth(50);
        $sheet->getColumnDimension('O')->setWidth(10);
        $sheet->getColumnDimension('P')->setWidth(50);
        //
        $sheet->setCellValue('A1', Str::upper($request->session()->get('institute')));
        $sheet->setCellValue('A2', 'DATA SUMBER DAYA MANUSIA - ' . config('app.name'));
        $sheet->setCellValue('A4', 'NO.');
        $sheet->setCellValue('B4', 'BAGIAN');
        $sheet->setCellValue('C4', 'NIP');
        $sheet->setCellValue('D4', 'NAMA');
        $sheet->setCellValue('E4', 'TEMPAT & TANGGAL LAHIR');
        $sheet->setCellValue('F4', 'JENIS KELAMIN');
        $sheet->setCellValue('G4', 'MENIKAH');
        $sheet->setCellValue('H4', 'SUKU');
        $sheet->setCellValue('I4', 'NO. IDENTITAS');
        $sheet->setCellValue('J4', 'NO. TELPON');
        $sheet->setCellValue('K4', 'NO. HANDPHONE');
        $sheet->setCellValue('L4', 'EMAIL');
        $sheet->setCellValue('M4', 'TANGGAL BEKERJA');
        $sheet->setCellValue('N4', 'ALAMAT');
        $sheet->setCellValue('O4', 'AKTIF');
        $sheet->setCellValue('P4', 'KETERANGAN');
        //
        $baris = 5;
        $number = 1;
        $idArray = collect(json_decode($request->data))->pluck('id')->toArray();
        $query = Employee::whereIn('id', $idArray)->orderBy('id')->get();
        foreach ($query as $q) 
        {
            $sheet->setCellValue('A'.$baris,$number);
            $sheet->setCellValue('B'.$baris,$q->getSection->name);
            $sheet->setCellValue('C'.$baris,$q->employee_id);
            $sheet->setCellValue('D'.$baris,$q->name);
            $sheet->setCellValue('E'.$baris,$q->pob .', '. $q->dob->format('d/m/Y'));
            $sheet->setCellValue('F'.$baris,$this->getGender()[$q->gender]);
            $sheet->setCellValue('G'.$baris,$this->getMarital()[$q->marital]);
            $sheet->setCellValue('H'.$baris,$q->getTribe->name);
            $sheet->setCellValue('I'.$baris,$q->national_id);
            $sheet->setCellValue('J'.$baris,$q->phone);
            $sheet->setCellValue('K'.$baris,$q->mobile);
            $sheet->setCellValue('L'.$baris,$q->email);
            $sheet->setCellValue('M'.$baris,$q->work_start->format('d/m/Y'));
            $sheet->setCellValue('N'.$baris,$q->address);
            $sheet->setCellValue('O'.$baris,$this->getActive()[$q->is_active]);
            $sheet->setCellValue('P'.$baris,$q->remark);
            $baris++;
            $number++;
        }
        //
        $sheet->getStyle('A1:A2')->applyFromArray($this->PHPExcelCommonStyle()['title']);
        $sheet->getStyle('A4:P4')->applyFromArray($this->PHPExcelCommonStyle()['header']);
        $sheet->getStyle('A5:A'.$baris = $baris - 1)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('B5:B'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('C5:C'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('D5:D'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('E5:E'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('F5:F'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('G5:G'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('H5:H'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('I5:I'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('J5:J'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('K5:K'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('L5:L'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('M5:M'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('N5:N'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $sheet->getStyle('N5:N'.$baris)->getAlignment()->setWrapText(true);
        $sheet->getStyle('O5:O'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('P5:P'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
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
        $query['models'] = Employee::whereIn('id', $idArray)->orderBy('id')->get()->map(function ($model) {
            $model['gender'] = $this->getGender()[$model->gender];
            $model['marital'] = $this->getMarital()[$model->marital];
            $model['is_active'] = $this->getActive()[$model->is_active];
            return $model;
        });
        $view = View::make('hr::pages.employee_pdf', $query);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake();
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfLandscape($hashfile, $filename);
        echo $filename;
    }
    
}
