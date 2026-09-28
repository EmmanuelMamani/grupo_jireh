@extends("header")
@section("titulo", "Grupo JIREH")
@section("opciones")
<a href="{{route('menu')}}" class="opciones_head">Inicio</a>
<a href="{{route('registro_cliente')}}" class="opciones_head">Registro</a>
<a href="{{route('reporte_cliente')}}" class="opciones_head">Reporte</a>
@if (Auth::user()->Rol=='Administrador')

@endif
@endsection
@section("estilos")
<script src="https://cdn.tailwindcss.com"></script>
@endsection
@section("contenido")
<div class="max-w-xl mx-auto px-3 py-4">
  <x-ui-card>
    <form id="form_cliente" method="POST" action="{{route('registro_cliente')}}" class="space-y-4">
      @csrf
      <h3 class="text-xl font-bold text-slate-800">Registro de cliente</h3>
      <div>
        <x-ui-label for="nombre">Nombre completo</x-ui-label>
        <x-ui-input type="text" name="nombre" id="nombre" value="{{old('nombre')}}" autofocus autocomplete="off"/>
        <x-ui-error field="nombre"/>
      </div>
      <div>
        <x-ui-label for="telefono">Teléfono</x-ui-label>
        <x-ui-input type="tel" name="telefono" id="telefono" value="{{old('telefono')}}" autocomplete="off"/>
        <x-ui-error field="telefono"/>
      </div>
      <div>
        <x-ui-label for="direccion">Dirección</x-ui-label>
        <x-ui-input type="text" name="direccion" id="direccion" value="{{old('direccion')}}" autocomplete="off"/>
        <x-ui-error field="direccion"/>
      </div>
      <div>
        <x-ui-label for="zona">Zona</x-ui-label>
        <x-ui-select name="zona" id="zona">
          @foreach ($zonas as $zona)
            <option value="{{$zona->id}}">{{$zona->Nombre}}</option>
          @endforeach
        </x-ui-select>
      </div>
      <div class="grid grid-cols-2 gap-3 pt-1">
        <x-ui-ghost href="{{route('menu')}}">Cancelar</x-ui-ghost>
        <x-ui-primary>Registrar</x-ui-primary>
      </div>
    </form>
  </x-ui-card>
  @if (session('registrar') == 'ok')
  <div class="grid grid-cols-2 gap-3 mt-3" id="post_acciones">
    <button type="button" id="otro_cliente" class="w-full rounded-xl border border-slate-300 text-slate-700 py-3 font-medium">Registrar otro</button>
    @if (Auth::user()->Rol == 'Administrador')
    <a href="{{route('reporte_cliente')}}" class="w-full rounded-xl text-white py-3 font-medium text-center" style="background-color:#125149">Ver reporte</a>
    @endif
  </div>
  <script>
    (function(){
      var nombre = document.getElementById('nombre');
      var otro = document.getElementById('otro_cliente');
      if (otro && nombre) {
        otro.addEventListener('click', function(){
          nombre.focus();
          nombre.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
      }
    })();
  </script>
  @endif
</div>
@endsection
