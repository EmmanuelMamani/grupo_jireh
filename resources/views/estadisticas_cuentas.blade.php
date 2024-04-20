@extends("header")
@section("titulo","Grupo JIREH")
@section("opciones")
<a href="{{route("menu")}}" class="opciones_head">Inicio</a>
@endsection
@section("estilos")
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@5.1.0/dist/apexcharts.min.css">
@endsection
@section("contenido")
<h3 class=" my-2 text-2xl">Control de Gastos</h3>

<div class="w-11/12 mx-auto">
    <div id="chart"></div>
</div>
@foreach ($results as $result)
    
@endforeach
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    var almuerzo=[]
    var desayuno=[]
    var gasolina=[]
    var diesel=[]
    var transporte=[]
    var aceite=[]
    var labels=[]
    @foreach ($results as $result)
        labels.push('{{$result->mes}} - {{$result->año}}')
        almuerzo.push({{$result->almuerzo}})
        desayuno.push({{$result->desayuno}})
        gasolina.push({{$result->gasolina}})
        diesel.push({{$result->diesel}})
        transporte.push({{$result->transporte}})
        aceite.push({{$result->aceite}})
    @endforeach
    var options = {
          series: [
            {name: "almuerzo",
             data: almuerzo},
            {name: "desayuno",
             data: desayuno},
             {name: "gasolina",
             data: gasolina},
             {name: "diesel",
             data: diesel},
             {name: "transporte",
             data: transporte},
             {name: "cambio aceite",
             data: aceite}
            ],
          chart: {
          height: 600,
          type: 'line',
          zoom: {
            enabled: false
          }
        },
        dataLabels: {
          enabled: true
        },
        stroke: {
          curve: 'straight'
        },
        title: {
          text: 'Product Trends by Month',
          align: 'left'
        },
        grid: {
          row: {
            colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
            opacity: 0.5
          },
        },
        xaxis: {
          categories: labels,
        }
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
      
</script>
@endsection