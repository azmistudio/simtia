<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use App\Models\Department;

trait DepartmentTrait 
{

    public function countDepartment() 
    {
        return Department::where('is_all',0)->count();
    }

	public function listDepartment() 
	{
        $count = Department::where('is_all',0)->count();
        $query = Department::where('is_active', 1);
        if ($count > 0)
        {
            $query = $query->where('is_all',0);
        } 
        return $query->orderBy('id')->get();
	}

    public function allDepartment()
    {
        return Department::where('is_active',1)->orderBy('id')->get()->map(function ($model) {
            $model['name'] = $model->is_all == 1 ? $model->name .' (SEMUA)' : $model->name;
            return $model;
        });
    }

}