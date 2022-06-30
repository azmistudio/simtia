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
use App\Http\Traits\ReferenceTrait;
use App\Http\Traits\AuditLogTrait;
use App\Models\Room;
use App\Repositories\General\RoomEloquent;
use Modules\Academic\Entities\Students;
use Modules\Academic\Repositories\Student\RoomPlacementEloquent;
use Carbon\Carbon;
use View;
use Exception;

class RoomPlacementController extends Controller
{

    use AuditLogTrait;
    use HelperTrait;
    use ReferenceTrait;

    private $subject = 'Pembagian Kamar Santri';

    function __construct(RoomPlacementEloquent $roomPlacementEloquent)
    {
        $this->roomPlacementEloquent = $roomPlacementEloquent;
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
        return view('academic::pages.admissions.room_placement', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|int',
            'students' => 'required|array',
        ]);
        try 
        {
            // check quota
            $room = Room::find($request->room_id);
            $room_occupied = $room->getOccupied($request->room_id);
            $remaining = $room->capacity - $room_occupied;
            if ($room->capacity == $room_occupied)
            {
                throw new Exception('Kuota Kamar sudah terpenuhi, silahkan pilih Kamar lainnya.', 1);
            }

            // check remaining
            if (count($request->students) > $remaining)
            {
                throw new Exception('Jumlah Santri ('.count($request->students).') melebihi batas Kuota Kamar tersisa (' .$remaining.')', 1);
            }

            for ($i=0; $i < count($request->students); $i++) 
            {
                $request->merge([
                    'student_id' => $request->students[$i]['id'],
                ]);
                $this->roomPlacementEloquent->create($request, $this->subject);
                DB::table('academic.student_room_histories')->insert([
                    'student_id' => $request->students[$i]['id'],
                    'room_id' => $request->room_id,
                    'start_date' => date('Y-m-d'),
                    'logged' => auth()->user()->email,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
            $response = $this->getResponse('store', '', $this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), '');
        }
        return response()->json($response);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function data(Request $request)
    {
        return response()->json($this->roomPlacementEloquent->data($request));
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
            DB::transaction(function () use ($request) {
                for ($i=0; $i < count($request->data); $i++) 
                { 
                    DB::table('academic.room_placements')->where('id', $request->data[$i]['id'])->delete();
                }
            });
            $response = $this->getResponse('destroy','',$this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), $this->subject);
        }
        return response()->json($response);   
    }
}
