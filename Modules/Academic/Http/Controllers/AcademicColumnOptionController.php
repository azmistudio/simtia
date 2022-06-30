<?php

namespace Modules\Academic\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\ReferenceTrait;
use App\Http\Traits\DepartmentTrait;
use Modules\Academic\Entities\ColumnOption;
use Modules\Academic\Repositories\Academic\ColumnOptionEloquent;
use View;
use Exception;

class AcademicColumnOptionController extends Controller
{
    use HelperTrait;
    use ReferenceTrait;
    use DepartmentTrait;

    private $subject = 'Data Pilihan Tambahan Kolom';

    function __construct(ColumnOptionEloquent $columnOptionEloquent)
    {
        $this->columnOptionEloquent = $columnOptionEloquent;
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function store(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'order' => 'required|int',
        ]);
        try {
            $request->merge([
                'column_id' => $id,
                'name' => Str::lower($request->name),
                'is_active' => $request->is_active == "Tidak" ? 1 : 2,
            ]);
            if (isset($request->isNewRecord))
            {
                $checkExist = ColumnOption::where('column_id', $id)->where('order', $request->order)->count();
                if ($checkExist > 0)
                {
                    throw new Exception('Kombinasi Nama Kolom dan Urutan sudah digunakan.', 1);
                } else {
                    $this->columnOptionEloquent->create($request, $this->subject);
                    $response = $this->getResponse('store', '', $this->subject);
                }
            } else {
                $this->columnOptionEloquent->update($request, $this->subject);
                $response = $this->getResponse('store', '', $this->subject);
            }
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Nama Kolom');
        }
        return response()->json($response);
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
            $this->columnOptionEloquent->destroy($request, $this->subject);
            $response = $this->getResponse('destroy', '', $this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Nama Kolom');
        }
        return response()->json($response);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function data(Request $request, $id)
    {
        return response()->json($this->columnOptionEloquent->data($request, $id));
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function dataList($id)
    {
        return response()->json($this->columnOptionEloquent->datalist($id));
    }
}
