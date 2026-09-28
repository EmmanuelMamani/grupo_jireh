@extends("header")
@section("titulo", "Grupo JIREH")
@section("opciones")
<a href="{{route("menu")}}" class="opciones_head">Inicio</a>
<a href="{{route("registro_lote")}}" class="opciones_head">Registro</a>
<a href="{{route("reporte_lotes")}}" class="opciones_head">Reporte</a>
@endsection
@section("estilos")
<script src="https://cdn.tailwindcss.com"></script>
@endsection
@section("contenido")
<div class="max-w-xl mx-auto px-3 py-4">
  <x-ui-card>
    <form id="form_lote" method="POST" action="{{route('registro_lote')}}" class="space-y-4">
      @csrf
      <h3 class="text-xl font-bold text-slate-800">Nuevo lote</h3>
      <div>
        <x-ui-label for="proveedor">Nombre del proveedor</x-ui-label>
        <x-ui-input type="text" name="proveedor" id="proveedor" autocomplete="off" value="{{old('proveedor')}}"/>
        <x-ui-error field="proveedor"/>
      </div>
      <div>
        <x-ui-label for="producto">Producto</x-ui-label>
        <x-ui-select name="producto" id="producto">
          @foreach ($productos as $producto )
            <option value="{{$producto->id}}" @if (old('producto')== $producto->id)  selected @endif>{{$producto->Nombre}} {{$producto->Tipo}}</option>
          @endforeach
        </x-ui-select>
      </div>
      <div>
        <x-ui-label for="moldes">Cantidad de moldes</x-ui-label>
        <x-ui-input type="text" name="moldes" id="moldes" inputmode="numeric" autocomplete="off" value="{{old('moldes')}}"/>
        <x-ui-error field="moldes"/>
      </div>
      <div>
        <x-ui-label for="peso">Peso total</x-ui-label>
        <x-ui-input type="text" name="peso" id="peso" inputmode="decimal" autocomplete="off" value="{{ old('peso', '0.00') }}"/>
        <x-ui-error field="peso"/>
      </div>
      <div>
        <x-ui-label for="costo">Costo por kilo o unidad</x-ui-label>
        <x-ui-input type="text" name="costo" id="costo" inputmode="decimal" autocomplete="off" value="{{old('costo')}}"/>
        <x-ui-error field="costo"/>
      </div>
      <div class="grid grid-cols-2 gap-3 pt-1" id="cont_btn">
        <x-ui-ghost href="{{route('menu')}}" id="cancelar">Cancelar</x-ui-ghost>
        <x-ui-primary id="enviar">Registrar</x-ui-primary>
      </div>
    </form>
  </x-ui-card>
</div>
@endsection
