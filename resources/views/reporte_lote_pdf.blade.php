
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

<h3>Reporte de lotes</h3>
<table id="tabla" class="table ">
    <thead>
      <tr>
        <th>#</th>
        <th>Proveedor</th>
        <th>Producto</th>
        <th>Unidades</th>
        <th>Peso total</th>
        <th>Precio unitario</th>
        <th>Precio total</th>
        <th>Ganancia de ventas</th>
        <th>Unidades vendidas</th>
        <th>Merma</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($lotes as $key=>$lote)
        <tr class="fila">
          <td>{{$key+1}}</td>
          <td>{{$lote->Proveedor}}</td>
          <td>{{$lote->producto->Nombre}} {{$lote->producto->Tipo}}</td>
          <td>{{$lote->CantMoldes}}</td>
          <td>{{$lote->Peso}} Kg</td>
          <td>{{$lote->Precio}} Bs</td>
          @if($lote->producto->Tipo=="Por Kilo")
            <td>{{$lote->Peso * $lote->Precio}} Bs</td>
            <td>{{$lote->salidas->sum('Total')-$lote->Peso * $lote->Precio}} Bs</td>
          @else
            <td>{{$lote->CantMoldes * $lote->Precio}} Bs</td>
            <td>{{$lote->salidas->sum('Total')-$lote->CantMoldes * $lote->Precio}} Bs</td>
          @endif
          <td>{{$lote->salidas->sum('CantMoldes')}}</td>
          @if ($lote->merma==null)
          <td>Sin merma</td>
          @else
          <td>{{$lote->merma->CantMerma}} Kg</td>
          @endif
          
        </tr>
      @endforeach
    <style>

body{
  margin:  2px;
  border: black solid 1px;
}
table {
  width: 98%;
  border: 1px solid black;
  text-align: center;
  border-collapse: collapse;
  margin-left: 1%;
  align: center ;
}
  td, th {
    padding: 15px;
    font-size: 11px;
  }
  thead{
    background: rgb(233, 233, 233);
    font-size: 11px;

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
</body>
</html>

