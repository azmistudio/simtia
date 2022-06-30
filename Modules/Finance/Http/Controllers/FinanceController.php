<?php

namespace Modules\Finance\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\ReferenceTrait;

class FinanceController extends Controller
{
    use HelperTrait;
    use ReferenceTrait;
}
