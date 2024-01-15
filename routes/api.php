<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RutaController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\ReciboController;
use App\Http\Controllers\SaldoController;






/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [AuthController::class, 'login']);
Route::post('login_admin', [AuthController::class, 'login_admin']);

Route::post('register', [AuthController::class, 'registerUser']);
Route::post('save_location',  [RutaController::class, 'save_location']);


Route::middleware("auth:sanctum")->group(function () {
    Route::get('userInfo',  [AuthController::class, 'userInfor']);
    Route::get('usuario_by_id',  [AuthController::class, 'usuario_by_id']);
    Route::post('updatePerson',  [AuthController::class, 'updatePerson']);
    Route::post('update_password',  [AuthController::class, 'update_password']);

    


    Route::get('ruta_by_usuario',  [RutaController::class, 'ruta_by_usuario']);
    Route::get('proveedor_by_ruta',  [ProveedorController::class, 'proveedor_by_ruta']);
    Route::post('save_registro',  [RegistroController::class, 'save_registro']);
    Route::get('get_codigo_reicb',  [RegistroController::class, 'new_codigo_recib']);
    Route::get('registro_by_proveedor',  [RegistroController::class, 'registro_by_proveedor']);
    Route::get('recibopend_by_registro',  [ReciboController::class, 'recibopend_by_registro']);
    Route::post('pagar_recibo',  [ReciboController::class, 'pagar_recibo']);
    Route::post('recibos_by_usuario',  [ReciboController::class, 'recibos_by_usuario']);
    Route::get('rutas/all',  [RutaController::class, 'ruta_all']);
    Route::get('rutas/delete/{id}',  [RutaController::class, 'destroy']);
    Route::post('rutas/store',  [RutaController::class, 'store']);
    Route::post('rutas/update/{id}',  [RutaController::class, 'update']);

    Route::get('usuarios/list',  [AuthController::class, 'usuario_app']);
    Route::get('ruta/{id}',  [RutaController::class, 'ruta_by_id']);

    Route::get('proveedores/all',  [ProveedorController::class, 'proveedores_all']);
    Route::get('proveedores/delete/{id}',  [ProveedorController::class, 'destroy']);
    Route::post('proveedores/store',  [ProveedorController::class, 'save']);
    Route::get('location_all',  [RutaController::class, 'location_all']);

    Route::get('saldos/all',  [SaldoController::class, 'saldos_all']);
    Route::get('saldo_by_usuario',  [SaldoController::class, 'saldo_by_usuario']);

    Route::post('saldos/store',  [SaldoController::class, 'store']);

    Route::get('test_comprobante',  [RegistroController::class, 'pdf_comprobante2']);
    Route::get('registro_all',  [RegistroController::class, 'registro_all']);
    Route::get('recibos_all',  [ReciboController::class, 'recibos_all']);


    
});
