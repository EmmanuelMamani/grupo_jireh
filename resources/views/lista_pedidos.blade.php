@extends("header")
@section("titulo","Grupo JIREH")
@section("opciones")
<a href="{{route("menu")}}" class="opciones_head">Inicio</a>
<a href="{{route("lista_reporte")}}" class="opciones_head">Lista</a>
<a href="{{route("registro_lista")}}" class="opciones_head">Registro</a>
@endsection
@section("estilos")
<script src="https://cdn.tailwindcss.com"></script>
@endsection
@push('head-scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
@endpush
@section("contenido")
<div class="max-w-xl mx-auto px-3 py-4">
  <x-ui-card>
    <form action="{{route("registro_lista")}}" method="post" id="form_pedido" class="space-y-4">
      @csrf
      <h3 class="text-xl font-bold text-slate-800">Registrar pedido</h3>
      <div>
        <x-ui-label for="zona">Zona</x-ui-label>
        <x-ui-select name="zona" id="zona">
          <option>Elige una zona</option>
          @foreach ($zonas as $zona)
            <option value="{{$zona->id}}">{{$zona->Nombre}}</option>
          @endforeach
        </x-ui-select>
      </div>
      <div>
        <x-ui-label for="buscar">Buscar cliente</x-ui-label>
        <x-ui-input type="text" id="buscar" autocomplete="off"/>
      </div>
      <div>
        <x-ui-label for="cliente">Cliente</x-ui-label>
        <x-ui-select name="cliente" id="cliente">
          <option>Elige un cliente</option>
          @foreach ($clientes as $cliente )
            <option class="cliente" value="{{$cliente->id}}" @if(old('cliente') == $cliente->id ) selected @endif>{{$cliente->Nombre}}</option>
          @endforeach
        </x-ui-select>
        <x-ui-error field="cliente"/>
      </div>
      <div>
        <x-ui-label for="producto">Producto</x-ui-label>
        <x-ui-select name="producto" id="producto">
          <option>Elige un producto</option>
          @foreach ($productos as $producto )
            <option value="{{$producto->id}}" @if(old('producto') == $producto->id ) selected @endif>{{$producto->Nombre}}</option>
          @endforeach
        </x-ui-select>
        <x-ui-error field="producto"/>
      </div>
      <div>
        <x-ui-label for="unidades">Unidades</x-ui-label>
        <x-ui-input type="text" name="unidades" id="unidades" inputmode="numeric" autocomplete="off" value="{{old("unidades")}}"/>
        <x-ui-error field="unidades"/>
      </div>
      <div class="grid grid-cols-2 gap-3 pt-1" id="cont_btn">
        <x-ui-ghost href="{{route('menu')}}" id="cancelar">Cancelar</x-ui-ghost>
        <x-ui-primary id="enviar">Agregar</x-ui-primary>
      </div>
    </form>
  </x-ui-card>
</div>
<script>
  var zona=document.getElementById("zona");
  zona.addEventListener('change',(event)=>{
      var zona_id=zona.options[zona.selectedIndex].value;
      var cliente=document.getElementById("cliente");
      cliente.innerHTML="<option>Elige un cliente</option>";
      @foreach ($clientes as $cliente)
          if(zona_id=={{$cliente->zona_id}}){
              cliente.innerHTML+="<option class='cliente' value='{{$cliente->id}}'>{{$cliente->Nombre}}</option>";
          }
      @endforeach
  });
</script>
<script>
  $(document).ready(function() {
    $('#buscar').on('input', function() {
      var textoBuscado = $(this).val().toLowerCase();
      $('.cliente').each(function() {
        var textoOpcion = $(this).text().toLowerCase();
        if (textoOpcion.includes(textoBuscado)) {
          $(this).show();
        } else {
          $(this).hide();
        }
      });
    });
  });
</script>
@endsection
