@extends("header")
@section("titulo","Grupo JIREH")

@section("opciones")
    <a href="{{ route('menu') }}" class="opciones_head">Inicio</a>
@endsection

@section("estilos")
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@5.1.0/dist/apexcharts.min.css">
    <style>
        .jireh-green { background-color: #125149; }
        .jireh-orange { background-color: #DA7922; }
        .jireh-orange:hover { background-color: #b8621a; }
        .kpi-top-green { border-top: 4px solid #125149; }
        .kpi-top-orange { border-top: 4px solid #DA7922; }
        .kpi-icon {
            display: inline-flex; align-items: center; justify-content: center;
            width: 36px; height: 36px; border-radius: 9999px;
            background-color: #e7f0ee; color: #125149;
        }
        .kpi-icon-orange { background-color: #fbeedf; color: #DA7922; }
        .kpi-icon-red { background-color: #fdecec; color: #b91c1c; }
        .chart-type-btn.active { background-color: #125149; color: #fff; }
        .kpi-ayuda { border: none; background: transparent; color: #94a3b8; padding: 0; line-height: 1; cursor: pointer; }
        .kpi-ayuda:hover { color: #125149; }
        .kpi-ayuda:focus { outline: none; }
    </style>
@endsection

@section("contenido")
    <div class="max-w-5xl mx-auto px-3 py-4">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-1">
            <h3 class="text-xl font-bold text-slate-800">Reporte de estados de cuentas</h3>
            <span id="periodo_badge" class="hidden text-xs font-semibold text-white rounded-full px-3 py-1 jireh-green"></span>
        </div>
        <p class="text-sm text-slate-500 mb-4">Consulta por rango de fechas</p>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-4">
            <div class="jireh-green px-4 py-2">
                <p class="text-white font-semibold text-sm">Filtros</p>
            </div>
            <form id="formReporte" class="p-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="fecha_inicio" class="block text-sm font-medium text-slate-700 mb-1">
                            Fecha inicio
                        </label>
                        <input
                            type="date"
                            name="fecha_inicio"
                            id="fecha_inicio"
                            class="w-full rounded-xl border border-slate-300 px-3 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-slate-800"
                            required
                        >
                    </div>

                    <div>
                        <label for="fecha_fin" class="block text-sm font-medium text-slate-700 mb-1">
                            Fecha fin
                        </label>
                        <input
                            type="date"
                            name="fecha_fin"
                            id="fecha_fin"
                            class="w-full rounded-xl border border-slate-300 px-3 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-slate-800"
                            required
                        >
                    </div>
                </div>

                <button
                    type="submit"
                    id="btnGenerar"
                    class="w-full rounded-xl text-white py-3 font-medium active:scale-[0.99] transition mt-4 jireh-orange"
                >
                    Generar reporte
                </button>
            </form>

            <div id="errores" class="hidden mx-4 mb-4 rounded-xl bg-red-50 border border-red-200 p-3 text-sm text-red-700"></div>
        </div>

        <div id="loading" class="hidden mt-4 text-center text-sm text-slate-500">
            Generando reporte...
        </div>

        <div id="resultado" class="hidden mt-5 space-y-4">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm kpi-top-green">
                    <div class="flex items-center gap-2 mb-1">
                        <x-icon name="payments" class="kpi-icon"/>
                        <p class="text-xs text-slate-500">Total ingreso <x-kpi-ayuda texto="Costo de la mercadería comprada en el período, aunque siga en stock."/></p>
                    </div>
                    <p id="total_ing" class="text-lg font-bold text-slate-800">0.00</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm kpi-top-orange">
                    <div class="flex items-center gap-2 mb-1">
                        <x-icon name="shopping_cart" class="kpi-icon kpi-icon-orange"/>
                        <p class="text-xs text-slate-500">Total salida <x-kpi-ayuda texto="Ventas del período de lotes comprados en el período."/></p>
                    </div>
                    <p id="total_salida" class="text-lg font-bold text-slate-800">0.00</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm kpi-top-green">
                    <div class="flex items-center gap-2 mb-1">
                        <x-icon name="trending_up" class="kpi-icon"/>
                        <p class="text-xs text-slate-500">Utilidad bruta <x-kpi-ayuda texto="Ventas menos el costo de lo vendido, venta por venta. No incluye gastos."/></p>
                    </div>
                    <p id="kpi_utilidad" class="text-lg font-bold text-emerald-800">Bs 0.00</p>
                    <p class="text-xs text-slate-500">Margen: <span id="kpi_margen" class="font-semibold">0.00%</span></p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm kpi-top-orange">
                    <div class="flex items-center gap-2 mb-1">
                        <x-icon name="account_balance_wallet" class="kpi-icon kpi-icon-orange"/>
                        <p class="text-xs text-slate-500">Cobrado del período <x-kpi-ayuda texto="Plata cobrada en el período por ventas del período."/></p>
                    </div>
                    <p id="kpi_cobrado_periodo" class="text-lg font-bold text-slate-800">Bs 0.00</p>
                    <p class="text-xs text-slate-500">Por pagos: <span id="total_pago">Bs 0.00</span></p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm kpi-top-orange">
                    <div class="flex items-center gap-2 mb-1">
                        <x-icon name="schedule" class="kpi-icon kpi-icon-red"/>
                        <p class="text-xs text-slate-500">Pendiente de cobro (histórico) <x-kpi-ayuda texto="Todo lo fiado sin cobrar de toda la historia, no solo del mes."/></p>
                    </div>
                    <p id="kpi_pendiente" class="text-lg font-bold text-amber-800">Bs 0.00</p>
                    <p id="kpi_pendiente_n" class="text-xs text-slate-500">0 ventas pendientes en total</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm kpi-top-green">
                    <div class="flex items-center gap-2 mb-1">
                        <x-icon name="local_shipping" class="kpi-icon kpi-icon-red"/>
                        <p class="text-xs text-slate-500">Deuda proveedores (histórica) <x-kpi-ayuda texto="Compras acumuladas menos todos los pagos a proveedores registrados."/></p>
                    </div>
                    <p id="prov_deuda" class="text-lg font-bold text-red-700">Bs 0.00</p>
                    <p class="text-xs text-slate-500">Compras del período: <span id="prov_compras">Bs 0.00</span></p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm kpi-top-orange">
                    <div class="flex items-center gap-2 mb-1">
                        <x-icon name="schedule" class="kpi-icon kpi-icon-orange"/>
                        <p class="text-xs text-slate-500">Pendiente generado en el período <x-kpi-ayuda texto="Fiado nuevo del mes: ventas del período con saldo pendiente."/></p>
                    </div>
                    <p id="kpi_pendiente_periodo" class="text-lg font-bold text-amber-800">Bs 0.00</p>
                    <p id="kpi_pendiente_periodo_n" class="text-xs text-slate-500">0 ventas del período con saldo</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm kpi-top-green">
                    <div class="flex items-center gap-2 mb-1">
                        <x-icon name="local_shipping" class="kpi-icon"/>
                        <p class="text-xs text-slate-500">Flujo neto proveedores (período) <x-kpi-ayuda texto="Compras del período menos pagos a proveedores del período."/></p>
                    </div>
                    <p id="prov_flujo" class="text-lg font-bold text-slate-800">Bs 0.00</p>
                    <p class="text-xs text-slate-500">Compras menos pagos del período</p>
                </div>
            </div>
            <p id="prov_nota" class="hidden text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-xl px-3 py-2">Sin pagos a proveedores registrados: la deuda histórica equivale al total comprado. Registra pagos con Pagar lote en el reporte de lotes.</p>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 jireh-green">
                    <h4 class="font-semibold text-white">Antigüedad del pendiente de cobro <x-kpi-ayuda texto="Deuda agrupada por días desde la venta. Lo de más de 180 días es cartera vieja."/></h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs text-slate-500 border-b border-slate-200">
                                <th class="px-4 py-2 font-medium">Antigüedad</th>
                                <th class="px-4 py-2 font-medium text-right">Ventas</th>
                                <th class="px-4 py-2 font-medium text-right">Monto</th>
                            </tr>
                        </thead>
                        <tbody id="tablaAntiguedad"></tbody>
                    </table>
                </div>
            </div>

            <div class="hidden md:grid grid-cols-4 gap-3">
                <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm">
                    <p class="text-xs text-slate-500">Peso ingreso</p>
                    <p id="peso_ing" class="text-lg font-bold text-slate-800">0.00</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm">
                    <p class="text-xs text-slate-500">Peso salida</p>
                    <p id="peso_salida" class="text-lg font-bold text-slate-800">0.00</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm">
                    <p class="text-xs text-slate-500">Lotes comprados</p>
                    <p id="prov_lotes" class="text-lg font-bold text-slate-800">0</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm">
                    <p class="text-xs text-slate-500">Pagado a proveedores</p>
                    <p id="prov_pagado" class="text-lg font-bold text-slate-800">Bs 0.00</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="jireh-green px-4 py-2 flex flex-wrap items-center justify-between gap-2">
                    <p class="text-white font-semibold text-sm">Ventas vs costos por mes</p>
                    <div class="flex gap-1" role="group" aria-label="Tipo de gráfico">
                        <button type="button" class="chart-type-btn active text-xs px-2 py-1 rounded bg-white/90" data-mchart-type="line">Línea</button>
                        <button type="button" class="chart-type-btn text-xs px-2 py-1 rounded bg-white/90" data-mchart-type="bar">Barras</button>
                        <button type="button" class="chart-type-btn text-xs px-2 py-1 rounded bg-white/90" data-mchart-type="area">Área</button>
                    </div>
                </div>
                <div class="p-3">
                    <div id="chartMensual"></div>
                    <p class="text-xs text-slate-400 mt-1">Usa el menú de la esquina del gráfico para descargar como PNG, SVG o CSV.</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 jireh-green">
                    <h4 class="font-semibold text-white">Detalle mensual</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs text-slate-500 border-b border-slate-200">
                                <th class="px-4 py-2 font-medium">Mes</th>
                                <th class="px-4 py-2 font-medium text-right">Ventas</th>
                                <th class="px-4 py-2 font-medium text-right">Costo</th>
                                <th class="px-4 py-2 font-medium text-right">Utilidad</th>
                                <th class="px-4 py-2 font-medium text-right">Margen</th>
                            </tr>
                        </thead>
                        <tbody id="tablaMensual"></tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
                <div class="mb-3">
                    <h4 class="font-semibold text-slate-800">Estadísticas de gastos</h4>
                    <p class="text-xs text-slate-500">Resumen del rango consultado</p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-xs text-slate-500">Almuerzo</p>
                        <p id="stat_almuerzo" class="text-base font-bold text-slate-800">Bs 0.00</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-xs text-slate-500">Desayuno</p>
                        <p id="stat_desayuno" class="text-base font-bold text-slate-800">Bs 0.00</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-xs text-slate-500">Gasolina</p>
                        <p id="stat_gasolina" class="text-base font-bold text-slate-800">Bs 0.00</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-xs text-slate-500">Diesel</p>
                        <p id="stat_diesel" class="text-base font-bold text-slate-800">Bs 0.00</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-xs text-slate-500">Transporte</p>
                        <p id="stat_transporte" class="text-base font-bold text-slate-800">Bs 0.00</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-xs text-slate-500">Aceite</p>
                        <p id="stat_aceite" class="text-base font-bold text-slate-800">Bs 0.00</p>
                    </div>
                    <div class="bg-slate-100 rounded-xl p-3 col-span-2 md:col-span-3">
                        <p class="text-xs text-slate-500">Total gastos</p>
                        <p id="stat_total_gastos" class="text-lg font-bold text-slate-900">Bs 0.00</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 jireh-green">
                    <h4 class="font-semibold text-white">Detalle por producto</h4>
                </div>

                <div id="tablaReporte" class="divide-y divide-slate-100"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        const form = document.getElementById('formReporte');
        const errores = document.getElementById('errores');
        const loading = document.getElementById('loading');
        const resultado = document.getElementById('resultado');
        const tablaReporte = document.getElementById('tablaReporte');
        const btnGenerar = document.getElementById('btnGenerar');
        let chartMensual = null;

        const fmtBO = new Intl.NumberFormat('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        function money(valor) {
            return fmtBO.format(Number(valor || 0));
        }

        function numberFormat(valor) {
            return fmtBO.format(Number(valor || 0));
        }

        function setChartType(type) {
            if (chartMensual) {
                chartMensual.updateOptions({ chart: { type: type } });
            }
            document.querySelectorAll('[data-mchart-type]').forEach(function (b) {
                b.classList.toggle('active', b.dataset.mchartType === type);
            });
        }
        document.querySelectorAll('[data-mchart-type]').forEach(function (b) {
            b.addEventListener('click', function () { setChartType(b.dataset.mchartType); });
        });

        function renderChartMensual(items) {
            if (chartMensual) {
                chartMensual.destroy();
                chartMensual = null;
            }
            document.querySelector('#chartMensual').innerHTML = '';
            if (!items || items.length === 0) {
                document.querySelector('#chartMensual').innerHTML =
                    '<p class="text-sm text-slate-500 text-center py-6">Sin ventas en ese rango de fechas.</p>';
                return;
            }
            const options = {
                series: [
                    { name: 'Ventas', data: items.map(i => Number(i.ventas || 0)) },
                    { name: 'Costo', data: items.map(i => Number(i.costo || 0)) },
                    { name: 'Utilidad', data: items.map(i => Number(i.utilidad || 0)) }
                ],
                colors: ['#125149', '#DA7922', '#2E86AB'],
                chart: {
                    height: 350,
                    type: 'line',
                    toolbar: {
                        show: true,
                        tools: { download: true, selection: false, zoom: false, zoomin: false, zoomout: false, pan: false, reset: false }
                    }
                },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                markers: { size: 3 },
                xaxis: { categories: items.map(i => i.mes) },
                yaxis: { labels: { formatter: function (v) { return 'Bs ' + v; } } },
                legend: { position: 'bottom' }
            };
            chartMensual = new ApexCharts(document.querySelector('#chartMensual'), options);
            chartMensual.render();
        }

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            errores.classList.add('hidden');
            errores.innerHTML = '';
            resultado.classList.add('hidden');
            tablaReporte.innerHTML = '';
            document.getElementById('tablaMensual').innerHTML = '';
            document.getElementById('tablaAntiguedad').innerHTML = '';
            document.getElementById('prov_nota').classList.add('hidden');
            loading.classList.remove('hidden');
            btnGenerar.disabled = true;
            btnGenerar.classList.add('opacity-70');

            const formData = new FormData(form);

            try {
                const response = await fetch("{{ route('estado_cuentas.reporte') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        "Accept": "application/json"
                    },
                    body: formData
                });

                const data = await response.json();

                if (!response.ok) {
                    if (data.errors) {
                        let html = '<ul class="list-disc pl-5 space-y-1">';
                        Object.values(data.errors).forEach(items => {
                            items.forEach(msg => {
                                html += `<li>${msg}</li>`;
                            });
                        });
                        html += '</ul>';
                        errores.innerHTML = html;
                    } else {
                        errores.innerHTML = 'Ocurrió un error al generar el reporte.';
                    }

                    errores.classList.remove('hidden');
                    return;
                }

                const badge = document.getElementById('periodo_badge');
                badge.textContent = formData.get('fecha_inicio') + ' al ' + formData.get('fecha_fin');
                badge.classList.remove('hidden');

                document.getElementById('total_ing').textContent = 'Bs ' + money(data.totales.total_ing);
                document.getElementById('total_salida').textContent = 'Bs ' + money(data.totales.total_salida);
                document.getElementById('peso_ing').textContent = numberFormat(data.totales.peso_ing);
                document.getElementById('peso_salida').textContent = numberFormat(data.totales.peso_salida);
                document.getElementById('total_pago').textContent = 'Bs ' + money(data.totales.total_pago);

                document.getElementById('kpi_utilidad').textContent = 'Bs ' + money(data.ventas_costos_totales?.utilidad);
                document.getElementById('kpi_margen').textContent = money(data.ventas_costos_totales?.margen) + '%';
                document.getElementById('kpi_cobrado_periodo').textContent = 'Bs ' + money(data.cobranza?.cobrado_del_periodo);
                document.getElementById('kpi_pendiente').textContent = 'Bs ' + money(data.cobranza?.pendiente_total);
                document.getElementById('kpi_pendiente_n').textContent = (data.cobranza?.ventas_con_pendiente || 0) + ' ventas pendientes en total';
                document.getElementById('kpi_pendiente_periodo').textContent = 'Bs ' + money(data.cobranza?.pendiente_periodo);
                document.getElementById('kpi_pendiente_periodo_n').textContent = (data.cobranza?.ventas_con_pendiente_periodo || 0) + ' ventas del período con saldo';
                document.getElementById('prov_lotes').textContent = data.proveedores?.lotes || 0;
                document.getElementById('prov_compras').textContent = 'Bs ' + money(data.proveedores?.compras_total);
                document.getElementById('prov_pagado').textContent = 'Bs ' + money(data.proveedores?.pagado_rango);
                document.getElementById('prov_deuda').textContent = 'Bs ' + money(data.proveedores?.deuda_total);
                document.getElementById('prov_flujo').textContent = 'Bs ' + money(data.proveedores?.flujo_neto_rango);
                document.getElementById('prov_nota').classList.toggle('hidden', (data.proveedores?.pagos_historico_n || 0) > 0);

                const ta = document.getElementById('tablaAntiguedad');
                const orden = ['0-30', '31-90', '91-180', '>180'];
                const ant = {};
                (data.cobranza?.pendiente_antiguedad || []).forEach(function (r) { ant[r.bucket] = r; });
                ta.innerHTML = orden.map(function (b) {
                    const r = ant[b] || { cantidad: 0, total: 0 };
                    return `<tr class="border-b border-slate-100"><td class="px-4 py-2">${b} días</td><td class="px-4 py-2 text-right">${r.cantidad}</td><td class="px-4 py-2 text-right">Bs ${money(r.total)}</td></tr>`;
                }).join('');

                renderChartMensual(data.ventas_costos);

                const tm = document.getElementById('tablaMensual');
                if (!data.ventas_costos || data.ventas_costos.length === 0) {
                    tm.innerHTML = `
                        <tr><td colspan="5" class="px-4 py-3 text-sm text-slate-500 text-center">
                            Sin ventas en ese rango de fechas.
                        </td></tr>
                    `;
                } else {
                    data.ventas_costos.forEach(item => {
                        tm.innerHTML += `
                            <tr class="border-b border-slate-100">
                                <td class="px-4 py-2 font-semibold">${item.mes}</td>
                                <td class="px-4 py-2 text-right">Bs ${money(item.ventas)}</td>
                                <td class="px-4 py-2 text-right">Bs ${money(item.costo)}</td>
                                <td class="px-4 py-2 text-right font-semibold text-emerald-800">Bs ${money(item.utilidad)}</td>
                                <td class="px-4 py-2 text-right">${money(item.margen)}%</td>
                            </tr>
                        `;
                    });
                }

                document.getElementById('stat_almuerzo').textContent = 'Bs ' + money(data.estadisticas?.almuerzo);
                document.getElementById('stat_desayuno').textContent = 'Bs ' + money(data.estadisticas?.desayuno);
                document.getElementById('stat_gasolina').textContent = 'Bs ' + money(data.estadisticas?.gasolina);
                document.getElementById('stat_diesel').textContent = 'Bs ' + money(data.estadisticas?.diesel);
                document.getElementById('stat_transporte').textContent = 'Bs ' + money(data.estadisticas?.transporte);
                document.getElementById('stat_aceite').textContent = 'Bs ' + money(data.estadisticas?.aceite);
                document.getElementById('stat_total_gastos').textContent = 'Bs ' + money(data.estadisticas_totales?.total_gastos);

                if (data.data.length === 0) {
                    tablaReporte.innerHTML = `
                        <div class="p-4 text-sm text-slate-500 text-center">
                            No se encontraron registros en ese rango de fechas.
                        </div>
                    `;
                } else {
                    data.data.forEach(item => {
                        tablaReporte.innerHTML += `
                            <div class="p-4">
                                <p class="font-semibold text-slate-800 text-sm">${item.producto}</p>

                                <div class="grid grid-cols-2 gap-2 mt-3 text-xs">
                                    <div class="bg-slate-50 rounded-xl p-2">
                                        <p class="text-slate-500">Cant. ingreso</p>
                                        <p class="font-semibold">${numberFormat(item.cant_ing)}</p>
                                    </div>
                                    <div class="bg-slate-50 rounded-xl p-2">
                                        <p class="text-slate-500">Cant. salida</p>
                                        <p class="font-semibold">${numberFormat(item.cantidad_salida)}</p>
                                    </div>
                                    <div class="bg-slate-50 rounded-xl p-2">
                                        <p class="text-slate-500">Peso ingreso</p>
                                        <p class="font-semibold">${numberFormat(item.peso_ing)}</p>
                                    </div>
                                    <div class="bg-slate-50 rounded-xl p-2">
                                        <p class="text-slate-500">Peso salida</p>
                                        <p class="font-semibold">${numberFormat(item.peso_salida)}</p>
                                    </div>
                                    <div class="bg-slate-50 rounded-xl p-2">
                                        <p class="text-slate-500">Total ingreso</p>
                                        <p class="font-semibold">Bs ${money(item.total_ing)}</p>
                                    </div>
                                    <div class="bg-slate-50 rounded-xl p-2">
                                        <p class="text-slate-500">Total salida</p>
                                        <p class="font-semibold">Bs ${money(item.total_salida)}</p>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                }

                resultado.classList.remove('hidden');

            } catch (error) {
                errores.innerHTML = 'No se pudo conectar con el servidor.';
                errores.classList.remove('hidden');
            } finally {
                loading.classList.add('hidden');
                btnGenerar.disabled = false;
                btnGenerar.classList.remove('opacity-70');
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            if (typeof bootstrap === 'undefined') return;
            document.querySelectorAll('[data-bs-toggle="popover"]').forEach(function (el) {
                new bootstrap.Popover(el);
            });
        });
    </script>
@endsection
