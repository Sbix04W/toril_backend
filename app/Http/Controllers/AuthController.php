<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use DB;
use App\Models\User;
use App\Models\Persona;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{

    public function registerUser(Request $request)
    {
        try{

            $request_person = array(
                "nombre" => $request->nombre,
                "apellido" => $request->apellido,
                "correo" => $request->correo,
                "direccion" => $request->direccion,
                "telefono" => $request->telefono,
                "cifnif" => $request->cifnif
            );

            $request_user = array(
                "usuario" => $request->usuario,
                "password" => $request->password,
                "password2" => $request->password2,

                "rol" => $request->rol,
            );


            $validation = Validator::make(
                $request_user,
                [
                    'usuario' => 'required|string|unique:usuarios',
                    'password' => 'required',
                    'password2' => 'required|same:password',

                ],
                [
                    'usuario.unique' => 'El usuario ya existe',
                    'usuario.same' => 'Contraseñas no coinciden',

    
                ]
            );

            if ($validation->fails()) {
                return response()->json([
                    'data' => null,
                    'message' => 'Campos inválidos',
                    'status' => 400,
                    'statusText' => 'Bad Request',
                    'messageError' => $validation->errors()->all()
                ]);
            }

            DB::beginTransaction();
            $p = Persona::where('cifnif', $request_person["cifnif"])->first();
            $persona = new Persona();
            if ($p) {
                $persona = $p;
            }
            $persona->cifnif = trim($request_person["cifnif"]);
            $persona->nombre = trim($request_person["nombre"]);
            $persona->apellido = trim($request_person["apellido"]);
            $persona->correo =trim($request_person["correo"]);
            $persona->telefono = trim($request_person["telefono"]);
            $persona->direccion = trim($request_person["direccion"]);
            $persona->save();

            $user = new User();
            $user->usuario=trim($request_user['usuario']);
            $user->password= Hash::make($request_user['password']);
            $user->rol=$request_user['rol'];
            $user->id_persona= $persona->id;
            $user->save();
            $tokenResult =$user->createToken('auth_token')->plainTextToken;

            DB::commit();
            return response()->json([
                'message' => 'Usuario creado correctamente',
                'status' => 200,
                'statusText' => 'OK',
                'ok' => true
            ], 200);
        }
        catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Usuario no creado. Intente nuevamente',
                'status' => 400,
                'statusText' => 'Bad Request',
                'ok' => false
            ], 400);
        }
        
    }


    public function update_password(Request $request)
    {
        try{

            $user = User::where('id', $request->id)->first();
            $user->password= Hash::make($request->password);
            $user->save();

            return response()->json([
                'message' => 'Contraseña actualizada correctamente',
                'status' => 200,
                'statusText' => 'OK',
                'ok' => true
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Contraseña  no actualizada. Intente nuevamente',
                'status' => 400,
                'statusText' => 'Bad Request',
                'ok' => false
            ], 400);
        }
        
    }

    public function updatePerson(Request $request)
    {
        try{

         
            $persona = Persona::where('id', $request->id)->first();
            $persona->nombre = trim($request->nombre);
            $persona->apellido = trim($request->apellido);
            $persona->correo =trim( $request->correo);
            $persona->telefono = trim($request->telefono);
            $persona->save();

            return response()->json([
                'message' => 'Datos actualizado correctamente',
                'status' => 200,
                'statusText' => 'OK',
                'ok' => true
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 400,
                'statusText' => 'Bad Request',
                'ok' => false
            ], 400);
        }
        
    }

    public function login(Request $request)
    {
        $validation = Validator::make(
            $request->all(),
            [
                'usuario' => 'required|string',
                'password' => 'required|string'
            ]
        );

        if ($validation->fails()) {
            return response()->json([
                'message' => 'Campos Incompletos',
                'status' => 400,
                'statusText' => 'Bad Request',
            ]);
        }

        $user = User::where('usuario', $request->usuario)->where('rol', 'user')->first();
        if (!$user) {
            return response()->json(
                [
                    'message' => 'Usuario no existe.',
                    'status' => 404,
                    'statusText' => 'Not Found',
                ]
            );
        }

        $credentials = [
            'usuario' => $request->usuario,
            'password' => $request->password,
        ];

        if (!(Auth::attempt($credentials))) {
            return response()->json(
                [
                    'message' => 'Credenciales incorrectas',
                    'status' => 401,
                    'statusText' => 'Unauthorized',
                ]
            );
        }
        $user = $request->user();
        $tokenResult = Auth::user()->createToken('auth_token')->plainTextToken;
        $rol =$user->rol;
        return response()->json(
            [ 
                'message' => 'Credenciales correctas',
                'status' => 200,
                'access_token' => $tokenResult,
                'rol' => $rol,
            ]
        );
    }

    public function login_admin(Request $request)
    {
        $validation = Validator::make(
            $request->all(),
            [
                'usuario' => 'required|string',
                'password' => 'required|string'
            ]
        );

        if ($validation->fails()) {
            return response()->json([
                'message' => 'Campos Incompletos',
                'status' => 400,
                'statusText' => 'Bad Request',
            ]);
        }

        $user = User::where('usuario', $request->usuario)->where('rol', 'admin')->first();
        if (!$user) {
            return response()->json(
                [
                    'message' => 'Usuario no existe.',
                    'status' => 404,
                    'statusText' => 'Not Found',
                ]
            );
        }

        $credentials = [
            'usuario' => $request->usuario,
            'password' => $request->password,
        ];

        if (!(Auth::attempt($credentials))) {
            return response()->json(
                [
                    'message' => 'Credenciales incorrectas',
                    'status' => 401,
                    'statusText' => 'Unauthorized',
                ]
            );
        }
        $user = $request->user();
        $tokenResult = Auth::user()->createToken('auth_token')->plainTextToken;
        return response()->json(
            [ 
                'message' => 'Credenciales correctas',
                'status' => 200,
                'access_token' => $tokenResult,
            ]
        );
    }

    public function userInfor(Request $request)
    {
        try {
            $persona=$request->user()->getPersona;
            $persona['id']=$request->user()['id'];

            return response()->json(
                [
                    'data' => $persona,
                    'HttpResponse' => [
                        'status' => 200,
                        'statusText' => 'OK',
                    ]
                ]
            );
        } catch (\Exception $t) {
            return response()->json(
                [
                    'HttpResponse' => [
                        'status' => 400,
                        'statusText' => 'Bad Request',
                    ]
                ]
            );
        }
    }

    public function usuario_by_id(Request $request)
    {
        $id=$request->id_usuario;
        $b = User::where('id', $id)->with('getPersona')->first();
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

    public function usuario_app(Request $request)
    {
        $r = User::where('rol','user')->with('getPersona')->get();
        return response()->json([
            'data' => $r,
            'message' => 'Success',
            'status' => 200,
            'statusText' => 'OK',
        ], 200);
    }
}
