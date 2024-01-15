<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recibo;
use App\Models\Registro;

use Carbon\Carbon;
use DB;

class ReciboController extends Controller
{
    public function recibopend_by_registro(Request $request)
    {
        $id= $request->id_registro;
        $r = Recibo::where('id_registro',$id )->where('estado','2' )->first();
        $existe=false;
        if($r){
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

    public function recibos_all()
    {
        $existe=false;
        $r = Recibo::with('registro')->get();
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

    public function new_codigo_recib2()
    {
        $num=1;
        $cant_reg = DB::table('recibos')->count();
        if($cant_reg>0){
            $num=$cant_reg+1;
        }
        $elNumero = trim($num);
        $codigo=str_pad($elNumero, 7, "0", STR_PAD_LEFT);
        return  "RECB".$codigo;
    }

    public function pagar_recibo(Request $request)
    {
        //EMITIDO=2
        //PAGADO=1
        $id_usuario= $request->id_usuario;

        try {
                DB::beginTransaction();
                $id= $request->id_recibo;
                $rec=Recibo::where('id',$id)->first();  

                if($request->total_cobrar>$request->total){
                    return response()->json([
                        'message' => 'El valor a cobrar es mayor al total',
                        'status' => 400,
                        'statusText' => 'Bad Request'
                    ]);  
                }
                else if($request->total_cobrar==$request->total){
                    $rec->estado='1';
                    $rec->fechapag=Carbon::now()->format('Y-m-d');
                    if($rec->save()){

                         
                        $registro=Registro::where('id',$rec->id_registro)->first();
                        $p=Proveedor::where('id', $registro->id_proveedor)->with('persona')->first();
                        if($p){
                            $dataFile = new \stdClass();
                            $dataFile->codigo =$rec->codigo ;
                            $dataFile->fecha =$rec->fechapag ;
                            $dataFile->cifnif =$p->persona->cifnif ;
                            $dataFile->proveedor =$p->persona->nombre ." ".$p->persona->apellido ;
                            $dataFile->email =$p->persona->correo ;
                            $dataFile->importe=$rec->importe ;
                            $dataFile->id_recibo=$rec->id;
                            $pdf=$this->pdf_comprobante($dataFile);
                            if($pdf){
                                $objEmail = new \stdClass();
                                $objEmail->nombre = $p->persona->nombre;
                                $objEmail->apellido = $p->persona->apellido ;
                                $objEmail->id_recibo =$rec->id;
                                Mail::to($p->persona->correo)->send(new MailComprobante($objEmail));
    
                            }
                           
    
    
                        }

                        $saldo=Saldos::where('id_usuario',$id_usuario)->where('monto_restante','>',0)->first();
                        if($saldo){
                            $saldo->monto_restante=$saldo->monto_restante-$rec->importe;
                            $saldo->save();
                        }
                        
                        DB::commit();
                        return response()->json([
                            'message' => 'Cobro realizado correctamente.',
                            'status' => 200,
                            'statusText' => 'OK',
                        ]);
                    }
                    else{
                        DB::rollBack();
                        return response()->json([
                            'message' => 'Cobro no realizado correctamente. Intente nuevamente',
                            'status' => 400,
                            'statusText' => 'OK',
                        ]);
                    }
                  

                }
                else{
                    $rec->estado='1';
                    $rec->fechapag=Carbon::now()->format('Y-m-d');
                    $rec->importe=$request->total_cobrar;
                    if($rec->save()){
                        $rec1=new Recibo();  

                        $nuevo_importe=$request->total-$request->total_cobrar;
                        //NUEVO RECIBO
                        $rec1=new Recibo();  
                        $rec1->estado='2';
                        $rec1->codigo=$this->new_codigo_recib2();
                        $rec1->fecha=Carbon::now()->format('Y-m-d');
                        $rec1->fechapag=NULL;
                        $rec1->importe=$nuevo_importe;
                        $rec1->id_registro=$rec->id_registro;

                        if($rec1->save()){
                            
                            $registro=Registro::where('id',$rec->id_registro)->first();
                            $p=Proveedor::where('id', $registro->id_proveedor)->with('persona')->first();
                            if($p){
                                $dataFile = new \stdClass();
                                $dataFile->codigo =$rec->codigo ;
                                $dataFile->fecha =$rec->fechapag ;
                                $dataFile->cifnif =$p->persona->cifnif ;
                                $dataFile->proveedor =$p->persona->nombre ." ".$p->persona->apellido ;
                                $dataFile->email =$p->persona->correo ;
                                $dataFile->importe=$rec->importe ;
                                $dataFile->id_recibo=$rec->id;
                                $pdf=$this->pdf_comprobante($dataFile);
                                if($pdf){
                                    $objEmail = new \stdClass();
                                    $objEmail->nombre = $p->persona->nombre;
                                    $objEmail->apellido = $p->persona->apellido ;
                                    $objEmail->id_recibo =$rec->id;
                                    Mail::to($p->persona->correo)->send(new MailComprobante($objEmail));
        
                                }
                               
        
        
                            }

                            $saldo=Saldos::where('id_usuario',$id_usuario)->where('monto_restante','>',0)->first();
                            if($saldo){
                                $saldo->monto_restante=$saldo->monto_restante-$rec->importe;
                                $saldo->save();
                            }
                            
                            DB::commit();
                            return response()->json([
                                'message' => 'Cobro realizado correctamente.',
                                'status' => 200,
                                'statusText' => 'OK',
                            ]);

                        }
                        else{
                            DB::rollBack();
                            return response()->json([
                                'message' => 'Cobro no realizado correctamente. Intente nuevamente',
                                'status' => 400,
                                'statusText' => 'OK',
                            ]);
                        }
                    }
                    else{
                        DB::rollBack();
                        return response()->json([
                            'message' => 'Cobro no realizado correctamente. Intente nuevamente',
                            'status' => 400,
                            'statusText' => 'OK',
                        ]);
                    }
                    
                    
                }

         
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' =>'Cobro no realizado correctamente. Contactese con el administrador',
                'status' => 400,
                'statusText' => 'Bad Request',
                'ok' => false
            ], 400);
        }
    }

    public function recibos_by_usuario(Request $request)
    {
        $id= $request->id_usuario;
        $tipo= $request->tipo;

        $recibos = DB::table('recibos')
            ->select('recibos.*','rutas.origen','rutas.destino','registro_leche.codigo as codigo_reg','personas.nombre','personas.apellido')
            ->leftJoin('registro_leche', 'registro_leche.id', '=', 'recibos.id_registro')
            ->leftJoin('proveedores', 'proveedores.id', '=', 'registro_leche.id_proveedor')
            ->leftJoin('personas', 'personas.id', '=', 'proveedores.id_persona')

            ->leftJoin('rutas', 'rutas.id', '=', 'proveedores.id_ruta')
            ->where('rutas.id_usuario', '=', $id)
            ->where('recibos.estado', '=', $tipo)

            ->get();

        return response()->json([
            'data' => $recibos,
            'message' => 'Success',
            'status' => 200,
            'statusText' => 'OK',
        ], 200);
    }
}
