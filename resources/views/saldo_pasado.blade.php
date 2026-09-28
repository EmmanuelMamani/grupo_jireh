@extends("header")
@section("titulo", "Grupo JIREH")
@section("opciones")
<a href="{{route("menu")}}"  class="opciones_head">Inicio</a>
<a href="{{route('saldos')}}" class="opciones_head">Cobranza</a>
@if (Auth::user()->Rol=='Administrador')
<a href="{{route('saldo_pasado')}}" class="opciones_head">C. Pasados</a>
@endif
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
    <form id="form_saldo_pasado" method="POST" action="{{route('saldo_pasado')}}" class="space-y-4">
      @csrf
      <h3 class="text-xl font-bold text-slate-800">Deudas pasadas</h3>
      <div>
        <x-ui-label for="zona">Zonas</x-ui-label>
        <x-ui-select name="" id="zona" onchange="cambio()">
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
          <option value="">Seleccionar Cliente</option>
          @foreach ($clientes as $cliente )
            @if ($cliente->zona_id == $zonas->first()->id)
              <option class="cliente" value="{{$cliente->id}}">{{$cliente->Nombre}}</option>
            @endif
          @endforeach
        </x-ui-select>
      </div>
      <div>
        <x-ui-label for="monto">Monto</x-ui-label>
        <x-ui-input type="text" name="monto" id="monto" inputmode="decimal" autocomplete="off" value="{{old('monto')}}"/>
        <x-ui-error field="monto"/>
      </div>
      <div>
        <x-ui-label for="motivo">Motivo de deuda</x-ui-label>
        <x-ui-input type="text" name="motivo" id="motivo" autocomplete="off" value="{{old('motivo')}}"/>
        <x-ui-error field="motivo"/>
      </div>
      <div class="grid grid-cols-2 gap-3 pt-1" id="cont_btn">
        <x-ui-ghost href="{{route('menu')}}" id="cancelar">Cancelar</x-ui-ghost>
        <x-ui-primary id="enviar">Registrar</x-ui-primary>
      </div>
    </form>
  </x-ui-card>
</div>
<script>
    function cambio(){
        var zona = document.getElementById('zona').value
        var cliente= document.getElementById('cliente')
        cliente.innerHTML="<option >Seleccionar Cliente</option>"
        @foreach ($clientes as $cliente )
            if({{$cliente->zona_id}} == zona){
                cliente.innerHTML+='<option class="cliente" value="{{$cliente->id}}">{{$cliente->Nombre}}</option>'
            }
        @endforeach
    }
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
