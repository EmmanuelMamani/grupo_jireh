@extends("header")
@section("titulo","Grupo JIREH")
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
    <form id="form_cobranza" action="{{route("saldos")}}" method="POST" class="space-y-4">
      @csrf
      <h3 class="text-xl font-bold text-slate-800">Cobranza</h3>
      <div>
        <x-ui-label for="zona">Zonas</x-ui-label>
        <x-ui-select name="" id="zona" onchange="cambio()">
          <option> Selecciona una zona </option>
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
        </x-ui-select>
      </div>
      <div>
        <x-ui-label for="venta_id">Imputar a venta (opcional)</x-ui-label>
        <x-ui-select name="venta_id" id="venta_id">
          <option value="">Automático (la más antigua primero)</option>
        </x-ui-select>
        <x-ui-error field="venta_id"/>
      </div>
      <div>
        <x-ui-label for="monto">Monto a pagar</x-ui-label>
        <x-ui-input type="text" name="monto" id="monto" inputmode="decimal" autocomplete="off" value="{{old('monto')}}"/>
        <x-ui-error field="monto"/>
      </div>
      <div>
        <x-ui-label>Ver compras</x-ui-label>
        <a href="#" class="inline-block rounded-xl border border-slate-300 text-slate-700 px-4 py-2 text-sm font-medium" id="compras">Kardex</a>
      </div>
      <div class="grid grid-cols-2 gap-3 pt-1" id="cont_btn">
        <x-ui-ghost href="{{route('menu')}}" id="cancelar">Cancelar</x-ui-ghost>
        <x-ui-primary id="enviar">Pagar</x-ui-primary>
      </div>
    </form>
  </x-ui-card>
</div>
<script>
    function cambio() {
        const zonaId = document.getElementById('zona').value;
        const clienteSelect = document.getElementById('cliente');
        clienteSelect.innerHTML = '<option value="">Cargando clientes...</option>';

        fetch(`/clientes-por-zona/${zonaId}`)
            .then(res => res.json())
            .then(clientes => {
                clienteSelect.innerHTML = '<option value="">Seleccionar Cliente</option>';
                clientes.forEach(cliente => {
                    clienteSelect.innerHTML += `<option class="cliente" value="${cliente.id}">${cliente.nombre} Debe: ${cliente.saldo} Bs</option>`;
                });
            })
            .catch(error => {
                clienteSelect.innerHTML = '<option>Error al cargar</option>';
                console.error("Error:", error);
            });
    }
</script>
<script>
    $(document).ready(function() {
        $('#buscar').on('input', function() {
            var textoBuscado = $(this).val().toLowerCase();
            $('.cliente').each(function() {
                var textoOpcion = $(this).text().toLowerCase();
                $(this).toggle(textoOpcion.includes(textoBuscado));
            });
        });
    });
</script>
<script>
    $('#cliente').change(function(){
        var nuevoHref = "{{ route('ventas_periodo', ['id' => ':idCliente']) }}";
        var idCliente = $(this).val();

        if(idCliente != ''){
            nuevoHref = nuevoHref.replace(':idCliente', idCliente);
        } else {
            nuevoHref = "#";
        }

        $('#compras').attr('href', nuevoHref);

        var ventaSelect = $('#venta_id');
        ventaSelect.html('<option value="">Automático (la más antigua primero)</option>');
        if(idCliente != ''){
            fetch(`/ventas-pendientes-cliente/${idCliente}`)
                .then(res => res.json())
                .then(ventas => {
                    ventas.forEach(v => {
                        ventaSelect.append(`<option value="${v.id}">Venta #${v.id} · ${v.fecha} · debe Bs ${v.pendiente}</option>`);
                    });
                })
                .catch(error => {
                    console.error("Error:", error);
                });
        }
    });
</script>
<script>
    // Deep-link desde reporte_cliente (?cliente=&zona=): preselecciona y carga.
    (function(){
        var params = new URLSearchParams(window.location.search);
        var zonaId = params.get('zona');
        var clienteId = params.get('cliente');
        if(!zonaId && !clienteId) return;
        if(zonaId){
            var zonaSel = document.getElementById('zona');
            if(zonaSel.querySelector('option[value="' + zonaId + '"]')){
                zonaSel.value = zonaId;
                cambio();
            }
        }
        if(clienteId){
            var intentos = 0;
            var iv = setInterval(function(){
                var cli = document.getElementById('cliente');
                var opt = cli ? cli.querySelector('option[value="' + clienteId + '"]') : null;
                intentos++;
                if(opt || intentos > 40){
                    clearInterval(iv);
                    if(opt){
                        cli.value = clienteId;
                        cli.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                }
            }, 150);
        }
    })();
</script>
@endsection
