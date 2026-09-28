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
<script src="https://cdn.tailwindcss.com"></script>
@endsection
@section("contenido")
<div class="max-w-xl mx-auto px-3 py-4">
  <x-ui-card>
    <form id="form_gasto" method="POST" action="{{route("registro_gasto")}}" class="space-y-4">
      @csrf
      <h3 id="titulo_formulario" class="text-xl font-bold text-slate-800">Registro de gasto</h3>
      <div class="flex items-center gap-2">
        <input type="checkbox" name="cuenta" id="cuenta" value=-1 class="h-5 w-5 accent-[#125149]">
        <x-ui-label for="cuenta" style="margin-bottom:0">Es ingreso (en vez de gasto)</x-ui-label>
      </div>
      <div>
        <x-ui-label for="monto" id="titulo_monto">Monto gastado</x-ui-label>
        <x-ui-input type="text" name="monto" id="monto" inputmode="decimal" autocomplete="off" value="{{old('monto')}}"/>
        <x-ui-error field="monto"/>
      </div>
      <div>
        <x-ui-label for="detalle" id="titulo_detalle">Detalle del gasto</x-ui-label>
        <x-ui-input type="text" name="detalle" id="detalle" autocomplete="off" value="{{old('detalle')}}"/>
        <x-ui-error field="detalle"/>
      </div>
      <div class="grid grid-cols-2 gap-3 pt-1" id="cont_btn">
        <x-ui-ghost href="{{route('menu')}}" id="cancelar">Cancelar</x-ui-ghost>
        <x-ui-primary id="enviar">Registrar</x-ui-primary>
      </div>
    </form>
  </x-ui-card>
</div>
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
