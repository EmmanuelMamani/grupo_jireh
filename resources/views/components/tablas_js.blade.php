{{-- JS DataTables compartido + defaults globales (español, responsive,
  deferRender, 25 filas). Los init por vista heredan esto; solo agregan
  lo propio de cada tabla. --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script>
if (window.jQuery && jQuery.fn.dataTable) {
  jQuery.extend(jQuery.fn.dataTable.defaults, {
    responsive: true,
    autoWidth: false,
    deferRender: true,
    pageLength: 25,
    language: {
      lengthMenu: "Mostrar _MENU_  ",
      zeroRecords: "No hay resultados",
      info: "Mostrando la página _PAGE_ de _PAGES_",
      infoEmpty: "No records available",
      infoFiltered: "(filtrado de _MAX_ registros totales)",
      search: "Buscar",
      paginate: {
        next: "Siguiente",
        previous: "Anterior"
      }
    }
  });
}
</script>
