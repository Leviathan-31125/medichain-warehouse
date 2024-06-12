<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WarehouseController extends Controller
{
    public function getAllWarehouse (){
        $data = Warehouse::get();
        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function getDetailWarehouse ($fc_warehousecode) {
        $warehousecode_decoded = base64_decode(($fc_warehousecode));

        $warehouse = Warehouse::find($warehousecode_decoded);
        if (!$warehouse)
            return response()->json(['status' => 200, 'data' => $warehouse], 200);

        return response()->json(['status' => 400, 'message' => 'Not Found! Data tidak ditemukan'], 400);
    }

    public function createWarehouse (Request $request) {
        $validator = Validator::make($request->all(), [
            'fv_warehousename' => 'required',
            'fc_position' => 'required',
            'fv_warehouseaddress' => 'required'
        ], [
            'fv_warehousename.required' => 'Nama gudang wajib disertakan',
            'fc_position.required' => 'Status posisi gudang wajib dimasukkan',
            'fv_warehouseaddress' => 'Alamat gudang wajib dicantumkan'
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first()
            ], 400);
        }

        $created = Warehouse::create([
            'fv_warehousename' => $request->fv_warehousename,
            'fc_position' => $request->fc_position,
            'fv_warehouseaddress' => $request->fv_warehouseaddress,
            'ft_description' => $request->ft_description
        ]);

        if ($created) 
            return response()->json(['status' => 201, 'message' => 'Gudang berhasil dibuat', 'data' => $created]);
        else 
            return response()->json(['status' => 400, 'message' => 'Create Failed! Gagal menambahkan gudang'], 400);
    }

    public function updateWarehouse (Request $request, $fc_warehousecode) {
        $validator = Validator::make($request->all(), [
            'fv_warehousename' => 'required',
            'fc_position' => 'required',
            'fv_warehouseaddress' => 'required'
        ], [
            'fv_warehousename.required' => 'Nama gudang wajib disertakan',
            'fc_position.required' => 'Status posisi gudang wajib dimasukkan',
            'fv_warehouseaddress' => 'Alamat gudang wajib dicantumkan'
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first()
            ], 400);
        }

        $warehousecode_decoded = base64_decode($fc_warehousecode);
        $warehouse = Warehouse::find($warehousecode_decoded);

        if (!$warehouse)
            return response()->json(['status' => 400, 'message' => 'Not Found! Data tidak ditemukan'], 400);

        $updated = $warehouse->update([
            'fv_warehousename' => $request->fv_warehousename,
            'fc_position' => $request->fc_position,
            'fv_warehouseaddress' => $request->fv_warehouseaddress,
            'ft_description' => $request->ft_description
        ]);

        if ($updated)
            return response()->json(['status' => 201, 'message' => 'Data gudang berhasil diupdate']);
        else
            return response()->json(['status' => 400, 'message' => 'Gagal update!'], 400);
    }

    public function deletedWarehouse ($fc_warehousecode) {
        $warehousecode_decoded = base64_decode($fc_warehousecode);
        $warehouse = Warehouse::find($warehousecode_decoded);

        if (!$warehouse)
            return response()->json(['status' => 400, 'message' => 'Not Found! Data tidak ditemukan'], 400);

        $deleted = $warehouse->delete();

        if($deleted) 
            return response()->json(['status' => 200, 'message' => 'Gudang berhasil dihapus']);
        else 
            return response()->json(['status' => 400, 'message' => 'Gagal hapus! Data gagal dihapus atau tidak ditemukan'], 400);
    }
}