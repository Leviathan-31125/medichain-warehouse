<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function getAllBrands () {
        $data = Brand::with('stock')->get();

        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function createBrands (Request $request) {
        $brand = Brand::where([
            'fv_brandname' => $request->fv_brandname, 
            'fv_group' => $request->fv_group
        ])->limit(1)->get();

        if ($brand->count() > 0)
            return response()->json(['status' => 400, 'message' => 'Duplikasi data! Maaf data telah ada di system']);

        $data = Brand::create([
            'fv_brandname' => $request->fv_brandname,
            'fv_group' => $request->fv_group,
            'ft_image' => $request->ft_image,
            'ft_description' => $request->ft_description
        ]);

        if ($data) 
            return response()->json(['status' => 201, 'data' => $data]);
        else {
            return response()->json([
                'status' => 400,
                'message' =>  'Hayoo kenapa?'
            ]);
        }
    }

    public function updateBrand (Request $request, $fc_brandcode) {
        $brandcode_decoded = base64_decode($fc_brandcode);
        
        $brand = Brand::find($brandcode_decoded);

        if (!$brand) 
            return response()->json(['status' => '400', 'message' => 'Gagal update! Brand tidak ditemukan']);

        $updated = $brand->update($request->all());
        if ($updated) 
            return response()->json(['status' => 201, 'message' => 'Data berhasil diupdate']);
        else
            return response()->json(['status' => '400', 'message' => 'Gagal update!']);
    }

    public function getDetailBrand ($fc_brandcode) {
        $brandcode_decoded = base64_decode($fc_brandcode);

        $brand = Brand::find($brandcode_decoded);
        
        if($brand) 
            return response()->json(['status' => '200', 'data' => $brand]);
        else 
            return response()->json(['status' => '400', 'message' => 'Not Found! Data tidak ditemukan']);
    }

    public function deleteBrand ($fc_brandcode) {
        $brandcode_decoded = base64_decode($fc_brandcode);

        $brand = Brand::where('fc_brandcode', $brandcode_decoded)->delete();

        if($brand) 
            return response()->json(['status' => '200', 'message' => 'Brand berhasil dihapus']);
        else 
            return response()->json(['status' => '400', 'message' => 'Gagal hapus! Data gagal dihapus atau tidak ditemukan']);
    }
}