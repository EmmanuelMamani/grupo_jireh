@extends("header")
@section("titulo", "Grupo JIREH")
@section("opciones")
<a href="{{route("menu")}}" class="opciones_head">Inicio</a>
<a href="{{route("registro_gasto")}}" class="opciones_head">Registro</a>
<a href="{{route("reporte_diario")}}" class="opciones_head">Reporte</a>
@if (Auth::user()->Rol=='Administrador')
<a href="{{route("reporte_cuenta")}}" class="opciones_head">R. Total</a>
<a href="{{route("cuentas_periodo")}}" class="opciones_head">R. Periodo</a>
<a href="{{route("reporte_historico")}}" class="opciones_head">R Historico</a>
@endif
@endsection
@section("estilos")
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@section("contenido")
<form id="formulario" method="POST" action="{{route("registro_gasto")}}">
    @csrf
    <h3 id="titulo_formulario">Registro de gasto</h3>
    <label>gasto/ingreso</label><br>
    <input type="checkbox" name="cuenta" id="cuenta" value=-1><br>
    <label class="form-label" id="titulo_monto">Monto gastado:</label>
    <input type="text" class="form-control" name="monto"  value="{{old('monto')}}">
    @if ($errors->has('monto'))
    <span class="error text-danger">{{ $errors->first('monto') }}</span>
    @endif <br>
    <label class="form-label" id="titulo_detalle">Detalle del gasto:</label>
    <input type="text" class="form-control" name="detalle"  value="{{old('detalle')}}">
    @if ($errors->has('detalle'))
    <span class="error text-danger">{{ $errors->first('detalle') }}</span>
    @endif <br>
    <div class="row" id="cont_btn">
        <div class="col"><a href="{{route("menu")}}" id="cancelar">Cancelar</a></div>
        <div class="col"><button id="enviar">Registrar</button></div>
    </div>
</form>
<script>
    var cuenta=document.getElementById("cuenta")
    var titulo=document.getElementById("titulo_formulario")
    var monto=document.getElementById("titulo_monto")
    var detalle =document.getElementById("titulo_detalle")
    cuenta.onclick=function(){
        if(cuenta.value==-1){
            cuenta.value=1
            titulo.innerHTML="Registro de ingreso"
            monto.innerHTML="Monto ingresado"
            detalle.innerHTML="Detalle del ingreso"
        }else{
            cuenta.value=-1
            titulo.innerHTML="Registro de gasto"
            monto.innerHTML="Monto gastado"
            detalle.innerHTML="Detalle del gasto"
        }
    }
</script>
@endsection