
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
      <th>Unid.</th>
      <th>Peso</th>
      <th>Costo U.</th>
      <th>Monto</th>
    </tr>
  </thead>
  <tbody>
    @php
      $sumatoria=0;
      $sum_moldes=0;
      $sum_peso=0;
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
        <td>{{$venta->salida->Total}} Bs</td>
      </tr>
      @php
        $sumatoria+=$venta->salida->Total;
        $sum_moldes+=$venta->salida->CantMoldes;
        $sum_peso+=$venta->salida->Peso;
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
    <tr>
      <td></td>
      <td>Totales:</td>
      <td></td>
      <td>{{$sum_moldes}}</td>
      <td>{{$sum_peso}} Kg.</td>
      <td></td>
      <td>{{$sumatoria}} Bs.</td>
    </tr>
  </tbody>
</table>
<h4>Producto: {{$producto->Nombre}}</h4>
<h4>Proveedor: {{$lote->Proveedor}}</h4>
<h4>Moldes del Lote: {{$lote->CantMoldes}}</h4>
<h4>Entrega del lote: {{$lote->created_at->format('Y-m-d')}}</h4>
<style>
  h4{
    margin-left: 2%
  }
</style>
</body>
</html>

