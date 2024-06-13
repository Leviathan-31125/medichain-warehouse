<?php

namespace App\Http\Controllers;

use App\Models\DoMST;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DOMSTController extends Controller
{
    public function getAllDOMST() {
        $data = DoMST::with(['dodtl', 'dodtl.invstore', 'somst'])->get();
        return response()->json($data, 200);
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
            ], 400);
        }

        $dono_decoded = base64_decode($fc_dono);
        $DoMST = DoMST::find($dono_decoded);

        if (!$DoMST || $DoMST->fc_status == 'FINISH')
            return response()->json(['status' => 400, 'message' => 'Data Not Found! Delivery Order tidak tersedia di System'], 400);

        $DoMST->fc_status = 'FINISH';
        $DoMST->fd_doarrivaldate = $request->fd_doarrivaldate;
        $DoMST->fc_custreceiver = $request->fc_custreceiver;
        if ($request->ft_description !== null)
            $DoMST->ft_description = $DoMST->ft_description . "Customer: " . $request->ft_description;

        $updated = $DoMST->save();

        if ($updated)
            return response()->json(['status' => 200, 'message' => 'Delivery Order berhasil diterima']);
        return response()->json(['status' => 400, 'message' => 'Delivery Order gagal diterima'], 400);
    }
}
