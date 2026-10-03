{{-- Bloque "Por categorías": mismas keywords que Control de Gastos (estadísticas).
  Requiere: $categoriasDisponibles, $totalesCategoria, $otrosCategoria, $totalGeneralCategoria.
  Montos en ABS (gastos); "Otros" = movimientos sin clasificar. No toca la tabla principal. --}}
@if (!empty($categoriasDisponibles))
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-4">
  <p class="px-3 py-2 text-sm font-semibold text-slate-800" style="background-color:#e7f0ee">Por categorías</p>
  <div class="overflow-x-auto">
    <table class="table w-full text-sm">
      <thead>
        <tr class="text-left text-xs text-slate-500 border-b border-slate-200">
          <th class="px-3 py-2">Categoría</th>
          <th class="px-3 py-2 text-right">Total (Bs)</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($categoriasDisponibles as $key => $nombre)
          <tr class="border-b border-slate-100">
            <td class="px-3 py-2 text-slate-700">{{ $nombre }}</td>
            <td class="px-3 py-2 text-right font-semibold whitespace-nowrap">Bs {{ number_format($totalesCategoria[$key] ?? 0, 2, ',', '.') }}</td>
          </tr>
        @endforeach
        <tr class="border-b border-slate-100">
          <td class="px-3 py-2 text-slate-500">Otros (sin clasificar)</td>
          <td class="px-3 py-2 text-right font-semibold whitespace-nowrap">Bs {{ number_format($otrosCategoria ?? 0, 2, ',', '.') }}</td>
        </tr>
      </tbody>
      <tfoot>
        <tr class="font-bold bg-slate-50">
          <td class="px-3 py-2">Total general</td>
          <td class="px-3 py-2 text-right whitespace-nowrap">Bs {{ number_format($totalGeneralCategoria ?? 0, 2, ',', '.') }}</td>
        </tr>
      </tfoot>
    </table>
  </div>
  <p class="px-3 py-2 text-xs text-slate-400">Mismas categorías que Control de Gastos, por coincidencia en el detalle.</p>
</div>
@endif
