<?php


use App\Http\Controllers\ProductoController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ZonaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CuentaController;
use App\Http\Controllers\IngresoController;
use App\Http\Controllers\SaldoController;
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

Route::middleware(['guest'])->group(function() {
    //rutas para login
    Route::get('/', function () {return view('login');})->name('login');
    Route::post('/',[LoginController::class,'autentificacion']);
});

Route::middleware(['auth'])->group(function() {
    //rutas para usuarios identificados
    Route::get('/menu', function () {return view('menu');})->name('menu');
    Route::get("/registro_gasto",[CuentaController::class,"vistaRegistro"])->name("registro_gasto");
    Route::post("/registro_gasto",[CuentaController::class,"registro"])->name("registro_gasto");
    Route::get("/venta",[VentaController::class,"vistaRegistro"])->name("venta");
    Route::post("/venta",[VentaController::class,"registro"])->name("ventao");
    Route::get("/venta_rapida",[VentaController::class,"vistaRegistroRapido"])->name("venta_rapida");
    Route::post("/venta_rapida",[VentaController::class,"registroRapido"])->name("venta_rapida");
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
        });



     

    Route::get('logout',[LoginController::class,'logout'])->name('logout');
});

Route::get("/NoPermitido", function(){return view("alerta");});






