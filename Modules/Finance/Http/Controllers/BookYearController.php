<?php

namespace Modules\Finance\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\PdfTrait;
use App\Http\Traits\ReferenceTrait;
use Modules\Academic\Entities\SchoolYear;
use Modules\Finance\Entities\BookYear;
use Modules\Finance\Entities\Code;
use Modules\Finance\Entities\BeginBalance;
use Modules\Finance\Http\Requests\BookYearCloseRequest;
use Modules\Finance\Repositories\Reference\BookYearEloquent;
use Modules\Finance\Repositories\Journal\JournalEloquent;
use Carbon\Carbon;
use View;
use Exception;

class BookYearController extends Controller
{
    use HelperTrait;
    use PdfTrait;
    use ReferenceTrait;

    private $subject = 'Data Tahun Buku';
    private $subject_close = 'Data Tutup Buku';

    function __construct(BookYearEloquent $bookYearEloquent, JournalEloquent $journalEloquent)
    {
        $this->bookYearEloquent = $bookYearEloquent;
        $this->journalEloquent = $journalEloquent;
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
        $data['departments'] = SchoolYear::select('id','department_id','school_year','start_date')->where('is_active', 1)->get();
        return view('finance::pages.references.book_year', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_year' => 'required',
            'start_date' => 'required',
            'prefix' => 'required',
        ]);
        try 
        {
            $request->merge([
                'number' => 0,
                'prefix' => Str::lower($request->prefix),
                'end_date' => date("Y-m-t", strtotime('+11 months', strtotime($this->formatDate($request->start_date,'sys')))),
                'is_active' => $request->is_active ?: 1,
                'logged' => auth()->user()->email,
            ]);
            $dates = explode('/', $request->start_date);
            if ($request->book_year != $dates[2])
            {
                $response = $this->getResponse('warning', 'Tanggal Mulai tidak dalam periode Tahun Buku.');
            } else {
                $isActive = BookYear::where('is_active', 1)->count();
                if ($request->id < 1) 
                {
                    if ($isActive > 0 && $request->is_active == 1)
                    {
                        $response = $this->getResponse('warning', 'Sudah ada Tahun Buku yang dibuat, gunakan menu Tutup Buku untuk membuat Tahun Buku baru.');
                    } else {
                        $this->bookYearEloquent->create($request, $this->subject);
                        $response = $this->getResponse('store', '', $this->subject);
                    }
                } else {
                    $this->bookYearEloquent->update($request, $this->subject);
                    $response = $this->getResponse('store', '', $this->subject);
                }
            }
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Tahun Buku');
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
        return response()->json(BookYear::find($id));
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function active()
    {
        return response()->json(BookYear::where('is_active',1)->first());
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function period($id)
    {
        $bookyear = BookYear::find($id);
        $date = new \DateTime($bookyear->start_date);
        return response()->json($date->modify('-1 month')->format('t/m/Y'));
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return Renderable
     */
    public function data(Request $request)
    {
        return response()->json($this->bookYearEloquent->data($request));
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
            $this->bookYearEloquent->destroy($id, $this->subject);
            $response = $this->getResponse('destroy', '', $this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), $this->subject);
        }
        return response()->json($response);
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdf(Request $request)
    {
        $idArray = collect(json_decode($request->data))->pluck('id')->toArray();
        $data['bookyears'] = BookYear::whereIn('id', $idArray)->orderBy('id')->get();
        // 
        $view = View::make('finance::pages.references.book_year_pdf', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake();
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortrait($hashfile, $filename);
        echo $filename;
    }

    /**
     * Display a listing of data.
     * @param Request $request
     * @return Renderable
     */
    public function combogrid(Request $request)
    {
        return response()->json($this->bookYearEloquent->combogrid($request));
    }

    // close period

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexClose(Request $request)
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
        $data['bookyear'] = BookYear::where('is_active',1)->first();
        $data['accounts'] = Code::where('category_id', 3)->where('code','3-103')->get();
        if (!empty($data['bookyear']->id))
        {
            return view('finance::pages.references.book_close', $data);
        } else {
            $data['error'] = 'Belum ada <b>Tahun Buku</b> yang dibuat, gunakan menu<br/> Data Master &#8594; Tahun Buku untuk membuat baru.';
            return view('errors.400', $data);
        }
    }

    /**
     * Save resources to database.
     * @param Request $request
     * @return Response
     */
    public function storeClose(BookYearCloseRequest $request)
    {
        $validated = $request->validated();
        try 
        {
            $bookyear = BookYear::where('is_active',1)->first();
            // validate close date
            if ($this->formatDate($request->close_date,'sys') < $bookyear->end_date)
            {
                throw new Exception('Tanggal Tutup Buku tidak boleh lebih kecil dari tanggal akhir Periode tahun buku berjalan.', 1);
            }
            // validate prefix
            if ($request->prefix <= $bookyear->prefix)
            {
                throw new Exception('Awalan kuitansi tidak boleh sama atau lebih kecil dari tahun buku berjalan.', 1);
            }
            // validate new date
            if ($this->formatDate($request->start_date,'sys') <= $bookyear->end_date)
            {
                throw new Exception('Tanggal Mulai Buku tidak boleh lebih kecil atau sama dengan tanggal akhir Periode tahun buku berjalan.', 1);
            }

            // check balance activa and passiva
            $balance = $this->journalEloquent->checkBalance($bookyear->id, $this->formatDate($bookyear->start_date,'local'), $request->close_date);
            if (!$balance)
            {
                throw new Exception('Laporan neraca tidak seimbang! Anda perlu memeriksa kembali data-data transaksi agar laporan neraca menjadi seimbang.', 1);
            } else {
                $n_activa = 0;
                $n_pasiva = 0;
                $end_date_new = date("Y-m-t", strtotime('+11 months', strtotime($this->formatDate($request->start_date,'sys'))));

                // get activa
                $activas = $this->journalEloquent->getActivaPasiva('activa', $bookyear->id, $this->formatDate($bookyear->start_date,'local'), $request->close_date);
                if (!empty($activas))
                {
                    foreach ($activas as $activ) 
                    {
                        $activa[$n_activa]['account_id'] = $activ->account_id;
                        $activa[$n_activa]['code'] = $activ->code;
                        $activa[$n_activa]['total'] = $activ->total;
                        $n_activa++;
                    }
                }

                // get pasiva
                $pasivas = $this->journalEloquent->getActivaPasiva('pasiva', $bookyear->id, $this->formatDate($bookyear->start_date,'local'), $request->close_date);
                if (!empty($pasivas))
                {
                    foreach ($pasivas as $pasiv) 
                    {
                        $pasiva[$n_pasiva]['account_id'] = $pasiv->account_id;
                        $pasiva[$n_pasiva]['code'] = $pasiv->code;
                        $pasiva[$n_pasiva]['total'] = $pasiv->total;
                        $n_pasiva++;
                    }
                }

                // transaction
                DB::beginTransaction();

                // update bookyear active
                BookYear::where('id', $bookyear->id)->update(['is_active' => 0]);

                // create new bookyear
                $request->merge([
                    'book_year' => $request->book_year,
                    'number' => 0,
                    'prefix' => Str::lower($request->prefix),
                    'end_date' => $end_date_new,
                    'remark' => $request->remark,
                    'is_active' => 1,
                    'logged' => auth()->user()->email,
                ]);
                $this->bookYearEloquent->create($request, $this->subject);

                // set variables
                $uuid = strtotime('now');
                $new_bookyear = BookYear::where('is_active',1)->first();
                $number = 1;
                $cash_no = sprintf('%06d', $number);

                // save begin balances
                $balances = BeginBalance::where('bookyear_id', $bookyear->id)->get();
                foreach ($balances as $balance) 
                {
                    BeginBalance::upsert([
                        'bookyear_id' => $new_bookyear->id,
                        'trans_date' => $request->close_date,
                        'account_id' => $balance->account_id,
                        'total' => $balance->total,
                        'pos' => $balance->pos,
                        'logged' => auth()->user()->email,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ],['bookyear_id','trans_date','account_id']);
                }

                // store to journal
                $journal = $this->journalEloquent->store($request->start_date, 'Saldo awal tahun buku '. $request->book_year, $cash_no, $new_bookyear->id, 'begin_balance', 1);
                // save activa
                if (isset($activa))
                {
                    for ($i=0; $i < count($activa); $i++) 
                    { 
                        $account_id = $activa[$i]['account_id'];
                        $code = $activa[$i]['code'];
                        $total = $activa[$i]['total'];
                        // store to journal detail
                        if ($total > 0)
                        {
                            $this->journalEloquent->createDetail($journal->id, $account_id, $total, 0, $uuid);
                        } else {
                            $this->journalEloquent->createDetail($journal->id, $account_id, 0, $total, $uuid);
                        }

                        // update begin balances
                        BeginBalance::where('account_id', $account_id)
                            ->where('bookyear_id', $new_bookyear->id)
                            ->where('trans_date', $request->close_date)
                            ->update([
                                'total' => $total
                            ]);
                    }
                }

                // save pasiva
                if (isset($pasiva))
                {
                    for ($i=0; $i < count($pasiva); $i++) 
                    { 
                        $account_id = $pasiva[$i]['account_id'];
                        $code = $pasiva[$i]['code'];
                        $total = $pasiva[$i]['total'];
                        // store to journal detail
                        if ($total > 0)
                        {
                            $this->journalEloquent->createDetail($journal->id, $account_id, 0, $total, $uuid);
                        } else {
                            $this->journalEloquent->createDetail($journal->id, $account_id, $total, 0, $uuid);
                        }

                        // update begin balances
                        BeginBalance::where('account_id', $account_id)
                            ->where('bookyear_id', $new_bookyear->id)
                            ->where('trans_date', $request->close_date)
                            ->update([
                                'total' => $total
                            ]);
                    }
                }
                
                // save retained earning
                $retained_earning = $this->journalEloquent->getRetainedEarning($bookyear->id, $this->formatDate($bookyear->start_date,'local'), $request->close_date);
                if ($retained_earning > 0)
                {
                    $this->journalEloquent->createDetail($journal->id, $request->re_account, 0, $retained_earning, $uuid);
                } else {
                    $this->journalEloquent->createDetail($journal->id, $request->re_account, $retained_earning, 0, $uuid);
                }

                // update begin balances
                BeginBalance::where('account_id', $request->re_account)
                    ->where('bookyear_id', $new_bookyear->id)
                    ->where('trans_date', $request->close_date)
                    ->update([
                        'total' => $retained_earning
                    ]);

                // update bookyear number
                BookYear::where('id', $new_bookyear->id)->increment('number');

                DB::commit();
                $response = $this->getResponse('store', '', $this->subject_close);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            $response = $this->getResponse('error', $e->getMessage(), 'Tutup Buku');
        }
        return response()->json($response);
    }
}
