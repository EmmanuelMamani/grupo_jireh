
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

<h3>Reporte diario</h3>
<table id="tabla" class="table ">
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Fecha</th>
        <th>Monto</th>
        <th>Detalle</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($usuarios as $usuario )
        @foreach ($cuentas as $cuenta )
          @if ($usuario->id == $cuenta->user_id)
            <tr class="fila">
              <td>{{$usuario->Nombre}}</td>
              <td>{{$cuenta->Fecha}}</td>
              <td>{{$cuenta->Monto}}</td>
              <td>{{$cuenta->Detalle}}</td>
            </tr>
          @endif
        @endforeach
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
</body>
</html>

