@extends("header")
@section("titulo","Grupo JIREH")
@section("opciones")
<a href="{{route("menu")}}" class="opciones_head">Inicio</a>
@endsection
@section("estilos")
<style>
    .card-header-jireh { background-color: #125149; color: #fff; }
    .btn-jireh { background-color: #DA7922; border-color: #DA7922; color: #fff; }
    .btn-jireh:hover { background-color: #b8621a; border-color: #b8621a; color: #fff; }
</style>
@endsection
@section("contenido")
<div class="container-fluid px-3 px-md-4 pb-4">
    <h3 class="my-3">Conciliación de cobros 2026</h3>
    <p class="text-muted">Asigna cobros sueltos a ventas pendientes. Solo cobros desde {{ $desde }}.</p>

    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <p class="mb-0">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="card shadow-sm mb-3">
        <div class="card-header card-header-jireh fw-semibold">Cliente</div>
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-8">
                    <label class="form-label fw-semibold" for="cliente">Cliente con cobros sueltos</label>
                    <select id="cliente" class="form-select">
                        <option value="">Seleccionar cliente</option>
                        @foreach ($clientes as $c)
                            <option value="{{ $c->id }}">{{ $c->Nombre }} ({{ $resumen[$c->id]['n'] ?? 0 }} cobros · Bs {{ number_format($resumen[$c->id]['monto'] ?? 0, 2) }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <div id="progreso" class="text-muted small"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="panel" style="display:none;">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="card shadow-sm mb-3">
                    <div class="card-header card-header-jireh fw-semibold">Cobros sueltos</div>
                    <div class="card-body" id="cobros"></div>
                </div>
                <div class="card shadow-sm mb-3">
                    <div class="card-header card-header-jireh fw-semibold">Sin cliente identificado (2026)</div>
                    <div class="card-body" id="bolsa"></div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card shadow-sm mb-3">
                    <div class="card-header card-header-jireh fw-semibold">Ventas pendientes</div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead><tr><th>#</th><th>Fecha</th><th class="text-end">Total</th><th class="text-end">Pendiente</th></tr></thead>
                                <tbody id="ventas"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let clienteActual = null;
let ventasGlobal = [];

function opcionesVentas(seleccionada) {
    let html = '';
    ventasGlobal.forEach(v => {
        const sel = (seleccionada && v.id === seleccionada) ? 'selected' : '';
        html += `<option value="${v.id}" ${sel}>#${v.id} · ${v.fecha} · debe Bs ${v.pendiente}</option>`;
    });
    return html;
}

function formCobro(cobro, esBolsa) {
    const sugV = cobro.sugerencia ? cobro.sugerencia.venta_id : null;
    const sugM = cobro.sugerencia ? cobro.sugerencia.monto : cobro.disponible;
    const etiqueta = cobro.tipo === 'saldo' ? `Saldo #${cobro.saldo_id}` : `Cuenta #${cobro.cuenta_id}`;
    const extra = esBolsa ? `<span class="badge bg-warning text-dark">figura como: ${cobro.nombre}</span>` : '';
    const hidden = cobro.saldo_id
        ? `<input type="hidden" name="saldo_id" value="${cobro.saldo_id}">`
        : `<input type="hidden" name="cuenta_id" value="${cobro.cuenta_id}">`;
    return `
    <div class="border rounded p-2 mb-2">
        <div class="d-flex justify-content-between flex-wrap gap-1">
            <strong>${etiqueta}</strong>${extra}
            <span>${cobro.fecha} · disp. Bs ${cobro.disponible}</span>
        </div>
        <form method="POST" action="{{ route('conciliacion.asignar') }}" class="row g-2 mt-1">
            @csrf
            <input type="hidden" name="cliente_id" value="${clienteActual}">
            ${hidden}
            <div class="col-6">
                <select name="venta_id" class="form-select form-select-sm" required>${opcionesVentas(sugV)}</select>
            </div>
            <div class="col-3">
                <input type="number" step="0.01" min="0.01" name="monto" value="${sugM}" class="form-control form-control-sm" required>
            </div>
            <div class="col-3">
                <button class="btn btn-jireh btn-sm w-100">Asignar</button>
            </div>
        </form>
    </div>`;
}

document.getElementById('cliente').addEventListener('change', function () {
    clienteActual = this.value;
    if (!clienteActual) {
        document.getElementById('panel').style.display = 'none';
        return;
    }
    fetch(`/conciliacion/pendientes/${clienteActual}`)
        .then(r => r.json())
        .then(data => {
            ventasGlobal = data.ventas_pendientes;
            document.getElementById('panel').style.display = 'block';
            document.getElementById('progreso').textContent =
                `${data.cliente.nombre}: ${data.cobros.length} cobros sueltos, ${data.ventas_pendientes.length} ventas pendientes`;

            let vt = '';
            data.ventas_pendientes.forEach(v => {
                vt += `<tr><td>#${v.id}</td><td>${v.fecha}</td><td class="text-end">${v.total}</td><td class="text-end fw-bold">${v.pendiente}</td></tr>`;
            });
            document.getElementById('ventas').innerHTML = vt || '<tr><td colspan="4" class="text-center">Sin pendientes</td></tr>';

            let ch = '';
            data.cobros.forEach(c => { ch += formCobro(c, false); });
            document.getElementById('cobros').innerHTML = ch || '<p class="text-muted mb-0">Sin cobros sueltos. ¡Todo conciliado!</p>';

            let bh = '';
            data.sin_identificar.forEach(c => {
                bh += formCobro({tipo: 'cuenta', saldo_id: null, cuenta_id: c.cuenta_id,
                    disponible: c.disponible, fecha: c.fecha, nombre: c.nombre, sugerencia: null}, true);
            });
            document.getElementById('bolsa').innerHTML = bh || '<p class="text-muted mb-0">Vacío</p>';
        })
        .catch(e => console.error(e));
});
</script>
@endsection
