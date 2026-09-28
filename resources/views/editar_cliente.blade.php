@extends("header")
@section("titulo", "Grupo JIREH")
@section("opciones")
<a href="{{route('menu')}}" class="opciones_head">Inicio</a>
<a href="{{route('registro_cliente')}}" class="opciones_head">Registro</a>
@if (Auth::user()->Rol=='Administrador')
<a href="{{route('reporte_cliente')}}" class="opciones_head">Reporte</a>
@endif
@endsection
@section("estilos")
<script src="https://cdn.tailwindcss.com"></script>
@endsection
@section("contenido")
<div class="max-w-xl mx-auto px-3 py-4">
  <x-ui-card>
    <form id="form_editar_cliente" method="POST" action="{{route('editar_cliente',['id'=>$cliente->id])}}" enctype="multipart/form-data" class="space-y-4">
      @csrf
      <h3 class="text-xl font-bold text-slate-800">Editar cliente</h3>
      <div>
        <x-ui-label for="nombre">Nombre completo</x-ui-label>
        <x-ui-input type="text" name="nombre" id="nombre" autocomplete="off" value="{{$cliente->Nombre}}"/>
        <x-ui-error field="nombre"/>
      </div>
      <div>
        <x-ui-label for="telefono">Teléfono</x-ui-label>
        <x-ui-input type="tel" name="telefono" id="telefono" autocomplete="off" value="{{$cliente->Telefono}}"/>
        <x-ui-error field="telefono"/>
      </div>
      <div>
        <x-ui-label for="direccion">Dirección</x-ui-label>
        <x-ui-input type="text" name="direccion" id="direccion" autocomplete="off" value="{{$cliente->Direccion}}"/>
        <x-ui-error field="direccion"/>
      </div>
      <div>
        <x-ui-label for="zona">Zona</x-ui-label>
        <x-ui-select name="zona" id="zona">
          @foreach ($zonas as $zona )
            @if ($zona->id == $cliente->zona_id)
            <option value="{{$zona->id}}" selected>{{$zona->Nombre}}</option>
            @else
            <option value="{{$zona->id}}">{{$zona->Nombre}}</option>
            @endif
          @endforeach
        </x-ui-select>
      </div>
      <div>
        <x-ui-label for="mapa">Mapa</x-ui-label>
        <x-ui-input type="text" name="mapa" id="mapa" autocomplete="off"/>
      </div>
      <div>
        <x-ui-label for="tienda">Tienda</x-ui-label>
        @if ($cliente->tienda && Storage::disk('public')->exists($cliente->tienda))
          <div class="mb-2"><img src="{{ Storage::disk('public')->url($cliente->tienda) }}" width="200" class="rounded-xl"></div>
        @endif
        <x-ui-input type="file" name="tienda" id="tienda" accept="image/*"/>
        <x-ui-error field="tienda"/>
      </div>
      <div class="grid grid-cols-2 gap-3 pt-1" id="cont_btn">
        <x-ui-ghost href="{{route('menu')}}" id="cancelar">Cancelar</x-ui-ghost>
        <x-ui-primary id="enviar">Guardar</x-ui-primary>
      </div>
    </form>
  </x-ui-card>
</div>
@endsection
