<?php

namespace App\Http\Controllers;

use App\Models\SOMST;
use Illuminate\Http\Request;

class SalesOrderController extends Controller
{
    public function getAllSOMST () {
        $data = SOMST::with(['sodtl', 'customer', 'sodtl.stock'])
        ->where('fc_status', '!=', 'FINISH')
        ->where('fc_status', '!=', 'LOCK')
        ->where('fc_status', '!=', 'REQUEST')->get();
        return response()->json(['status' => 200, 'data' => $data]);
    }
}
