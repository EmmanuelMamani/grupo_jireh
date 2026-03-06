@extends("header")
@section("titulo","Grupo JIREH")

@section("opciones")
    <a href="{{ route('menu') }}" class="opciones_head">Inicio</a>
@endsection

@section("estilos")
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section("contenido")
    <div class="max-w-md mx-auto px-3 py-4">
        <h3 class="text-xl font-bold text-slate-800 mb-1">Reporte de estados de cuentas</h3>
        <p class="text-sm text-slate-500 mb-4">Consulta por rango de fechas</p>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4">
            <form id="formReporte" class="space-y-4">
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

                <button
                    type="submit"
                    id="btnGenerar"
                    class="w-full rounded-xl bg-slate-900 text-white py-3 font-medium active:scale-[0.99] transition"
                >
                    Generar reporte
                </button>
            </form>

            <div id="errores" class="hidden mt-4 rounded-xl bg-red-50 border border-red-200 p-3 text-sm text-red-700"></div>
        </div>

        <div id="loading" class="hidden mt-4 text-center text-sm text-slate-500">
            Generando reporte...
        </div>

        <div id="resultado" class="hidden mt-5 space-y-4">
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm">
                    <p class="text-xs text-slate-500">Total ingreso</p>
                    <p id="total_ing" class="text-lg font-bold text-slate-800">0.00</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm">
                    <p class="text-xs text-slate-500">Total salida</p>
                    <p id="total_salida" class="text-lg font-bold text-slate-800">0.00</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm">
                    <p class="text-xs text-slate-500">Peso ingreso</p>
                    <p id="peso_ing" class="text-lg font-bold text-slate-800">0.00</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-sm">
                    <p class="text-xs text-slate-500">Peso salida</p>
                    <p id="peso_salida" class="text-lg font-bold text-slate-800">0.00</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
                <div class="mb-3">
                    <h4 class="font-semibold text-slate-800">Estadísticas de gastos</h4>
                    <p class="text-xs text-slate-500">Resumen del rango consultado</p>
                </div>

                <div class="grid grid-cols-2 gap-3">
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
                    <div class="bg-slate-100 rounded-xl p-3 col-span-2">
                        <p class="text-xs text-slate-500">Total gastos</p>
                        <p id="stat_total_gastos" class="text-lg font-bold text-slate-900">Bs 0.00</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200">
                    <h4 class="font-semibold text-slate-800">Detalle por producto</h4>
                </div>

                <div id="tablaReporte" class="divide-y divide-slate-100"></div>
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('formReporte');
        const errores = document.getElementById('errores');
        const loading = document.getElementById('loading');
        const resultado = document.getElementById('resultado');
        const tablaReporte = document.getElementById('tablaReporte');
        const btnGenerar = document.getElementById('btnGenerar');

        function money(valor) {
            return Number(valor || 0).toFixed(2);
        }

        function numberFormat(valor) {
            return Number(valor || 0).toFixed(2);
        }

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            errores.classList.add('hidden');
            errores.innerHTML = '';
            resultado.classList.add('hidden');
            tablaReporte.innerHTML = '';
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

                document.getElementById('total_ing').textContent = 'Bs ' + money(data.totales.total_ing);
                document.getElementById('total_salida').textContent = 'Bs ' + money(data.totales.total_salida);
                document.getElementById('peso_ing').textContent = numberFormat(data.totales.peso_ing);
                document.getElementById('peso_salida').textContent = numberFormat(data.totales.peso_salida);

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
    </script>
@endsection
