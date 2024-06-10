<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function getAllStock() {
        $data = Stock::with('brand')->get();

        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function getDetailStock ($fc_barcode) {
        $barcode_decoded = base64_decode($fc_barcode);

        $stock = Stock::with('brand')->find($barcode_decoded);
        
        if($stock) 
            return response()->json(['status' => '200', 'data' => $stock]);
        else 
            return response()->json(['status' => '400', 'message' => 'Not Found! Data tidak ditemukan']);
    }

    public function createStock (Request $request) {
        $stock = Stock::where('fc_stockcode', $request->fc_stockcode)->first();
        
        
        // pengecekan stockcode untuk mencegah duplikasi stock 
        if ($stock) 
            return response()->json(['status' => 400, 'message' => 'Duplikasi data! Maaf data telah ada di system']);
        if (!$this->brand_check($request->fc_brandcode))
            return response()->json(['status' => 400, 'message' => 'Undefined data! Maaf brand belum terdaftar pada system']); 
        
        $created = Stock::create([
            'fc_stockcode' => $request->fc_stockcode,
            'fv_namestock' => $request->fv_namestock,
            'fv_namealias_stock' => $request->fv_namealias_stock,
            'fc_brandcode' => $request->fc_brandcode,
            'fv_group' => $request->fv_group,
            'fc_typestock' => $request->fc_typestock,
            'fc_formstock' => $request->fc_formstock,
            'fc_namepack' => $request->fc_namepack,
            'fn_minstock' => $request->fn_minstock,
            'fn_maxstock' => $request->fn_maxstock,
            'fm_purchase' => $request->fm_purchase,
            'ft_description' => $request->ft_description
        ]);

        if ($created) 
            return response()->json(['status' => '201', 'message' => 'Stock baru berhasil dibuat', 'data' => $created]);
        else 
            return response()->json(['status' => '400', 'message' => 'Create Failed! Gagal menambahkan stock']);
    }

    public function updateStock (Request $request, $fc_barcode) {
        $barcode_decoded = base64_decode($fc_barcode);
        $stock = Stock::where('fc_barcode', $barcode_decoded)->first();
        
        // pengecekan stok apakah ada di system atau tidak 
        if (!$stock)
            return response()->json(['status' => '400', 'message' => 'Not Found! Data tidak ditemukan']);

        // pengecekan brand sudah ter-register atau tidak
        if(!$this->brand_check($request->fc_brandcode)) 
            return response()->json(['status' => '400', 'message' => 'Brand undefined! Brand belum terdaftar pada system']);

        // update data berdasarkan request kecuali stockcode
        $updated = $stock->update($request->except(['fc_stockcode']));
        if ($updated)
            return response()->json(['status' => 201, 'message' => 'Data stock berhasil diupdate']);
        else
            return response()->json(['status' => '400', 'message' => 'Gagal update!']);
    }

    public function deleteStock ($fc_barcode) {
        $brandcode_decoded = base64_decode($fc_barcode);
        $brand = Stock::where('fc_barcode',$brandcode_decoded)->delete();

        if($brand) 
            return response()->json(['status' => '200', 'message' => 'Stock berhasil dihapus']);
        else 
            return response()->json(['status' => '400', 'message' => 'Gagal hapus! Data gagal dihapus atau tidak ditemukan']);
    }

    private function brand_check ($brand_code) {
        $validate = Brand::find($brand_code);

        if($validate) return true;
        return false;
    }
}
