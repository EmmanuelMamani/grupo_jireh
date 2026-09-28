
@extends("header")
@section("opciones")
<a href="{{route('logout')}}" class="opciones_head">Salir</a>
<a href="{{route("perfil")}}" class="opciones_head">{{Auth::user()->Nombre}}</a>
@endsection
@section("estilos")
<link rel="stylesheet" href="{{asset("css/menu.css")}}">
@endsection
@section("titulo", "Grupo JIREH")
@section("contenido")
<div id="opciones" >
    <h3>Menú</h3>
    <a href="{{route("registro_cliente")}}" class="opcion row">
        <span class="funciones col-8">Clientes</span>
        <x-icon name="group" class="icono col"/>
    </a>
    @if (Auth::user()->Rol=='Administrador')
        <a href="{{route("reporte_empleados")}}" class="opcion row">
            <span class="funciones col-8">Empleados</span>
            <x-icon name="business_center" class="icono col"/>
        </a>
        <a href="{{route("reporte_lotes")}}" class="opcion row">
            <span class="funciones col-8">Lotes</span>
            <x-icon name="local_shipping" class="icono col"/>
        </a>
        <a href="{{route("registro_producto")}}" class="opcion row">
            <span class="funciones col-8">Productos</span>
            <x-icon name="local_pizza" class="icono col"/>
        </a>
        <a href="{{route("registro_zona")}}" class="opcion row">
            <span class="funciones col-8">Zonas</span>
            <x-icon name="map" class="icono col"/>
        </a>
        <a href="{{route("estadisticas_cuentas")}}" class="opcion row">
            <span class="funciones col-8">Estadísticas</span>
            <x-icon name="analytics" class="icono col"/>
        </a>
    @endif
    <a class="opcion row" href="{{route("venta")}}">
        <span class="funciones col-8">Pre-Venta</span>
        <x-icon name="shopping_cart" class="icono col"/>
    </a>
    <a href="{{route("venta_rapida")}}" class="opcion row">
        <span class="funciones col-8">Venta rapida</span>
        <x-icon name="shopping_cart_checkout" class="icono col"/>
    </a>
    <a href="{{route("saldos")}}" class="opcion row">
        <span class="funciones col-8">Cobranza</span>
        <x-icon name="payments" class="icono col"/>
    </a>
    <a href="{{route("transferir_lote")}}" class="opcion row">
        <span class="funciones col-8">Transferir lote</span>
        <x-icon name="swap_horiz" class="icono col"/>
    </a>
    <a class="opcion row" href="{{route("registro_gasto")}}">
        <span class="funciones col-8">Cuentas</span>
        <x-icon name="monetization_on" class="icono col"/>
    </a>
    <a class="opcion row" href="{{route("lista_reporte")}}">
        <span class="funciones col-8">Lista</span>
        <x-icon name="receipt_long" class="icono col"/>
    </a>
    <a class="opcion row" href="{{route("estado_cuentas.index")}}">
        <span class="funciones col-8">Estado de cuentas</span>
        <x-icon name="balance" class="icono col"/>
    </a>
    <a class="opcion row" href="{{route("conciliacion")}}">
        <span class="funciones col-8">Conciliación</span>
        <x-icon name="link" class="icono col"/>
    </a>
</div>

@endsection

