@extends("header")
@section("titulo", "Grupo JIREH")
@section("opciones")
<a href="{{route("reporte_lotes")}}" class="opciones_head" id="flecha" aria-label="Volver"><x-icon name="arrow_back"/></a>
@endsection
@section("estilos")
<script src="https://cdn.tailwindcss.com"></script>
@endsection
@section("contenido")
<div class="max-w-xl mx-auto px-3 py-4">
  <x-ui-card>
    <form id="form_editar_lote" method="POST" action="{{route("editar_lote",["id"=>$lote->id])}}" class="space-y-4">
      @csrf
      <h3 class="text-xl font-bold text-slate-800">Editar lote</h3>
      <div>
        <x-ui-label for="proveedor">Nombre del proveedor</x-ui-label>
        <x-ui-input type="text" name="proveedor" id="proveedor" autocomplete="off" value="{{$lote->Proveedor}}" readonly/>
        <x-ui-error field="proveedor"/>
      </div>
      <div>
        <x-ui-label for="producto">Producto</x-ui-label>
        <x-ui-select name="producto" id="producto">
          <option value="{{$lote->producto_id}}">{{$lote->producto->Nombre}} {{$lote->producto->Tipo}}</option>
        </x-ui-select>
      </div>
      <div>
        <x-ui-label for="moldes">Cantidad de moldes</x-ui-label>
        <x-ui-input type="text" name="moldes" id="moldes" inputmode="numeric" autocomplete="off" value="{{$lote->CantMoldes}}"/>
        <x-ui-error field="moldes"/>
      </div>
      <div>
        <x-ui-label for="peso">Peso total</x-ui-label>
        <x-ui-input type="text" name="peso" id="peso" inputmode="decimal" autocomplete="off" value="{{$lote->Peso}}"/>
        <x-ui-error field="peso"/>
      </div>
      <div>
        <x-ui-label for="costo">Costo por kilo o unidad</x-ui-label>
        <x-ui-input type="text" name="costo" id="costo" inputmode="decimal" autocomplete="off" value="{{$lote->Precio}}"/>
        <x-ui-error field="costo"/>
      </div>
      <div class="grid grid-cols-2 gap-3 pt-1" id="cont_btn">
        <x-ui-ghost href="{{route('reporte_lotes')}}" id="cancelar">Cancelar</x-ui-ghost>
        <x-ui-primary id="enviar">Modificar</x-ui-primary>
      </div>
    </form>
  </x-ui-card>
</div>
@endsection
