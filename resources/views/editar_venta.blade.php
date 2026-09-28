@extends("header")
@section("titulo","Grupo JIREH")
@section("opciones")
<a href="{{route("menu")}}" class="opciones_head">Inicio</a>
<a href="{{route("venta")}}" class="opciones_head">Venta</a>
<a href="{{route("reporte_ventas")}}" class="opciones_head">Reporte</a>
<a href="{{route("ventas_pendientes")}}" class="opciones_head">Pendientes</a>
@endsection
@section("estilos")
<script src="https://cdn.tailwindcss.com"></script>
@endsection
@section("contenido")
<div class="max-w-xl mx-auto px-3 py-4">
  <x-ui-card>
    <form action="{{route('editar_venta',["id"=>$venta->id])}}" id="form_editar_venta" method="POST" class="space-y-4">
      <h3 class="text-xl font-bold text-slate-800">Pre-Venta</h3>
      @csrf
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="0" id="tipo" name="tipo">
        <label class="form-check-label" for="tipo">Redondear total a entero</label>
      </div>
      @if ($venta->cliente_id != null)
        <div>
          <x-ui-label for="cliente">Cliente</x-ui-label>
          <x-ui-select name="cliente" id="cliente">
            <option value={{"$venta->cliente->id"}}>{{$venta->cliente->Nombre}}</option>
          </x-ui-select>
          <x-ui-error field="cliente"/>
        </div>
      @endif
      <div>
        <x-ui-label for="producto">Producto</x-ui-label>
        <x-ui-select name="producto" id="producto">
          <option value="{{$venta->ingreso->producto_id}}">{{$venta->ingreso->producto->Nombre}} {{$venta->ingreso->producto->Tipo}}</option>
        </x-ui-select>
        <x-ui-error field="producto"/>
      </div>
      <div>
        <x-ui-label for="lote">Lote</x-ui-label>
        <x-ui-select name="lote" id="lote">
          <option value="$venta->ingreso_id">{{$venta->ingreso->Proveedor}}</option>
        </x-ui-select>
        <x-ui-error field="lote"/>
        <p id="cantidad_lotes" class="text-xs text-slate-500 mt-1"></p>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <x-ui-label for="cantidad_moldes">Cantidad de moldes</x-ui-label>
          <x-ui-input type="text" name="cantidad_moldes" id="cantidad_moldes" inputmode="numeric" autocomplete="off" value="{{$venta->salida->CantMoldes}}" readonly/>
          <x-ui-error field="cantidad_moldes"/>
        </div>
        <div>
          <x-ui-label for="peso">Peso total</x-ui-label>
          <x-ui-input type="text" name="peso" id="peso" inputmode="decimal" autocomplete="off" value="{{$venta->salida->Peso}}" readonly/>
          <x-ui-error field="peso"/>
        </div>
      </div>
      <div>
        <x-ui-label for="precio">Precio por kilo o unidad</x-ui-label>
        <x-ui-input type="text" name="precio" id="precio" inputmode="decimal" autocomplete="off" value="{{$venta->salida->Precio}}"/>
        <x-ui-error field="precio"/>
      </div>
      <div class="grid grid-cols-2 gap-3 pt-1" id="cont_btn">
        <x-ui-ghost href="{{route('menu')}}" id="cancelar">Cancelar</x-ui-ghost>
        <x-ui-primary id="enviar" type="submit">Guardar</x-ui-primary>
      </div>
    </form>
  </x-ui-card>
</div>
<script>
    var tipo=document.getElementById("tipo")
    tipo.onclick=function(){
        if(tipo.value=="0"){tipo.value="1"}else{tipo.value="0"}
    }
</script>
@endsection
