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

    public function getDetailTempDOSOMST($fc_dono){
        $dono_decoded = base64_decode($fc_dono);
        $TempDoMST = TempDoDMST::with(['tempdodtl', 'tempdodtl.invstore', 'somst', 'somst.sodtl', 'somst.sodtl.stock', 'somst.customer'])->find($dono_decoded);

        if ($TempDoMST)
            return response()->json(['status' => 200, 'data' => $TempDoMST]);
        return response()->json(['status' => 400, 'message' => 'Data Not Found! Delivery Order tidak tersedia di System'], 400);
    }

    public function detailTempDOMST ($fc_dono) {
        $dono_decoded = base64_decode($fc_dono);
        $TempDoMST = TempDoDMST::with('tempdodtl')->find($dono_decoded);

        if ($TempDoMST)
            return response()->json(['status' => 200, 'data' => $TempDoMST]);
        return response()->json(['status' => 400, 'message' => 'Data Not Found! Delivery Order tidak tersedia di System'], 400);
    }
    
    public function createTempDOMST (Request $request) {
        $validator = Validator::make($request->all(), [
            'fc_dono' => 'required',
            'fc_sono' => 'required',
            'fc_warehousecode' => 'required'
        ], [
            'fc_dono.required' => "No. DO wajib dilampirkan",
            'fc_sono.required' => "No. SO tidak diketahui",
            'fc_warehousecode.required' => "Gudang pengiriman tidak ada"
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first()
            ], 400);
        }

        $tempDoMST = TempDoDMST::find($request->fc_dono);
        if ($tempDoMST)
            return response()->json(['status' => 400, 'message' => 'Duplicate Data! User yang sama sedang membuat Sales Order'], 400);

        $SOMST = SOMST::find($request->fc_sono);
        if (!$SOMST || $SOMST->fc_status == "LOCK" || $SOMST->fc_status == "FINISH")
            return response()->json(['status' => 400, 'message' => 'Invalid Data! Sales Order yang dimasukkan tidak valid'], 400);
            
        $warehouse = Warehouse::find($request->fc_warehousecode);
        if (!$warehouse)
            return response()->json(['status' => 400, 'message' => 'Invalid Data! Gudang yang dimasukkan tidak valid'], 400);
        
        $created = TempDoDMST::create([
            'fc_dono' => $request->fc_dono,
            'fc_sono' => $request->fc_sono,
            'fc_warehousecode' => $request->fc_warehousecode
        ]);

        if ($created) 
            return response()->json(['status' => 201, 'message' => 'DO berhasil dibuat']);
        
        return response()->json(['status' => 400, 'message' => 'Create Fail! Maaf Delivery Order gagal dibuat'], 400);
    }

    public function submitTempDOMST ($fc_dono) {
        $dono_decoded = base64_decode($fc_dono);
        $tempDoMST = TempDoDMST::find($dono_decoded);

        if(!$tempDoMST)
            return response()->json(['status' => 400, 'message' => 'Data Not Found! Delivery Order tidak tersedia di System'], 400);

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
            return response()->json(['status' => 400, 'message' => 'Create Failed! Delivery Order gagal dibuat'.$err->getMessage()], 400);
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
            ], 400);
        }
        
        $dono_decoded = base64_decode($fc_dono);
        $tempDoMST = TempDoDMST::find($dono_decoded);

        if (!$tempDoMST)
            return response()->json(['status' => 400, 'message' => 'Data Not Found! Delivery Order tidak tersedia di System'], 400);

        $updated = $tempDoMST->update([
            'fd_dodate_user' => $request->fd_dodate_user,
            'fv_transporter' => $request->fv_transporter,
            'fv_memberaddress_loading' => $request->fv_memberaddress_loading,
            'ft_description' => $request->ft_description
        ]);

        if ($updated) 
            return response()->json(['status' => 201, 'message' => 'DO berhasil diupdate']);

        return response()->json(['status' => 400, 'message' => 'Update Fail! Maaf Delivery Order gagal diupdate'], 400);
    }

    public function cancelTempDOMST ($fc_dono) {
        $dono_decoded = base64_decode($fc_dono);
        $tempDoMST = TempDoDMST::find($dono_decoded);

        if(!$tempDoMST)
            return response()->json(['status' => 400, 'message' => 'Data Not Found! Delivery Order tidak tersedia di System'], 400);

        DB::beginTransaction();

        try{
            $deletedDODTL = TempDoDTL::where('fc_dono', $dono_decoded)->delete();
            $deletedDOMST = TempDoDMST::where('fc_dono', $dono_decoded)->delete();

            DB::commit();

            if($deletedDODTL && $deletedDOMST)
                return response()->json(['status' => 201, 'message' => 'Delivery Order berhasil dicancel']);

        } catch (Exception $err) {
            DB::rollBack();
            return response()->json(['status' => 400, 'message' => 'Create Failed! Delivery Order gagal dibuat'.$err->getMessage()], 400);
        }
    }

    public function checkActiveDO ($fc_dono) {
        $dono_decoded = base64_decode($fc_dono);
        $TempDoMST = TempDoDMST::with('tempdodtl')->find($dono_decoded);

        if ($TempDoMST)
            return response()->json(['status' => true]);
        return response()->json(['status' => false]);
    }
}
