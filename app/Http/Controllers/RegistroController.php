<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registro;
use App\Models\Recibo;
use App\Models\Proveedor;
use App\Models\Saldos;

use Illuminate\Support\Facades\Mail;
use App\Mail\MailComprobante;


use Carbon\Carbon;
use DB;
use mikehaertl\pdftk\Pdf;

class RegistroController extends Controller
{

    public function registro_by_proveedor(Request $request)
    {
        $id= $request->id_proveedor;
        $r = Registro::where('id_proveedor',$id )->orderBy('fecha', 'desc')->orderBy('hora', 'desc')->limit(20)->get();
        
        return response()->json([
            'data' => $r,
            'message' => 'Success',
            'status' => 200,
            'statusText' => 'OK',
        ], 200);
    }

    public function registro_all(Request $request)
    {
        $existe=false;
        $r = Registro::orderBy('fecha', 'desc')->orderBy('hora', 'desc')->limit(20)->with('proveedor')->get();
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

    public static function pdf_comprobante($data)
    {
        $directory = "emitidos";
        try {
            $pdf = new Pdf(
                storage_path() . '/app/comprobante.pdf' 
            );

            $result = $pdf->fillForm([
                'codigo' => $data->codigo,
                'fecha' => $data->fecha,
                'cifnif' => $data->cifnif,

                'proveedor' => $data->proveedor,
                'email' => $data->email,
                'importe' => $data->importe,
            ])
                ->flatten()
                ->saveAs(storage_path() . '/app/' . $directory . "/comprobante_".$data->id_recibo.".pdf");
        
            return $pdf;
        } catch (Throwable  $e) {
            return false;
        }
    }

    public static function pdf_comprobante2()
    {
        $data = new \stdClass();
        $data->codigo ="REC000008";
        $data->fecha ="2024-10-21";
        $data->cifnif ="1805145701";
        $data->proveedor ="Jonathan Supe";
        $data->email ="correo@correo.com";
        $data->importe="12.00";
        $directory = "emitidos";
        try {
            $pdf = new Pdf(
                storage_path() . '/app/comprobante.pdf' 
            );

            $result = $pdf->fillForm([
                'codigo' => $data->codigo,
                'fecha' => $data->fecha,
                'cifnif' => $data->cifnif,

                'proveedor' => $data->proveedor,
                'email' => $data->email,
                'importe' => $data->importe,
            ])->flatten()->saveAs(storage_path() . '/app/' . $directory . "/comprobante_".$data->cifnif.".pdf");
               
        } catch (Throwable  $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 400,
                'statusText' => 'Bad Request',
                'ok' => false
            ], 400);
        }
    }

    


    public function save_registro(Request $request)
    {
        $id_usuario= $request->id_usuario;
        //EMITIDO=2
        //PAGADO=1
        try {
            DB::beginTransaction();

            $r=new Registro();  
            $r->fecha=date("Y-m-d", strtotime($request->fecha));
            $r->hora= date("H:i:s");
            $r->litros= $request->litros;
            $r->precio= $request->precio;
            $r->id_proveedor= $request->id_proveedor;
            $r->total= $request->total;
            $r->obs= $request->obs;
            $r->codigo= $this->new_codigo_reg();
            $r->iva= 0;
            $r->save();

            if(isset($r->id)){
                $rec=new Recibo();  
                if($request->total_cobrar>$request->total){
                    return response()->json([
                        'message' => 'El valor a cobrar es mayor al total',
                        'status' => 400,
                        'statusText' => 'Bad Request'
                    ]);  
                }
                else if($request->total_cobrar==$request->total){
                    $rec->estado='1';
                    $rec->codigo=$request->codigo_recibo;
                    $rec->fecha=Carbon::now()->format('Y-m-d');
                    $rec->fechapag=Carbon::now()->format('Y-m-d');
                    $rec->importe=$request->total_cobrar;
                    $rec->id_registro=$r->id;
                    $rec->save();




                    $p=Proveedor::where('id', $r->id_proveedor)->with('persona')->first();
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
                        'message' => 'Registro realizado correctamente.',
                        'status' => 200,
                        'statusText' => 'OK',
                    ]);
                   
                   


                }
                else{
                    $rec->estado='1';
                    $rec->codigo=$request->codigo_recibo;
                    $rec->fecha=Carbon::now()->format('Y-m-d');
                    $rec->fechapag=Carbon::now()->format('Y-m-d');
                    $rec->importe=$request->total_cobrar;
                    $rec->id_registro=$r->id;
                    $rec->save();

                    $nuevo_importe=$request->total-$request->total_cobrar;
                    //NUEVO RECIBO
                    $rec1=new Recibo();  
                    $rec1->estado='2';
                    $rec1->codigo=$this->new_codigo_recib2();
                    $rec1->fecha=Carbon::now()->format('Y-m-d');
                    $rec1->fechapag=NULL;
                    $rec1->importe=$nuevo_importe;
                    $rec1->id_registro=$r->id;
                    $rec1->save();
                    $p=Proveedor::where('id', $r->id_proveedor)->with('persona')->first();
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
                        'message' => 'Registro realizado correctamente.',
                        'status' => 200,
                        'statusText' => 'OK',
                    ]);
                }

            }  
            else{
                DB::rollBack();
                return response()->json([
                    'message' => 'Registro no realizado. Intente nuevamente.',
                    'status' => 400,
                    'statusText' => 'OK',
                ]);

            } 
         
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage(),
                'status' => 400,
                'statusText' => 'Bad Request',
                'ok' => false
            ], 400);
        }
    }

    public function new_codigo_reg()
    {
        $num=1;
        $cant_reg = DB::table('registro_leche')->count();
        if($cant_reg>0){
            $num=$cant_reg+1;
        }
        $elNumero = trim($num);
        $codigo=str_pad($elNumero, 7, "0", STR_PAD_LEFT);
        return "REL".$codigo;
        
    }

    public function new_codigo_recib()
    {
        $num=1;
        $cant_reg = DB::table('recibos')->count();
        if($cant_reg>0){
            $num=$cant_reg+1;
        }
        $elNumero = trim($num);
        $codigo=str_pad($elNumero, 7, "0", STR_PAD_LEFT);

        return response()->json([
            'codigo' => "RECB".$codigo,
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

}
