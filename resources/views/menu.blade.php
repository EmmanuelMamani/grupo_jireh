@extends("header")
@section("opciones")
<a href="#" class="opciones_heald">salir</a>
@endsection
@section("estilos")
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
<link rel="stylesheet" href="{{asset("css/menu.css")}}">
@endsection
@section("titulo", "Menu")
@section("contenido")
<div id="opciones">
    @if (Auth::user()->Rol=='Administrador')
        <div class="opcion row">
            <span class="funciones col-8">Clientes</span>
            <span class="material-symbols-outlined icono col">group</span>
        </div>
        <div class="opcion row">
            <span class="funciones col-8">Empleados</span>
            <span class="material-symbols-outlined icono col"> business_center</span>
        </div>
        <div class="opcion row">
            <span class="funciones col-8">Cuentas</span>
            <span class="material-symbols-outlined icono col">monetization_on</span>
        </div>
        <div class="opcion row">
            <span class="funciones col-8">Lotes</span>
            <span class="material-symbols-outlined icono col">local_shipping</span>
        </div>
    @endif
   
    
    
    <a class="opcion row" href="/venta">
        <span class="funciones col-8">Pre-Venta</span>
        <span class="material-symbols-outlined icono col">shopping_cart</span>
    </a>
    <div class="opcion row">
        <span class="funciones col-8">Venta rapida</span>
        <span class="material-symbols-outlined icono col">shopping_cart_checkout</span>
    </div>
    <div class="opcion row">
        <span class="funciones col-8">Saldos</span>
        <span class="material-symbols-outlined icono col">payments</span>
    </div>
</div>
@endsection
