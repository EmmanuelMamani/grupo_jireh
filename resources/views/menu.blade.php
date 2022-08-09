
@extends("header")
@section("opciones")
<a href="{{route('logout')}}" class="opciones_head">salir</a>
@endsection
@section("estilos")
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
<link rel="stylesheet" href="{{asset("css/menu.css")}}">
@endsection
@section("titulo", "Menu")
@section("contenido")
<div id="opciones" >
    @if (Auth::user()->Rol=='Administrador')
        <a href="/reporte_cliente" class="opcion row">
            <span class="funciones col-8">Clientes</span>
            <span class="material-symbols-outlined icono col">group</span>
        </a>
        <a href="/registro_empleado" class="opcion row">
            <span class="funciones col-8">Empleados</span>
            <span class="material-symbols-outlined icono col"> business_center</span>
        </a>
      {{-- <div class="opcion row">
            <span class="funciones col-8">Cuentas</span>
            <span class="material-symbols-outlined icono col">monetization_on</span>
        </div>--}}
        <a href="/registro_lote" class="opcion row">
            <span class="funciones col-8">Lotes</span>
            <span class="material-symbols-outlined icono col">local_shipping</span>
        </a>
    @endif
    <a class="opcion row" href="/venta">
        <span class="funciones col-8">Pre-Venta</span>
        <span class="material-symbols-outlined icono col">shopping_cart</span>
    </a>
    <a href="/venta_rapida" class="opcion row">
        <span class="funciones col-8">Venta rapida</span>
        <span class="material-symbols-outlined icono col">shopping_cart_checkout</span>
    </a>
    <a href="/saldos" class="opcion row">
        <span class="funciones col-8">Saldos</span>
        <span class="material-symbols-outlined icono col">payments</span>
    </a>
    <a href="/transferir_lote" class="opcion row">
        <span class="funciones col-8">Transferir lote</span>
        <span class="material-symbols-outlined icono col"> swap_horiz</span>
    </a>
    <a href="/registro_producto" class="opcion row">
        <span class="funciones col-8">Productos</span>
        <span class="material-symbols-outlined icono col">local_pizza</span>
    </a>
    <a href="/registro_zona" class="opcion row">
        <span class="funciones col-8">Zonas</span>
        <span class="material-symbols-outlined icono col">map</span>
    </a>
</div>

@endsection

