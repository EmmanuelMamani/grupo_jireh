<?php


use App\Http\Controllers\ProductoController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ZonaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CuentaController;
use App\Http\Controllers\IngresoController;
use App\Http\Controllers\SaldoController;
use App\Http\Controllers\AsignacionController;
use App\Http\Controllers\PendienteController;
use App\Http\Controllers\UserController;

use App\Http\Controllers\VentaController;
use App\Models\Cuenta;
use Facade\FlareClient\View;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get("/base",[UserController::class,"registro"])->name("base");
Route::middleware(['guest'])->group(function() {
    //rutas para login
    Route::get('/', function () {return view('login');})->name('login');
    Route::post('/',[LoginController::class,'autentificacion']);
});

Route::middleware(['auth'])->group(function() {
    //rutas para usuarios identificados
    Route::get("/reporte_venta",[VentaController::class,"vistaReporte"])->name("reporte_ventas");
    Route::get("/reporte_lote",[IngresoController::class,"vistaReporte"])->name("reporte_lotes");
    Route::get("/detalle_venta/{id}",[VentaController::class,"detalle"])->name("venta_detalle");

    Route::get('/menu', function () {return view('menu');})->name('menu');
    Route::get("/registro_gasto",[CuentaController::class,"vistaRegistro"])->name("registro_gasto");
    Route::post("/registro_gasto",[CuentaController::class,"registro"])->name("registro_gasto");
    Route::get("/venta",[VentaController::class,"vistaRegistro"])->name("venta");
    Route::post("/venta",[VentaController::class,"registro"])->name("venta");
    Route::get("/venta_rapida",[VentaController::class,"vistaRegistroRapido"])->name("venta_rapida");
    Route::post("/venta_rapida",[VentaController::class,"registroRapido"])->name("venta_rapida");
    Route::get("/transferir_lote",[AsignacionController::class,"vistaRegistro"])->name("transferir_lote");
    Route::post("/transferir_lote",[AsignacionController::class,"registro"])->name("transferir_lote");
    Route::get("/saldos",[SaldoController::class,"vistaPago"])->name("saldos");
    Route::post("/saldos",[SaldoController::class,"Pago"])->name("saldos");
    Route::get("/ventas_pendientes",[PendienteController::class,"reporte"])->name("ventas_pendientes");
    Route::post("/ventas_pendientes/{id}/{tipo}",[PendienteController::class,"modificar"])->name("modificar");


        Route::middleware(['rol'])->group(function(){
            //rutas para usuario administrador
            Route::get("/registro_zona",[ZonaController::class,"vistaRegistro"])->name("registro_zona");
            Route::post("registro_zona",[ZonaController::class,"registro"])->name("registro_zona");
            Route::get("reporte_cliente",[ClienteController::class,"vistaReporte"])->name("reporte_cliente");
            Route::get("saldo_pasado",[SaldoController::class,"vistaRegistro"])->name("saldo_pasado");
            Route::post("saldo_pasado",[SaldoController::class,"registro"])->name("saldo_pasado");
            Route::get("/registro_producto",[ProductoController::class,"vistaRegistro"])->name("registro_producto");
            Route::post("registro_producto",[ProductoController::class,"registro"])->name("registro_producto");
            Route::get("/registro_lote",[IngresoController::class,"vistaRegistro"])->name("registro_lote");
            Route::post("registro_lote",[IngresoController::class,"registro"])->name("registro_lote");
            Route::get("/registro_cliente",[ClienteController::class,"vistaRegistro"])->name("registro_cliente");
            Route::post("registro_cliente",[ClienteController::class,"registro"])->name("registro_cliente");
            Route::get("reporte_cuenta",[CuentaController::class,"vistaReporte"])->name("reporte_cuenta");
            Route::get("/registro_empleado",[UserController::class,"vistaRegistro"])->name("registro_empleado");
            Route::post("/registro_empleado",[UserController::class,"registro"])->name("registro_empleado");
            Route::get("/detalle_cuenta/{id}/{fecha}",[CuentaController::class,"DetalleCuenta"])->name("detalle_cuenta");
            Route::get("/cuentas_periodo",[CuentaController::class,"VistaPeriodo"])->name("cuentas_periodo");
            Route::get("/reporte_periodo",[CuentaController::class,"ReportePeriodo"])->name("reporte_periodo");
        });

    Route::get('logout',[LoginController::class,'logout'])->name('logout');
});

Route::get("/NoPermitido", function(){return view("alerta");});







