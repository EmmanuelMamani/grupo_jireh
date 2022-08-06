@extends("header")
@section("titulo", "Grupo JIREH")
@section("estilos")
<link rel="stylesheet" href="{{asset("css/formulario.css")}}">
@endsection
@section("contenido")
<form id="formulario" method="POST" action="{{route('saldo_pasado')}}">
    @csrf
    <h3>Deudas pasadas</h3>
    <label class="form-label">Cliente:</label>
    <select name="cliente" id="cliente" class="form-select">
        @foreach ($clientes as $cliente )
            <option value="{{$cliente->id}}">{{$cliente->Nombre}}</option>
        @endforeach
    </select>
    <Label class="form-label">Monto:</Label>
    <input type="text" class="form-control" name="monto">
    @if ($errors->has('monto'))
    <span class="error text-danger">{{ $errors->first('monto') }}</span>
    @endif <br>
    <label class="form-label">Motivo de deuda:</label>
    <input type="text" class="form-control" name="motivo">
    @if ($errors->has('motivo'))
    <span class="error text-danger">{{ $errors->first('motivo') }}</span>
    @endif <br>
    <div class="row" id="cont_btn">
        <div class="col"><button id="cancelar">Cancelar</button></div>
        <div class="col"><button id="enviar">Registrar</button></div>
    </div>
</form>
@endsection