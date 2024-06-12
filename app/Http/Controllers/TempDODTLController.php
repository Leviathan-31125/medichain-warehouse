<?php

namespace App\Http\Controllers;

use App\Models\InvStore;
use App\Models\SODTL;
use App\Models\TempDoDMST;
use App\Models\TempDoDTL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TempDODTLController extends Controller
{
    public function getTempDODTLbyDONO ($fc_dono) {
        $dono_decoded = base64_decode($fc_dono);
        $TempDoMST = TempDoDMST::find($dono_decoded);

        if (!$TempDoMST)
            return response()->json(['status' => 400, 'message' => "Invalid Data! Delivery Order tidak ditemukan pada system"], 400);

        $TempDODTL = TempDoDTL::where('fc_dono', $dono_decoded)->with('invstore')->get();
        return response()->json(['status' => 200, 'data' => $TempDODTL]);
    }

    public function addTempDODTL (Request $request, $fc_dono) {
        $validator = Validator::make($request->all(), [
            'fc_barcode' => 'required',
            'fc_statusbonus' => 'required',
            'fn_qty' => 'required'
        ], [
            'fc_barcode.required' => 'Kode barang wajib dicantumkan',
            'fc_statusbonus.required' => 'Status bonus atau reguler tidak terdeteksi',
            'fn_qty.required' => 'Kuantitas wajib disertakan'
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => 300,
                'message' => $validator->errors()->first()
            ], 400);
        }

        $dono_decoded = base64_decode($fc_dono);
        $TempDoMST = TempDoDMST::find($dono_decoded);

        if (!$TempDoMST)
            return response()->json(['status' => 400, 'message' => "Invalid Data! Delivery Order tidak ditemukan pada system"], 400);

        // cek barang di gudang 
        $checkInvStore = InvStore::where([
            'fc_barcode' => $request->fc_barcode,
            'fc_warehousecode' => $TempDoMST->fc_warehousecode
        ])->first();
        
        if(!$checkInvStore) 
            return response()->json(['status' => 400, 'message' => 'Not Found! Stock tidak tersedia pada gudang'], 400);

        // validasi jumlah barang digudang
        if ($checkInvStore->fn_quantity < $request->fn_qty)
            return response()->json(['status' => 400, 'message' => 'Out Stock! Jumlah stock tidak mencukupi'], 400);

        // cek duplikasi data di dodtl
        $checkTempDoDtl = TempDoDTL::where([
            'fc_barcode' => $request->fc_barcode,
            'fc_statusbonus' => $request->fc_statusbonus,
            'fc_dono' => $dono_decoded
        ])->first();

        if($checkTempDoDtl)
            return response()->json(['status' => 400, 'message' => 'Duplicate Data! Stock sudah tersedia pada Delivery Order'], 400);

        // cek qty barang di sodtl
        $checkSODTL = SODTL::where([
            'fc_barcode' => substr($request->fc_barcode, 0, 30),
            'fc_sono' => $TempDoMST->fc_sono
        ])->first();
        if(!$checkSODTL)
            return response()->json(['status' => 400, 'message' => "Invalid Order! Kode barang tidak ada pada sales order"], 400);

        if($checkSODTL->fn_qty < ($checkSODTL->fn_qty_do + $request->fn_qty))
            return response()->json(['status' => 400, 'message' => "Over Stock! Jumlah stock melebihi pesanan"], 400);
        
        $addTempDoDTL = TempDoDTL::create([
            'fc_dono' => $dono_decoded,
            'fc_barcode' => $request->fc_barcode,
            'fc_statusbonus' => $request->fc_statusbonus,
            'fn_qty' => $request->fn_qty
        ]);

        if ($addTempDoDTL)
            return response()->json(['status' => 201, 'message' => 'Stock berhasil dimasukkan ke Delivery Order']);
        return response()->json(['status' => 400, 'message' => 'Stock Gagal ditambahkan ke DO'], 400);
    }

    public function removeTempDODTL (Request $request, $fc_dono) {
        $dono_decoded = base64_decode($fc_dono);
        if($request->fn_rownum == null || !TempDoDTL::where([ 'fc_dono' => $dono_decoded, 'fn_rownum' => $request->fn_rownum])->first()) 
            return response()->json(['status' => 400, 'message' => 'Detail stock tidak valid'], 400);
        
        $removeTempDoDTL = TempDoDTL::where([
            'fc_dono' => $dono_decoded,
            'fn_rownum' => $request->fn_rownum
        ])->delete();

        if($removeTempDoDTL) 
            return response()->json(['status' => 201, 'message' => 'Stock berhasil diremove']);

        return response()->json(['status' => 400, 'message' => 'Stock Gagal diremove'], 400);
    }

    public function updateTempDODTL (Request $request, $fc_dono) {
        $validator = Validator::make($request->all(), [
            'fn_rownum' => 'required',
            'fn_qty' => 'required',
        ], [
            'fn_rownum.required' => 'Data yang mana yang mau diupdate?',
            'fn_qty.required' => 'Kuantitas barang diterima harus diisi',
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => 300,
                'message' => $validator->errors()->first()
            ], 400);
        }
        
        $dono_decoded = base64_decode($fc_dono);
        $TempDODTL = TempDoDTL::with('tempdomst')->where([ 'fc_dono' => $dono_decoded, 'fn_rownum' => $request->fn_rownum])->first();
        if($request->fn_rownum == null || !$TempDODTL) 
            return response()->json(['status' => 400, 'message' => 'Detail stock tidak valid'], 400);

        $checkSODTL = SODTL::where([
            'fc_sono' => $TempDODTL->tempdomst->fc_sono,
            'fc_barcode' => substr($TempDODTL->fc_barcode, 0, 30)
        ])->first();
        
        if($checkSODTL->fn_qty < ($checkSODTL->fn_qty_do + $request->fn_qty - $TempDODTL->fn_qty))
            return response()->json(['status' => 400, 'message' => 'Over Stock! Jumlah stock melebihi pesanan'], 400);

        $updated = $TempDODTL->update([
            'fn_qty' => $request->fn_qty,
            'ft_description' => $request->ft_description
        ]);

        if ($updated)
            return response()->json(['status' => 201, 'message' => "Atribut Stock berhasil diupdate"]);

        return response()->json(['status' => 400, 'message' => "Update Fail! Maaf, gagal mengupdate atribut stock"]);
    }
}
