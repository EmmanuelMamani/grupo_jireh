@extends("header")
@section("titulo","Grupo JIREH")
@section("estilos")
<script src="https://cdn.tailwindcss.com"></script>
@endsection
@section("opciones")
<a href="{{route("menu")}}" class="opciones_head">Inicio</a>
@endsection
@section("contenido")
<div class="max-w-xl mx-auto px-3 py-4">
  <x-ui-card>
    <form id="form_transferir" method="POST" action="{{route("transferir_lote")}}" class="space-y-4">
      @csrf
      <h3 class="text-xl font-bold text-slate-800">Transferir lote</h3>
      <div>
        <x-ui-label for="lote">Lote</x-ui-label>
        <x-ui-select name="lote" id="lote">
          @foreach ($asignaciones as $asignacion )
            @if ($asignacion->ingreso->Activo == 1)
            <option value="{{$asignacion->id}}">{{$asignacion->ingreso->Proveedor}} {{$asignacion->ingreso->created_at->format('Y-m-d')}} Producto:{{$asignacion->ingreso->producto->Nombre}} Lote: {{$asignacion->ingreso->CantMoldes}} Unidades:{{$asignacion->CantMoldes}} </option>
            @endif
          @endforeach
        </x-ui-select>
      </div>
      <div>
        <x-ui-label for="transferido">Transferir a</x-ui-label>
        <x-ui-select name="receptor" id="transferido">
          @foreach ($usuarios as $usuario)
            <option value="{{$usuario->id}}">{{$usuario->Nombre}}</option>
          @endforeach
        </x-ui-select>
      </div>
      <div>
        <x-ui-label for="moldes">Cantidad de moldes</x-ui-label>
        <x-ui-input type="number" name="cantidad_moldes" id="moldes" inputmode="numeric" autocomplete="off"/>
        <p id="alerta" class="text-sm text-red-600 mt-1"></p>
      </div>
      <div class="grid grid-cols-2 gap-3 pt-1" id="cont_btn">
        <x-ui-ghost href="{{route('menu')}}" id="cancelar">Cancelar</x-ui-ghost>
        <x-ui-primary id="enviar">Transferir</x-ui-primary>
      </div>
    </form>
  </x-ui-card>
</div>
<script>
    var enviar= document.getElementById("enviar");
    var lote=document.getElementById("lote");
    var moldes=document.getElementById("moldes");
    var alerta=document.getElementById("alerta");
    enviar.onclick=function(e){
        if(lote.innerHTML=="\n"){
            alerta.innerHTML="No hay lotes para transferir"
            e.preventDefault();
        }else{
            var almacen=parseInt(lote.options[lote.selectedIndex].text.split(":")[3])
        if(moldes.value.match('^[0-9]+$')!=null){
            if(almacen-parseInt(moldes.value)<0){
                alerta.innerHTML="No cuentas con esa cantidad de moldes"
                e.preventDefault();
            }
            if(alerta.innerHTML==""){
                var carga=document.getElementById("contenedor_carga");
                carga.style.visibility="visible";
            }
        }else{
            alerta.innerHTML="Debe ser un número"
            e.preventDefault();
        }
        }
    }
</script>
@endsection
