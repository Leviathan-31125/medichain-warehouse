<?php

namespace App\Http\Controllers;

use App\Models\InvStore;
use App\Models\Stock;
use Illuminate\Http\Request;

class InvStoreController extends Controller
{
    public function getAllInvStore () {
        $data = InvStore::get();
        return response()->json(['status' => 200, 'data' => $data]);
    }

    public function getDetailInvStore ($fc_barcode) {
        $barcode_decode = base64_decode($fc_barcode);

        $InvStore = InvStore::find($barcode_decode);
        if(!$InvStore)
            return response()->json(['status' => 400, 'message' => "Not Found! Persediaan tidak ditemukan di gudang"], 400);

        return response()->json(['status' => 200, 'data' => $InvStore]);
    }

    public function getInvStoreByWarehouse ($fc_warehousecode) {
        $warehousecode_decode = base64_decode($fc_warehousecode);
        $InvStore = InvStore::where('fc_warehousecode', $warehousecode_decode)
          ->where('fn_quantity', '>', 0)->get();
        
        return response()->json($InvStore);
    }

    public function getInvStoreByIntBarcode ($fc_barcode) {
        $intbarcode_decode = base64_decode($fc_barcode);
        $stock = Stock::find($intbarcode_decode);
        
        if(!$stock)
            return response()->json(['status' => 400, 'message' => "Not Found! Stock tidak tersedia pada system"], 400);

        $InvStore = InvStore::where('fc_barcode', 'like', $intbarcode_decode.'%')->orderBy('fd_expired' , 'asc')->get();
        return response()->json($InvStore);
    }
}
