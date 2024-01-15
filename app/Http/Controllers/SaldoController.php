<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Saldos;
use DB;

class SaldoController extends Controller
{
    public function saldos_all()
    {
        $r = Saldos::with('usuario')->get();
        return response()->json([
            'data' => $r,
            'message' => 'Success',
            'status' => 200,
            'statusText' => 'OK',
        ], 200);
    }

    
    public function saldo_by_usuario(Request $request)
    {
        $existe=false;
        $id=$request->id_usuario;
        $r = DB::table('saldos')->select( DB::raw('SUM(monto_restante) as monto_restante'))->where('id_usuario',$id)->groupBy('id_usuario')->having('monto_restante', '>', 0)->get();
        if(count($r)>0){
            $existe=true;

        }
        return response()->json([
            'data' => $r,
            'existe' => $existe,

            'message' => 'Success',
            'status' => 200,
            'statusText' => 'OK',
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            $s = Saldos::create(
                [
                    'fecha' =>date("Y-m-d"),
                    'monto_total' => $request->monto_total,
                    'monto_restante' => $request->monto_total,
                    'id_usuario' => $request->usuario,

                ]
            );

            return response()->json([
                'data' => $s,
                'message' => 'Success',
                'status' => 200,
                'statusText' => 'OK',
                'ok' => true
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => [$th->getMessage()],
                'status' => 400,
                'statusText' => 'Bad Request',
                'ok' => false
            ], 400);
        }
    }

}
