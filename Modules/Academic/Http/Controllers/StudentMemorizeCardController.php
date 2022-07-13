<?php

namespace Modules\Academic\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\PdfTrait;
use App\Http\Traits\ReferenceTrait;
use App\Http\Traits\DepartmentTrait;
use Modules\Academic\Entities\MemorizeCard;
use Modules\Academic\Http\Requests\MemorizeCardRequest;
use Modules\Academic\Repositories\Student\MemorizeCardEloquent;
use Carbon\Carbon;
use View;
use Exception;

class StudentMemorizeCardController extends Controller
{

    use HelperTrait;
    use PdfTrait;
    use ReferenceTrait;
    use DepartmentTrait;

    private $subject = 'Kartu Setoran Santri';

    function __construct(MemorizeCardEloquent $memorizeCardEloquent)
    {
        $this->memorizeCardEloquent = $memorizeCardEloquent;
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
        $data['surahs'] = DB::table('quran_surahs')->orderBy('id')->get()->map(function($model){
            $model->surah = $model->id .' - '. $model->surah . ' (' . $model->total .' ayat)';
            return $model; 
        });
        $data['departments'] = $this->listDepartment();
        return view('academic::pages.students.memorize_card', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(MemorizeCardRequest $request)
    {
        $validated = $request->validated();
        try 
        {
            $request->merge([
                'memorize_date' => $this->formatDate($request->memorize_date,'sys'),
                'logged' => auth()->user()->email,
            ]);

            // validate max verse
            $students = [];
            
            for ($i=0; $i < count($request->students); $i++) 
            {
                if (
                    $request->students[$i]['from_surah'] != null &&
                    $request->students[$i]['from_verse'] != null &&
                    $request->students[$i]['to_surah'] != null &&
                    $request->students[$i]['to_verse'] != null
                )
                {
                    $from_surah = $this->getSurah($request->students[$i]['from_surah']);
                    if ($request->students[$i]['from_verse'] > $from_surah->total)
                    {
                        throw new Exception('Isian jumlah ayat di kolom Dari Ayat: ('.$request->students[$i]['from_verse'].') melebihi jumlah ayat di Surat ' . $from_surah->surah . ' (' . $from_surah->total . ' ayat)', 1);
                    }

                    $to_surah = $this->getSurah($request->students[$i]['to_surah']);
                    if ($request->students[$i]['to_verse'] > $to_surah->total)
                    {
                        throw new Exception('Isian jumlah ayat di kolom Sampai Ayat: ('.$request->students[$i]['to_verse'].') melebihi jumlah ayat di Surat ' . $to_surah->surah . ' (' . $to_surah->total . ' ayat)', 1);
                    }

                    $students[] = array(
                        'id' => $request->students[$i]['id'],
                        'student_id' => $request->students[$i]['student_id'],
                        'from_surah' => $request->students[$i]['from_surah'],
                        'from_verse' => $request->students[$i]['from_verse'],
                        'to_surah' => $request->students[$i]['to_surah'],
                        'to_verse' => $request->students[$i]['to_verse'],
                        'status' => $request->students[$i]['status'],
                    );      
                } 
            }

            for ($i=0; $i < count($students); $i++) 
            { 
                $request->merge([
                    'id' => $students[$i]['id'],
                    'student_id' => $students[$i]['student_id'],
                    'from_surah_id' => $students[$i]['from_surah'],
                    'to_surah_id' => $students[$i]['to_surah'],
                    'from_verse' => $students[$i]['from_verse'],
                    'to_verse' => $students[$i]['to_verse'],
                    'status' => $students[$i]['status'],
                ]);

                if ($request->id < 1)
                {
                    $this->memorizeCardEloquent->create($request, $this->subject);
                } else {
                    $this->memorizeCardEloquent->update($request, $this->subject);
                }
            }
            $response = $this->getResponse('store', '', $this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), '');
        }
        return response()->json($response);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($class_id, $date)
    {
        return response()->json(MemorizeCard::where('class_id', $class_id)->where('memorize_date', $date)->get()->map(function($model){
            $model['student_no'] = $model->getStudent->student_no;
            $model['name'] = $model->getStudent->name;
            $model['department'] = $model->getClass->getGrade->getDepartment->name;
            $model['school_year'] = $model->getClass->getSchoolYear->school_year;
            $model['grade'] = $model->getClass->getGrade->grade;
            $model['semester'] = $model->getClass->getGrade->getSemesterByDept->semester;
            return $model;
        })[0]);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function data(Request $request)
    {
        return response()->json($this->memorizeCardEloquent->data($request));
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function dataCard(Request $request)
    {
        return response()->json($this->memorizeCardEloquent->dataCard($request));
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Request $request)
    {
        try 
        {
            for ($i=0; $i < count($request->students); $i++) 
            { 
                if ($request->students[$i]['id'] > 0)
                {
                    $this->memorizeCardEloquent->destroy($request->students[$i]['id'], $this->subject);
                }
            }
            $response = $this->getResponse('destroy','',$this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), $this->subject);
        }
        return response()->json($response);   
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function print(Request $request)
    {
        $data['requests'] = json_decode($request->data);            
        $data['profile'] = $this->getInstituteProfile();
        //
        $view = View::make('academic::pages.students.memorize_card_pdf', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake();
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortraits($hashfile, $filename);
        echo $filename;
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function printForm(Request $request)
    {
        $payload = json_decode($request->data);
        $data['profile'] = $this->getInstituteProfile();
        //
        $view = View::make('academic::pages.students.memorize_card_form_pdf', $data);
        $name = Str::lower(config('app.name')) .'_form_'. Str::of($this->subject)->snake();
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortrait($hashfile, $filename);
        echo $filename;
    }

}
