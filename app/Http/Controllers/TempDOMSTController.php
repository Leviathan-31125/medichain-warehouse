<?php

namespace App\Http\Controllers;

use App\Models\SOMST;
use App\Models\TempDoDMST;
use App\Models\TempDoDTL;
use App\Models\Warehouse;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TempDOMSTController extends Controller
{
    public function getAllTempDOMST() {
        $data = TempDoDMST::with('tempdodtl')->get();

        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function detailTempDOMST ($fc_dono) {
        $dono_decoded = base64_decode($fc_dono);
        $TempDoMST = TempDoDMST::with('tempdodtl')->find($dono_decoded);

        if ($TempDoMST)
            return response()->json(['status' => 200, 'data' => $TempDoMST]);
        return response()->json(['status' => 400, 'message' => 'Data Not Found! Delivery Order tidak tersedia di System']);
    }
    
    public function createTempDOMST (Request $request) {
        $tempDoMST = TempDoDMST::find($request->fc_dono);
        if ($tempDoMST)
            return response()->json(['status' => 400, 'message' => 'Duplicate Data! User yang sama sedang membuat Sales Order']);

        $SOMST = SOMST::find($request->fc_sono);
        if (!$SOMST)
            return response()->json(['status' => 400, 'message' => 'Invalid Data! Sales Order yang dimasukkan tidak valid']);
            
        $warehouse = Warehouse::find($request->fc_warehousecode);
        if (!$warehouse)
            return response()->json(['status' => 400, 'message' => 'Invalid Data! Gudang yang dimasukkan tidak valid']);
        
        $created = TempDoDMST::create([
            'fc_dono' => $request->fc_dono,
            'fc_sono' => $request->fc_sono,
            'fc_warehousecode' => $request->fc_warehousecode
        ]);

        if ($created) 
            return response()->json(['status' => 201, 'message' => 'DO berhasil dibuat']);
        
        return response()->json(['status' => 400, 'message' => 'Create Fail! Maaf Delivery Order gagal dibuat']);
    }

    public function submitTempDOMST ($fc_dono) {
        $dono_decoded = base64_decode($fc_dono);
        $tempDoMST = TempDoDMST::find($dono_decoded);

        if(!$tempDoMST)
            return response()->json(['status' => 400, 'message' => 'Data Not Found! Delivery Order tidak tersedia di System']);

        DB::beginTransaction();

        try{
            $tempDoMST->fc_status = 'SUBMIT';
            $tempDoMST->fd_dodate_system = Carbon::now();
            $tempDoMST->save();

            $deletedDODTL = TempDoDTL::where('fc_dono', $dono_decoded)->delete();
            $deletedDOMST = TempDoDMST::where('fc_dono', $dono_decoded)->delete();

            DB::commit();

            if($deletedDODTL && $deletedDOMST)
                return response()->json(['status' => 201, 'message' => 'Delivery Order berhasil disubmit']);

        } catch (Exception $err) {
            DB::rollBack();
            return response()->json((['status' => 400, 'message' => 'Create Failed! Delivery Order gagal dibuat'.$err->getMessage()]));
        }
    }

    public function setDetailInfoTempDODTL(Request $request, $fc_dono) {
        $validator = Validator::make($request->all(), [
            'fd_dodate_user' => 'required',
            'fv_transporter' => 'required',
            'fv_memberaddress_loading' => 'required'
        ], [
            'fd_dodate_user.required' => 'Masukkan tanggal Delivery Order',
            'fv_transporter.required' => 'Transportasi pengiriman tidak ditemukan',
            'fv_memberaddress_loading.required' => 'Alamat tujuan pengiriman belum ada',
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => 300,
                'message' => $validator->errors()->first()
            ]);
        }
        
        $dono_decoded = base64_decode($fc_dono);
        $tempDoMST = TempDoDMST::find($dono_decoded);

        if (!$tempDoMST)
            return response()->json(['status' => 400, 'message' => 'Data Not Found! Delivery Order tidak tersedia di System']);

        $updated = $tempDoMST->update([
            'fd_dodate_user' => $request->fd_dodate_user,
            'fv_transporter' => $request->fv_transporter,
            'fv_memberaddress_loading' => $request->fv_memberaddress_loading,
            'ft_description' => $request->ft_description
        ]);

        if ($updated) 
            return response()->json(['status' => 201, 'message' => 'DO berhasil diupdate']);

        return response()->json(['status' => 400, 'message' => 'Update Fail! Maaf Delivery Order gagal diupdate']);
    }

    public function updateRecevingStatus(Request $request, $fc_dono) {
        $validator = Validator::make($request->all(), [
            'fd_doarrivaldate' => 'required',
            'fc_custreceiver' => 'required'
        ], [
            'fd_doarrivaldate.required' => 'Tanggal kedatangan harus dilampirkan',
            'fc_custreceiver.required' => 'Penerima kedatangan barang tidak terdeteksi'
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => 300,
                'message' => $validator->errors()->first()
            ]);
        }

        $dono_decoded = base64_decode($fc_dono);
        $TempDoMST = TempDoDMST::find($dono_decoded);

        if (!$TempDoMST || $TempDoMST->fc_status == 'FINISH')
            return response()->json(['status' => 400, 'message' => 'Data Not Found! Delivery Order tidak tersedia di System']);

        $TempDoMST->fc_status = 'FINISH';
        $TempDoMST->fd_doarrivaldate = $request->fd_doarrivaldate;
        $TempDoMST->fc_custreceiver = $request->fc_custreceiver;
        if ($request->ft_description !== null)
            $TempDoMST->ft_description = $TempDoMST->ft_description . "Customer: " . $request->ft_description;

        $updated = $TempDoMST->save();

        if ($updated)
            return response()->json(['status' => 200, 'message' => 'Delivery Order berhasil diterima']);
        return response()->json(['status' => 400, 'message' => 'Delivery Order gagal diterima']);
    }
}
