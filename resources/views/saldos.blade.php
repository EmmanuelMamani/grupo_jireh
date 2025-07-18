@extends("header")
@section("titulo","Grupo JIREH")
@section("opciones")
<a href="{{route("menu")}}"  class="opciones_head">Inicio</a>
<a href="/saldos" class="opciones_head">Cobranza</a>
@if (Auth::user()->Rol=='Administrador')
<a href="/saldo_pasado" class="opciones_head">C. Pasados</a>
@endif
@endsection
@section("estilos")
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@section("contenido")
<form id="formulario" action="{{route("saldos")}}" method="POST">
    @csrf
    <h3>Cobranza</h3>
    <label class="form-label">Zonas:</label>
    <select name="" id="zona" class="form-select" onchange="cambio()">
        <option> Selecciona una zona </option>
        @foreach ($zonas as $zona)
            <option value="{{$zona->id}}">{{$zona->Nombre}}</option>
        @endforeach
    </select>
    <label for="" class="form-label">Buscar Cliente:</label>
    <input type="text" id="buscar" class="form-control"><br>
    <label class="form-label">Cliente:</label>
    <select name="cliente" id="cliente" class="form-select">
        <option value="">Seleccionar Cliente</option>
    </select>
    <label class="form-label">Monto a pagar:</label>
    <input type="text" name="monto" id="monto" class="form-control"  value="{{old('monto')}}">
    <label class="form-label">Ver compras:</label><br>
    <a href="#" class="btn btn-warning" id="compras">Kardex</a>
    @if ($errors->has('monto'))
    <span class="error text-danger">{{ $errors->first('monto') }}</span>
    @endif <br>
    <div class="row" id="cont_btn">
        <div class="col"><a href="/menu" id="cancelar">Cancelar</a></div>
        <div class="col"><button id="enviar">Pagar</button></div>
    </div>
</form>
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
                    let saldo = cliente.saldos[cliente.saldos.length - 1]?.Saldo ?? 0;
                    clienteSelect.innerHTML += `<option class="cliente" value="${cliente.id}">${cliente.Nombre} Debe: ${saldo} Bs</option>`;
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
    var carga=document.getElementById("contenedor_carga");
    var enviar=document.getElementById("enviar");
    enviar.onclick=function(){
       carga.style.visibility="visible";
    }
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
    });
</script>

@endsection
