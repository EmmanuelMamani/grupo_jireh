
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="preconnect" href="https://fonts.googleapis.com" type='text/css'>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Gruppo&family=Space+Grotesk:wght@300&display=swap" rel="stylesheet" type='text/css'>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" type='text/css'/>
</head>
<body>
  
<h2>Grupo JIREH</h2>

<h3>Reporte de ventas</h3>
<table id="tabla">
  <thead>
    <tr>
      <th>#</th>
      <th>Cliente</th>
      <th>Empleado</th>
      <th>Unidades</th>
      <th>Peso</th>
      <th>Costo Unitario</th>
      <th>Monto</th>
      <th>Fecha</th>
    </tr>
  </thead>
  <tbody>
    @php
      $sumatoria=0;
    @endphp
    @foreach ($ventas as $key=>$venta)
      <tr class="fila">
        <td>{{$key+1}}</td>
        @if ($venta->cliente==null)
        <td>Sin nombre</td>
        @else
        <td>{{$venta->cliente->Nombre}}</td>
        @endif
        
        <td>{{$venta->user->Nombre}}</td>
        <td>{{$venta->salida->CantMoldes}}</td>
        <td>{{$venta->salida->Peso==''?'0':$venta->salida->Peso}} Kg</td>
        <td>{{$venta->salida->Precio}} Bs</td>
        <td>{{$venta->salida->Total}}</td>
        <td>{{$venta->created_at->format('Y-m-d')}}</td>
      </tr>
      @php
        $sumatoria+=$venta->salida->Total;
      @endphp
    @endforeach
    <style>

body{
  margin:  2px;
  border: black solid 1px;
}
table {
  width: 90%;
  border: 1px solid black;
  text-align: center;
  border-collapse: collapse;
  margin-left: 5%;
  align: center ;
}
  td, th {
    padding: 15px;
  }
  thead{
    background: rgb(233, 233, 233)
  }
th, td {
   border-bottom: 1px solid black;

}
h3{
  color:#757575;
  margin-left: 10px;
}
h2{
  color:#000000 ;
  text-align: center;
}

    </style>
    
  </tbody>
</table>
<h4>El total es de {{$sumatoria}} Bs.</h4>
<h4>Producto: {{$producto->Nombre}}</h4>
<style>
  h4{
    margin-left: 2%
  }
</style>
</body>
</html>

