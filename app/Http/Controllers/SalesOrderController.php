<?php

namespace App\Http\Controllers;

use App\Models\SOMST;
use Illuminate\Http\Request;

class SalesOrderController extends Controller
{
    public function getAllSOMST () {
        $data = SOMST::with(['sodtl', 'customer'])
        ->where('fc_status', '!=', 'FINISH')
        ->where('fc_status', '!=', 'LOCK')->get();
        return response()->json(['status' => 200, 'data' => $data]);
    }
}
