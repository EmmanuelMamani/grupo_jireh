<?php


use App\Http\Controllers\ProductoController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ZonaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\IngresoController;
use App\Http\Controllers\SaldoController;
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

Route::middleware(['guest'])->group(function() {
    //rutas para login
    Route::get('/', function () {
        return view('login');
    });
    Route::post('/',[LoginController::class,'autentificacion'])->name('login');
});

Route::middleware(['auth'])->group(function() {
    //rutas para usuarios identificados
    Route::get("/menu", function(){return view("menu");});
    Route::get("/venta", function(){return view("venta");});
    Route::get("/venta_rapida", function(){return view("venta_rapida");});
    Route::get("/transferir_lote", function(){return view("transferir_lote");});
    Route::get("/saldos", function(){return view("saldos");});
    Route::get("/saldo_pasado", function(){return view("saldo_pasado");});
        Route::middleware(['rol'])->group(function(){
            //rutas para usuario administrador
        Route::get("/registro_lote", function(){return view("registro_lote");});
        Route::get("/registro_cliente", function(){return view("registro_cliente");});
        Route::get("/registro_producto", function(){return view("registro_producto");});
        Route::get("/registro_gasto", function(){return view("registro_gasto");});
        Route::get("/registro_empleado", function(){return view("registro_empleado");});
        Route::get("/reporte_cliente", function(){return view("reporte_cliente");});
        Route::get("/reporte_empleados", function(){return view("reporte_empleados");});
        Route::get("/reporte_lote", function(){return view("reporte_lote");});
        Route::get("/registro_zona",[ZonaController::class,"vistaRegistro"])->name("registro_zona");
        Route::post("registro_zona",[ZonaController::class,"registro"])->name("registro_zona");
        });
});

Route::get("/NoPermitido", function(){return view("alerta");});



Route::get("/registro_producto", function(){return view("registro_producto");});
Route::get("/registro_gasto", function(){return view("registro_gasto");});
Route::get("/registro_empleado", function(){return view("registro_empleado");});
Route::get("/reporte_cliente", function(){return view("reporte_cliente");});
Route::get("/saldos", function(){return view("saldos");});
Route::get("/saldo_pasado", function(){return view("saldo_pasado");});
Route::get("/reporte_empleados", function(){return view("reporte_empleados");});
Route::get("/reporte_lote", function(){return view("reporte_lote");});

Route::get("/registro_zona",[ZonaController::class,"vistaRegistro"])->name("registro_zona");
Route::post("registro_zona",[ZonaController::class,"registro"])->name("registro_zona");
Route::get("/registro_producto",[ProductoController::class,"vistaRegistro"])->name("registro_producto");
Route::post("registro_producto",[ProductoController::class,"registro"])->name("registro_producto");
Route::get("/registro_lote",[IngresoController::class,"vistaRegistro"])->name("registro_lote");
Route::post("registro_lote",[IngresoController::class,"registro"])->name("registro_lote");
Route::get("/registro_cliente",[ClienteController::class,"vistaRegistro"])->name("registro_cliente");
Route::post("registro_cliente",[ClienteController::class,"registro"])->name("registro_cliente");
Route::get("saldo_pasado",[SaldoController::class,"vistaRegistro"])->name("saldo_pasado");
Route::post("saldo_pasado",[SaldoController::class,"registro"])->name("saldo_pasado");
Route::get("/alerta", function(){return view("alerta");});

