<?php

namespace App\Http\Controllers;

use App\Models\TRXType;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    public function getTypeStock(){
        $data = TRXType::where('fc_trx', 'GOODS_TYPE')->get();
        return response()->json(['status' => 200, 'data' => $data]);
    }

    public function getFormStock () {
        $data = TRXType::where('fc_trx','GOODS_FORM')->get();
        return response()->json(['status' => 200, 'data' => $data]);
    }

    public function getNamePack() {
        $data = TRXType::where('fc_trx','PACKAGING')->get();
        return response()->json(['status' => 200, 'data' => $data]);
    }
}
