<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proveedor;
use App\Models\Persona;


class ProveedorController extends Controller
{
    public function proveedor_by_ruta(Request $request)
    {
        $id= $request->id_ruta;
        $r = Proveedor::where('id_ruta',$id )->with('persona')->get();
        $arrayRutas=[];
        if ($r) {
            foreach ($r as $p) {
                $arrayRutas[]=array(
                    'latitude'=>$p->latitud,
                    'longitude'=>$p->longitud
                );
               
            }
        }
        return response()->json([
            'data' => [
                'proveedor'=>$r,
                'rutas'=>$arrayRutas
            ],
            'message' => 'Success',
            'status' => 200,
            'statusText' => 'OK',
        ], 200);
    }

    public function proveedores_all()
    {
        $r = Proveedor::with('persona','ruta')->get();
        return response()->json([
            'data' => $r,
            'message' => 'Success',
            'status' => 200,
            'statusText' => 'OK',
        ], 200);
    }


    public function save(Request $request)
    {
        try {

            $p=Persona::where('cifnif',  $request->cifnif)->first();
            if(!$p){
                $p=new Persona();  
            }

            $p->cifnif= $request->cifnif;
            $p->nombre= $request->nombre;
            $p->apellido= $request->apellido;
            $p->correo= $request->email;
            $p->direccion= $request->direccion;
            $p->telefono= $request->telefono;
            $p->save();
            
            $v = Proveedor::create(
                [
                    'id_persona' => $p->id,
                    'id_ruta' => $request->id_ruta,
                    'latitud' => $request->latitude,
                    'longitud' => $request->longitude,
                ]
            );

            return response()->json([
                'data' => $v,
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


    public function destroy($id)
    {
        try {
            Proveedor::find($id)->delete();
            return response()->json([
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
