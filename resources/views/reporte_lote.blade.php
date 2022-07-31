@extends("header")
@section("titulo","Grupo JIREH")
@section("estilos")
<link rel="stylesheet" href="{{asset("css/reporte.css")}}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css">
@endsection
@section("contenido")
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
        <th>Unidades vendidas</th>
        <th>Ganancias</th>
      </tr>
    </thead>
    <tbody>
        <tr class="fila">
            <td>1</td>
            <td>Marko Quispe</td>
            <td>Mozzarella por kilo</td>
            <td>120</td>
            <td>5530</td>
            <td>26</td>
            <td>143780</td>
            <td>10</td>
            <td>54600</td>
        </tr>
        <tr class="fila">
            <td>2</td>
            <td>Mario Rosa</td>
            <td>Mozzarella por pepa</td>
            <td>100</td>
            <td>-</td>
            <td>26</td>
            <td>2600</td>
            <td>15</td>
            <td>420</td>
        </tr>
    </tbody>
  </table>
  <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>
  <script>
         $('#tabla').DataTable({
      responsive:true,
      autoWidth:false,
      "language": {
            "lengthMenu": "Mostrar _MENU_  ",
            "zeroRecords": "No hay resultados",
            "info": "Mostrando la página _PAGE_ de _PAGES_",
            "infoEmpty": "No records available",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "search":"Buscar",
            "paginate":{
                  "next":"Siguiente",
                  "previous":"Anterior"
            }
        }
      });
</script>
@endsection