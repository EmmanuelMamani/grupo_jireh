@extends("header")
@section("titulo","Grupo JIREH")
@section("opciones")
<a href="{{route("menu")}}" class="opciones_head">Inicio</a>
@endsection
@section("estilos")
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@5.1.0/dist/apexcharts.min.css">
<style>
    .card-header-jireh { background-color: #125149; color: #fff; }
    .btn-jireh { background-color: #DA7922; border-color: #DA7922; color: #fff; }
    .btn-jireh:hover { background-color: #b8621a; border-color: #b8621a; color: #fff; }
    .kpi { border: none; border-top: 4px solid #125149; }
    .kpi-accent { border-top-color: #DA7922; }
    .kpi-icon {
        display: inline-flex; align-items: center; justify-content: center;
        width: 42px; height: 42px; border-radius: 50%;
        background-color: #e7f0ee; color: #125149; margin-bottom: 8px;
    }
    .kpi-accent-icon { background-color: #fbeedf; color: #DA7922; }
    .kpi-label { font-size: .8rem; text-transform: uppercase; letter-spacing: .05em; color: #6c757d; margin-bottom: 2px; }
    .kpi-value { font-weight: 700; margin-bottom: 0; }
    .table-jireh thead { background-color: #125149; color: #fff; }
    .cat-pill input:checked + label { background-color: #125149; border-color: #125149; color: #fff; }
</style>
@endsection
@section("contenido")
<div class="container-fluid px-3 px-md-4 pb-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between my-3">
        <h3 class="mb-1">Control de Gastos</h3>
        @if(!empty($desde) || !empty($hasta))
            <span class="badge rounded-pill" style="background-color:#125149;">
                {{ $desde ?? '...' }} al {{ $hasta ?? '...' }}
            </span>
        @else
            <span class="badge rounded-pill bg-secondary">Período completo</span>
        @endif
    </div>

    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="card kpi shadow-sm h-100">
                <div class="card-body">
                    <x-icon name="payments" class="kpi-icon"/>
                    <p class="kpi-label">Total general</p>
                    <h4 class="kpi-value">Bs {{ number_format($kpis['total'] ?? 0, 2, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card kpi kpi-accent shadow-sm h-100">
                <div class="card-body">
                    <x-icon name="workspace_premium" class="kpi-icon kpi-accent-icon"/>
                    <p class="kpi-label">Categoría top</p>
                    <h4 class="kpi-value">{{ $kpis['categoriaTop'] ?? '—' }}</h4>
                    <small class="text-muted">Bs {{ number_format($kpis['montoTop'] ?? 0, 2, ',', '.') }}</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card kpi shadow-sm h-100">
                <div class="card-body">
                    <x-icon name="calendar_month" class="kpi-icon"/>
                    <p class="kpi-label">Meses con datos</p>
                    <h4 class="kpi-value">{{ $kpis['meses'] ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card kpi kpi-accent shadow-sm h-100">
                <div class="card-body">
                    <x-icon name="trending_up" class="kpi-icon kpi-accent-icon"/>
                    <p class="kpi-label">Promedio mensual</p>
                    <h4 class="kpi-value">Bs {{ number_format($kpis['promedio'] ?? 0, 2, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-header card-header-jireh fw-semibold">Filtros</div>
        <div class="card-body">
            <form method="GET" action="{{ route('estadisticas_cuentas') }}" class="row g-3 align-items-end">
                <div class="col-6 col-md-2">
                    <label class="form-label fw-semibold" for="desde">Desde</label>
                    <input type="date" id="desde" name="desde" value="{{ $desde ?? '' }}" class="form-control">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label fw-semibold" for="hasta">Hasta</label>
                    <input type="date" id="hasta" name="hasta" value="{{ $hasta ?? '' }}" class="form-control">
                </div>
                <div class="col-12 col-md-6">
                    <span class="form-label fw-semibold d-block">Categorías</span>
                    @foreach ($categoriasDisponibles as $key => $nombre)
                        <span class="cat-pill d-inline-block me-2 mb-1">
                            <input type="checkbox" class="btn-check" id="cat-{{ $key }}" name="categorias[]"
                                value="{{ $key }}" {{ in_array($key, $seleccionadas ?? []) ? 'checked' : '' }} autocomplete="off">
                            <label class="btn btn-outline-secondary btn-sm" for="cat-{{ $key }}">{{ $nombre }}</label>
                        </span>
                    @endforeach
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-jireh flex-fill">Filtrar</button>
                    <a href="{{ route('estadisticas_cuentas') }}" class="btn btn-outline-secondary">Limpiar</a>
                </div>
            </form>
            @if (isset($errors) && $errors->any())
                <div class="alert alert-danger mt-3 mb-0">
                    @foreach ($errors->all() as $error)
                        <p class="mb-0">{{ $error }}</p>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @if (count($results) > 0)
    <div class="card shadow-sm mb-3">
        <div class="card-header card-header-jireh d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span class="fw-semibold">Sumatoria de gastos mensuales</span>
            <div class="btn-group btn-group-sm" role="group" aria-label="Tipo de gráfico">
                <button type="button" class="btn btn-light active" data-chart-type="line">Línea</button>
                <button type="button" class="btn btn-light" data-chart-type="bar">Barras</button>
                <button type="button" class="btn btn-light" data-chart-type="area">Área</button>
            </div>
        </div>
        <div class="card-body">
            <div id="chart"></div>
            <small class="text-muted">Usa el menú de la esquina del gráfico para descargar como PNG, SVG o CSV.</small>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header card-header-jireh fw-semibold">Totales del período</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-jireh mb-0">
                    <thead>
                        <tr>
                            <th>Categoría</th>
                            <th class="text-end">Total (Bs)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($seleccionadas as $key)
                            <tr>
                                <td>{{ $categoriasDisponibles[$key] }}</td>
                                <td class="text-end">Bs {{ number_format($totales[$key] ?? 0, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold table-light">
                            <td>Total general</td>
                            <td class="text-end">Bs {{ number_format($totalGeneral ?? 0, 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        var seriesData = {};
        var labels = [];
        @foreach ($seleccionadas as $key)
            seriesData['{{ $key }}'] = [];
        @endforeach
        @foreach ($results as $result)
            labels.push('{{$result->mes}} - {{$result->año}}')
            @foreach ($seleccionadas as $key)
                seriesData['{{ $key }}'].push({{$result->$key}});
            @endforeach
        @endforeach
        var options = {
            series: [
                @foreach ($seleccionadas as $key)
                    {name: "{{ $categoriasDisponibles[$key] }}", data: seriesData['{{ $key }}']},
                @endforeach
            ],
            colors: ['#125149', '#DA7922', '#2E86AB', '#C73E1D', '#6A994E', '#F4A259'],
            chart: {
                height: 450,
                type: 'line',
                toolbar: {
                    show: true,
                    tools: { download: true, selection: false, zoom: false, zoomin: false, zoomout: false, pan: false, reset: false }
                }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            markers: { size: 4 },
            title: { text: 'Sumatoria de gastos mensuales', align: 'left' },
            grid: { row: { colors: ['#f3f3f3', 'transparent'], opacity: 0.5 } },
            xaxis: { categories: labels },
            yaxis: { labels: { formatter: function (v) { return 'Bs ' + v; } } },
            legend: { position: 'bottom' }
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();

        function setChartType(type) {
            chart.updateOptions({ chart: { type: type } });
            document.querySelectorAll('[data-chart-type]').forEach(function (b) {
                b.classList.toggle('active', b.dataset.chartType === type);
            });
        }
        document.querySelectorAll('[data-chart-type]').forEach(function (b) {
            b.addEventListener('click', function () { setChartType(b.dataset.chartType); });
        });
    </script>
    @else
    <div class="alert alert-warning text-center">
        No hay datos para los filtros seleccionados. Ajusta las fechas o categorías e intenta de nuevo.
    </div>
    @endif
</div>
@endsection
