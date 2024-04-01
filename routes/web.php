<?php


use App\Http\Controllers\ProductoController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ZonaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CuentaController;
use App\Http\Controllers\IngresoController;
use App\Http\Controllers\SaldoController;
use App\Http\Controllers\AsignacionController;
use App\Http\Controllers\ListaController;
use App\Http\Controllers\PendienteController;
use App\Http\Controllers\UserController;

use App\Http\Controllers\VentaController;
use App\Models\Cuenta;
use App\Models\User;
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
    Route::get("/completar_lista/{id}",[ListaController::class,"completar"])->name("completar_lista");
    Route::post("/cancelar_lista/{id}",[ListaController::class,"eliminar"])->name("cancelar_lista");
    Route::get("/reporte_diario",[CuentaController::class,"reporteDiario"])->name("reporte_diario");
    Route::get("/venta_devolucion/{id}",[VentaController::class,"VistaDevolucion"])->name("venta_devolucion");
    Route::post("/devolucion/{id}",[VentaController::class,"Devolucion"])->name("devolucion");
    Route::get('/menu', function () {return view('menu');})->name('menu');
    Route::get("/registro_gasto",[CuentaController::class,"vistaRegistro"])->name("registro_gasto");
    Route::post("/registro_gasto",[CuentaController::class,"registro"])->name("registro_gasto");
    Route::get("/venta",[VentaController::class,"vistaRegistro"])->name("venta");
    Route::post("/venta",[VentaController::class,"registro"])->name("venta");
    Route::post("/venta_completada/{id}",[VentaController::class,"venta_completa"])->name("venta_completa");
    Route::get("/venta_rapida",[VentaController::class,"vistaRegistroRapido"])->name("venta_rapida");
    Route::post("/venta_rapida",[VentaController::class,"registroRapido"])->name("venta_rapida");
    Route::get("/transferir_lote",[AsignacionController::class,"vistaRegistro"])->name("transferir_lote");
    Route::post("/transferir_lote",[AsignacionController::class,"registro"])->name("transferir_lote");
    Route::get("/saldos",[SaldoController::class,"vistaPago"])->name("saldos");
    Route::post("/saldos",[SaldoController::class,"Pago"])->name("saldos");
    Route::get("/ventas_pendientes",[PendienteController::class,"reporte"])->name("ventas_pendientes");
    Route::post("/ventas_pendientes/{id}/{tipo}",[PendienteController::class,"modificar"])->name("modificar");
    Route::get("/perfil",[UserController::class,"perfil"])->name("perfil");
    Route::post("/cambiar_contraseña",[UserController::class,"cambiar_contraseña"])->name("cambiar_contraseña");
    Route::get("/registro_lista",[ListaController::class,"vistaRegistro"])->name("registro_lista");
    Route::post("/registro_lista",[ListaController::class,"Registro"])->name("registro_lista");
    Route::post("/transferir_lista",[ListaController::class,"transferir"])->name("transferir_lista");
    Route::get("/lista_reporte",[ListaController::class,"reporte"])->name("lista_reporte");
    Route::get("/registro_cliente",[ClienteController::class,"vistaRegistro"])->name("registro_cliente");
    Route::post("registro_cliente",[ClienteController::class,"registro"])->name("registro_cliente");
    Route::get("/reporte_venta",[VentaController::class,"vistaReporte"])->name("reporte_ventas");
    Route::get("/reporte_lote_venta/{id}",[VentaController::class,"vistaReporteVentas"])->name("reporte_lote_ventas");
    Route::get("/detalle_venta/{id}",[VentaController::class,"detalle"])->name("venta_detalle");
    
    Route::middleware(['rol'])->group(function(){
            //rutas para usuario administrador
            Route::get("/editar_venta/{id}",[VentaController::class,"vistaEditar"])->name("editar_venta");
            Route::post("/editar_venta/{id}",[VentaController::class,"Editar"])->name("editar_venta");
            Route::get("/editar_lote/{id}",[IngresoController::class,"vistaEditar"])->name("editar_lote");
            Route::post("/editar_lote/{id}",[IngresoController::class,"Editar"])->name("editar_lote");
            Route::post("/eliminar_cliente/{id}",[ClienteController::class,"eliminar"])->name("eliminar_cliente");
            Route::get("/editar_cliente/{id}",[ClienteController::class,"vistaEditar"])->name('editar_cliente');
            Route::post("/editar_cliente/{id}",[ClienteController::class,'editar'])->name('editar_cliente');
            Route::post("/eliminar_empleado/{id}",[UserController::class,"eliminar"])->name("eliminar_empleado");
            Route::get("/reporte_empleados",[UserController::class,"vistaReporte"])->name("reporte_empleados");
            Route::get("/reporte_lote",[IngresoController::class,"vistaReporte"])->name("reporte_lotes");
            Route::get("/registro_zona",[ZonaController::class,"vistaRegistro"])->name("registro_zona");
            Route::post("registro_zona",[ZonaController::class,"registro"])->name("registro_zona");
            Route::get("reporte_cliente",[ClienteController::class,"vistaReporte"])->name("reporte_cliente");
            Route::get("saldo_pasado",[SaldoController::class,"vistaRegistro"])->name("saldo_pasado");
            Route::post("saldo_pasado",[SaldoController::class,"registro"])->name("saldo_pasado");
            Route::get("/registro_producto",[ProductoController::class,"vistaRegistro"])->name("registro_producto");
            Route::post("registro_producto",[ProductoController::class,"registro"])->name("registro_producto");
            Route::get("/registro_lote",[IngresoController::class,"vistaRegistro"])->name("registro_lote");
            Route::post("registro_lote",[IngresoController::class,"registro"])->name("registro_lote");
            Route::get("reporte_cuenta",[CuentaController::class,"vistaReporte"])->name("reporte_cuenta");
            Route::get("/registro_empleado",[UserController::class,"vistaRegistro"])->name("registro_empleado");
            Route::post("/registro_empleado",[UserController::class,"registro"])->name("registro_empleado");
            Route::get("/detalle_cuenta/{id}/{fecha}",[CuentaController::class,"DetalleCuenta"])->name("detalle_cuenta");
            Route::get("/cuentas_periodo",[CuentaController::class,"VistaPeriodo"])->name("cuentas_periodo");
            
            Route::get("/ventas_periodo/{id}",[VentaController::class,"VistaPeriodo"])->name("ventas_periodo");
            Route::get("/reporte_periodo_ventas/{id}",[VentaController::class,"ReportePeriodo"])->name("reporte_periodo_ventas");
            Route::get("/reporte_periodo_ventas_pdf/{id}/{inicio}/{fin}",[VentaController::class,"ReportePeriodoPDF"])->name("reporte_periodo_ventas_pdf");


            Route::get("/reporte_periodo",[CuentaController::class,"ReportePeriodo"])->name("reporte_periodo");
            Route::get("/reporte_historico",[CuentaController::class,"reporteHistorico"])->name("reporte_historico");
            Route::post("/eliminar_lote/{id}",[IngresoController::class,"Eliminar"])->name("eliminar_lote");
            Route::post("/pagar_lote/{id}",[IngresoController::class,"Pagar"])->name("pagar_lote");
           Route::get("/decarga_ventas/{id}",[VentaController::class,"descarga"])->name("descarga_ventas");
           Route::get("/decarga_cuentas",[CuentaController::class,"descarga"])->name("descarga_cuentas");
           Route::get("/decarga_cuentas_diarias",[CuentaController::class,"descarga_cuentas_diarias"])->name("descarga_cuentas_diarias");
           Route::get("/decarga_periodo/{inicio}/{fin}",[CuentaController::class,"descarga_periodo"])->name("descarga_periodo");
           Route::get("/decarga_diario/{user_id}",[CuentaController::class,"descarga_diario"])->name("descarga_diario");
           Route::get("/decarga_lotes",[IngresoController::class,"descarga"])->name("descarga_lotes");
           Route::get("/reporte_producto",[ProductoController::class,"vistaReporte"])->name("reporte_producto");
           Route::post("/eliminar_producto/{id}",[ProductoController::class,"eliminar"])->name("eliminar_producto");
           Route::get("/reporte_zona",[ZonaController::class,"vistaReporte"])->name("reporte_zona");
           Route::post("/eliminar_zona/{id}",[ZonaController::class,"eliminar"])->name("eliminar_zona");
        });

    Route::get('logout',[LoginController::class,'logout'])->name('logout');
});

Route::get("/NoPermitido", function(){return view("alerta");});


Route::get('phpmyinfo', function () {
    phpinfo(); 
})->name('phpmyinfo');




