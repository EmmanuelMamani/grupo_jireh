@extends("header")
@section("titulo", "Grupo JIREH")

@section("opciones")
    <a href="/menu" class="opciones_head">Inicio</a>
    <a href="/registro_cliente" class="opciones_head">Registro</a>
    @if (Auth::user()->Rol == 'Administrador')
        <a href="/reporte_cliente" class="opciones_head">Reporte</a>
    @endif
@endsection

@section("estilos")
    <script src="https://cdn.tailwindcss.com"></script>
@endsection

@section("contenido")
    @php
        $totalVentas = collect($ventas)->sum(fn($venta) => $venta->salida->Total ?? 0);
        $totalPeso = collect($ventas)->sum(fn($venta) => ($venta->salida->Peso === '' || $venta->salida->Peso === null) ? 0 : $venta->salida->Peso);
        $totalPagos = collect($saldos)->sum(fn($saldo) => $saldo->Monto ?? 0);
        $ultimoSaldo = collect($saldos)->last();
    @endphp

    <div class="min-h-screen bg-slate-100">
        <div class="max-w-md mx-auto px-3 py-4 space-y-4">

            <div class="bg-gradient-to-br from-slate-900 to-slate-700 text-white rounded-3xl p-4 shadow-lg">
                <div class="space-y-3">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-300">Reporte de cliente</p>
                        <h1 class="text-xl font-bold leading-tight">{{ $cliente->Nombre }}</h1>

                        @if(!empty($inicio) && !empty($fin))
                            <p class="text-sm text-slate-300 mt-1">
                                Periodo: {{ \Carbon\Carbon::parse($inicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fin)->format('d/m/Y') }}
                            </p>
                        @endif
                    </div>

                    <a
                        href="{{ route('reporte_periodo_ventas_pdf',['id'=>$cliente->id,'inicio'=>$inicio,'fin'=>$fin]) }}"
                        class="w-full inline-flex items-center justify-center rounded-2xl bg-white text-slate-900 font-semibold py-3 px-4 active:scale-[0.99] transition"
                    >
                        Descargar PDF
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="bg-white rounded-2xl p-3 shadow-sm border border-slate-200">
                    <p class="text-xs text-slate-500">Ventas</p>
                    <p class="text-lg font-bold text-slate-900">{{ count($ventas) }}</p>
                    <p class="text-[11px] text-slate-400 mt-1">Registros</p>
                </div>

                <div class="bg-white rounded-2xl p-3 shadow-sm border border-slate-200">
                    <p class="text-xs text-slate-500">Monto vendido</p>
                    <p class="text-lg font-bold text-slate-900">Bs {{ number_format($totalVentas, 2) }}</p>
                    <p class="text-[11px] text-slate-400 mt-1">Total acumulado</p>
                </div>

                <div class="bg-white rounded-2xl p-3 shadow-sm border border-slate-200">
                    <p class="text-xs text-slate-500">Peso vendido</p>
                    <p class="text-lg font-bold text-slate-900">{{ number_format($totalPeso, 2) }} Kg</p>
                    <p class="text-[11px] text-slate-400 mt-1">Peso total</p>
                </div>

                <div class="bg-white rounded-2xl p-3 shadow-sm border border-slate-200">
                    <p class="text-xs text-slate-500">Pagos</p>
                    <p class="text-lg font-bold text-slate-900">Bs {{ number_format($totalPagos, 2) }}</p>
                    <p class="text-[11px] text-slate-400 mt-1">Abonos registrados</p>
                </div>
            </div>

            @if($ultimoSaldo)
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 shadow-sm">
                    <p class="text-xs text-emerald-700">Último saldo registrado</p>
                    <p class="text-2xl font-bold text-emerald-900 mt-1">
                        Bs {{ number_format($ultimoSaldo->Saldo, 2) }}
                    </p>
                    <p class="text-xs text-emerald-700 mt-1">
                        Actualizado:
                        {{ \Carbon\Carbon::parse($ultimoSaldo->created_at)->format('d/m/Y H:i') }}
                    </p>
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-4 py-4 border-b border-slate-200">
                    <h2 class="text-base font-bold text-slate-900">Detalle de ventas</h2>
                    <p class="text-sm text-slate-500">Historial de ventas realizadas al cliente</p>
                </div>

                <div class="p-3 space-y-3">
                    @forelse ($ventas as $key => $venta)
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs text-slate-500">Venta #{{ $key + 1 }}</p>
                                    <h3 class="font-semibold text-slate-900 leading-tight">
                                        {{ $venta->ingreso->producto->Nombre }}
                                    </h3>
                                </div>

                                <span class="shrink-0 rounded-full bg-slate-900 text-white text-xs font-semibold px-3 py-1">
                                    Bs {{ number_format($venta->salida->Total, 2) }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-2 mt-3 text-sm">
                                <div class="bg-white rounded-xl p-2">
                                    <p class="text-slate-500 text-xs">Vendedor</p>
                                    <p class="font-medium text-slate-800">{{ $venta->user->Nombre }}</p>
                                </div>

                                <div class="bg-white rounded-xl p-2">
                                    <p class="text-slate-500 text-xs">Fecha</p>
                                    <p class="font-medium text-slate-800">{{ $venta->created_at->format('d-m-Y') }}</p>
                                </div>

                                <div class="bg-white rounded-xl p-2">
                                    <p class="text-slate-500 text-xs">Cantidad</p>
                                    <p class="font-medium text-slate-800">{{ $venta->salida->CantMoldes }}</p>
                                </div>

                                <div class="bg-white rounded-xl p-2">
                                    <p class="text-slate-500 text-xs">Costo unitario</p>
                                    <p class="font-medium text-slate-800">Bs {{ number_format($venta->salida->Precio, 2) }}</p>
                                </div>

                                <div class="bg-white rounded-xl p-2 col-span-2">
                                    <p class="text-slate-500 text-xs">Peso</p>
                                    <p class="font-medium text-slate-800">
                                        {{ number_format(($venta->salida->Peso === '' || $venta->salida->Peso === null) ? 0 : $venta->salida->Peso, 2) }} Kg
                                    </p>
                                </div>
                            </div>

                            <a
                                href="{{ route('editar_venta', ['id' => $venta->id]) }}"
                                class="mt-3 inline-flex w-full items-center justify-center rounded-xl bg-amber-500 text-white font-semibold py-2.5 px-4 active:scale-[0.99] transition"
                            >
                                Editar venta
                            </a>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center">
                            <p class="text-sm text-slate-500">No hay ventas registradas en este periodo.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-4 py-4 border-b border-slate-200">
                    <h2 class="text-base font-bold text-slate-900">Movimientos de saldo</h2>
                    <p class="text-sm text-slate-500">Pagos, cargos y evolución del saldo</p>
                </div>

                <div class="p-3 space-y-3">
                    @forelse ($saldos as $key => $saldo)
                        @php
                            $detalle = strtolower(trim($saldo->Detalle ?? ''));
                            $monto = (float) ($saldo->Monto ?? 0);
                            $saldoActual = (float) ($saldo->Saldo ?? 0);

                            $esAbono = str_contains($detalle, 'pago') || str_contains($detalle, 'abono') || str_contains($detalle, 'cancel');
                            $esCargo = str_contains($detalle, 'pre-venta') || str_contains($detalle, 'pre venta') || str_contains($detalle, 'venta') || str_contains($detalle, 'deuda');

                            if ($esAbono) {
                                $tipoMovimiento = 'ABONO';
                                $colorBadge = 'bg-emerald-100 text-emerald-700 border-emerald-200';
                                $colorMonto = 'text-emerald-700';
                                $signo = '-';
                                $saldoAnterior = $saldoActual + $monto;
                            } elseif ($esCargo) {
                                $tipoMovimiento = 'CARGO';
                                $colorBadge = 'bg-amber-100 text-amber-700 border-amber-200';
                                $colorMonto = 'text-amber-700';
                                $signo = '+';
                                $saldoAnterior = $saldoActual - $monto;
                            } else {
                                $tipoMovimiento = 'MOVIMIENTO';
                                $colorBadge = 'bg-slate-100 text-slate-700 border-slate-200';
                                $colorMonto = 'text-slate-700';
                                $signo = '';
                                $saldoAnterior = null;
                            }
                        @endphp

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs text-slate-500">Movimiento #{{ $key + 1 }}</p>
                                    <h3 class="font-semibold text-slate-900 leading-tight">
                                        {{ $saldo->Detalle }}
                                    </h3>
                                </div>

                                <span class="shrink-0 rounded-full border px-3 py-1 text-[11px] font-bold {{ $colorBadge }}">
                                    {{ $tipoMovimiento }}
                                </span>
                            </div>

                            <div class="mt-3 rounded-2xl bg-white border border-slate-200 p-3">
                                <div class="grid grid-cols-3 gap-2 text-xs items-center">
                                    <div>
                                        <p class="text-slate-400">Saldo anterior</p>
                                        <p class="font-semibold text-slate-800 mt-1">
                                            @if(!is_null($saldoAnterior))
                                                Bs {{ number_format($saldoAnterior, 2) }}
                                            @else
                                                -
                                            @endif
                                        </p>
                                    </div>

                                    <div class="text-center">
                                        <p class="text-slate-400">Movimiento</p>
                                        <p class="font-bold mt-1 {{ $colorMonto }}">
                                            {{ $signo }}Bs {{ number_format($monto, 2) }}
                                        </p>
                                    </div>

                                    <div class="text-right">
                                        <p class="text-slate-400">Saldo actual</p>
                                        <p class="font-semibold text-slate-900 mt-1">
                                            Bs {{ number_format($saldoActual, 2) }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between mt-3 text-slate-300 px-1">
                                    <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                                    <span class="flex-1 h-px bg-slate-300 mx-2"></span>
                                    <span class="text-xs">flujo</span>
                                    <span class="flex-1 h-px bg-slate-300 mx-2"></span>
                                    <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2 mt-3 text-sm">
                                <div class="bg-white rounded-xl p-2">
                                    <p class="text-slate-500 text-xs">Monto</p>
                                    <p class="font-medium text-slate-800">Bs {{ number_format($monto, 2) }}</p>
                                </div>

                                <div class="bg-white rounded-xl p-2">
                                    <p class="text-slate-500 text-xs">Saldo actual</p>
                                    <p class="font-medium text-emerald-700">Bs {{ number_format($saldoActual, 2) }}</p>
                                </div>

                                <div class="bg-white rounded-xl p-2">
                                    <p class="text-slate-500 text-xs">Cobrador</p>
                                    <p class="font-medium text-slate-800">
                                        {{ !empty($saldo->Nombre) ? $saldo->Nombre : 'Sin dato' }}
                                    </p>
                                </div>

                                <div class="bg-white rounded-xl p-2">
                                    <p class="text-slate-500 text-xs">Fecha</p>
                                    <p class="font-medium text-slate-800">
                                        {{ \Carbon\Carbon::parse($saldo->created_at)->format('d-m-Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center">
                            <p class="text-sm text-slate-500">No hay movimientos de saldo registrados.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
@endsection
