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
use Modules\Academic\Entities\Columns;
use Modules\Academic\Entities\ColumnOption;
use Modules\Academic\Http\Requests\ColumnRequest;
use Modules\Academic\Repositories\Academic\ColumnEloquent;
use View;
use Exception;

class AcademicColumnController extends Controller
{
    use HelperTrait;
    use ReferenceTrait;
    use DepartmentTrait;

    private $subject = 'Data Tambahan Kolom';

    function __construct(ColumnEloquent $columnEloquent)
    {
        $this->columnEloquent = $columnEloquent;
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
        $data['depts'] = $this->listDepartment();
        return view('academic::pages.academics.academic_column', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(ColumnRequest $request)
    {
        $validated = $request->validated();
        try 
        {
            $request->merge([
                'name' => Str::lower($request->name),
                'is_active' => $request->is_active ?: 1,
            ]);
            if ($request->id < 1) 
            {
                $this->columnEloquent->create($request, $this->subject);
            } else {
                $this->columnEloquent->update($request, $this->subject);
            }
            $response = $this->getResponse('store', '', $this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Departemen dan Nama Kolom');
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
        return response()->json(Columns::find($id));
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
            $this->columnEloquent->destroy($id, $this->subject);
            $response = $this->getResponse('destroy', '', $this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Tambahan Kolom');
        }
        return response()->json($response);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function data(Request $request)
    {
        return response()->json($this->columnEloquent->data($request));
    }

    /**
     * Display view from data.
     * @return JSON
     */
    public function view($id)
    {
        $columns = Columns::select('id','name','type')->where('department_id', $id)->where('is_active', 1)->orderBy('department_id','asc')->orderBy('order','asc')->get();
        $column_opts = ColumnOption::select('id','column_id','name')->where('is_active', 1)->orderBy('column_id','asc')->orderBy('order','asc')->get();
        $html = '';
        foreach ($columns as $column)
        {
            if ($column->type == 1) 
            {
                $html .= '
                    <div class="mb-1">
                        <input type="hidden" name="columnopts[]" value="'.$column->id.'-1" />
                        <input name="additional[]" id="col'.$column->id.'" class="easyui-textbox tbox" style="width:500px;height:22px;" label="'.$column->name.'" />
                    </div>
                ';
            } else {
                $html .= '
                    <div class="mb-1">
                        <input type="hidden" name="columnopts[]" value="'.$column->id.'-2" />
                        <select name="additional[]" id="col'.$column->id.'" class="easyui-combobox cbox" style="width:500px;height:22px;" label="'.$column->name.'">
                            <option value="">---</option>';
                            foreach ($column_opts as $opt)
                            {
                                if ($opt->column_id == $column->id)
                                {
                                    $html .= '<option value="'.$opt->id.'">'.$opt->name.'</option>';
                                }
                            }
                $html .= '</select></div>';
            }
        }
        echo $html;
    }
}
