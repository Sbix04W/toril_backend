<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ruta;
use App\Models\Locations;


class RutaController extends Controller
{
    public function ruta_by_usuario(Request $request)
    {
        $id= $request->id_user;
        $r = Ruta::where('id_usuario',$id )->get();
        return response()->json([
            'data' => $r,
            'message' => 'Success',
            'status' => 200,
            'statusText' => 'OK',
        ], 200);
    }

    public function ruta_all()
    {
        $r = Ruta::with('user')->get();
        return response()->json([
            'data' => $r,
            'message' => 'Success',
            'status' => 200,
            'statusText' => 'OK',
        ], 200);
    }

    public function location_all()
    {
        $r = Locations::where('fecha',date("Y-m-d"))->with('user')->get();
        return response()->json([
            'data' => $r,
            'message' => 'Success',
            'status' => 200,
            'statusText' => 'OK',
        ], 200);
    }

    public function destroy($id)
    {
        try {
            Ruta::find($id)->delete();
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

    public function ruta_by_id($id)
    {
        $b = Ruta::where('id', $id)->first();
        if (!$b) return response()->json([
            'data' => null,
            'message' => 'Not Found',
            'status' => 404,
            'statusText' => 'Bad Request',
        ], 404);

        
        return response()->json([
            'data' => $b,
            'message' => 'Success',
            'status' => 200,
            'statusText' => 'OK',
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            $s = Ruta::create(
                [
                    'origen' => $request->origen,
                    'destino' => $request->destino,
                    'descripcion' => $request->descripcion,
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


    public function save_location(Request $request)
    {

        try {
            $s = Locations::create(
                [
                    'fecha' => date("Y-m-d"),
                    'hora' => date("H:i:s"),
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'id_usuario' => $request->id_usuario,
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


    public function update(Request $request,$id)
    {
        try {

            $s = Ruta::find($id)->update(
                [
                    'origen' => $request->origen,
                    'destino' => $request->destino,
                    'descripcion' => $request->descripcion,
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
