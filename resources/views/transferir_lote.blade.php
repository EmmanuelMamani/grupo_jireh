@extends("header")
@section("titulo","Grupo JIREH")
@section("estilos") 
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@section("opciones")
<a href="{{route("menu")}}" class="opciones_head">Inicio</a>
@endsection
@section("contenido")
<form id="formulario" method="POST" action="{{route("transferir_lote")}}">
    @csrf
    <h3>Transferir lote</h3>
    <label class="form-label">Lote:</label>
    <select name="lote" id="lote" class="form-select">
        @foreach ($asignaciones as $asignacion )
            @if ($asignacion->ingreso->Activo == 1)
            <option value="{{$asignacion->id}}">{{$asignacion->ingreso->Proveedor}} {{$asignacion->ingreso->created_at->format('Y-m-d')}} Producto:{{$asignacion->ingreso->producto->Nombre}} Lote: {{$asignacion->ingreso->CantMoldes}} Unidades:{{$asignacion->CantMoldes}} </option>
            @endif
        @endforeach
    </select>
    <label class="form-label">Transferir a:</label>
    <select name="receptor" id="transferido" class="form-select">
            @foreach ($usuarios as $usuario)
                <option value="{{$usuario->id}}">{{$usuario->Nombre}}</option>
            @endforeach
    </select>
    <label class="form-label">Cantidad de moldes:</label>
    <input type="number" name="cantidad_moldes" class="form-control" id="moldes">
    <p id="alerta"></p>
    <div class="row" id="cont_btn">
        <div class="col"><a href="/menu" id="cancelar">Cancelar</a></div>
        <div class="col"><button id="enviar">Transferir</button></div>
    </div>
</form>
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
            if(parseInt(moldes.value)<1){
                alerta.innerHTML="Los moldes deben ser mayor a 0"
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
