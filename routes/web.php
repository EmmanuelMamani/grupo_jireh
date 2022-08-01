<?php

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

Route::get('/', function () {
    return view('login');
});
Route::get("/menu", function(){return view("menu");});
Route::get("/venta", function(){return view("venta");});
Route::get("/venta_rapida", function(){return view("venta_rapida");});
Route::get("/transferir_lote", function(){return view("transferir_lote");});
Route::get("/registro_lote", function(){return view("registro_lote");});
Route::get("/registro_cliente", function(){return view("registro_cliente");});
Route::get("/registro_producto", function(){return view("registro_producto");});
Route::get("/registro_gasto", function(){return view("registro_gasto");});
Route::get("/registro_zona", function(){return view("registro_zona");});
Route::get("/registro_empleado", function(){return view("registro_empleado");});
Route::get("/reporte_cliente", function(){return view("reporte_cliente");});
Route::get("/saldos", function(){return view("saldos");});
Route::get("/saldo_pasado", function(){return view("saldo_pasado");});
Route::get("/reporte_empleados", function(){return view("reporte_empleados");});
Route::get("/reporte_lote", function(){return view("reporte_lote");});