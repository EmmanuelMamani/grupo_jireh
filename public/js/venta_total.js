/* Total en vivo + confirmación para formularios de venta.
 * Replica la lógica del servidor (VentaController):
 *  - Producto "Por Kilo": total = peso * precio; si no: precio * moldes.
 *  - Redondeo: si el valor que enviaría el checkbox (checked ? value : ausente)
 *    es == 0 (comparación laxa como PHP), 2 decimales; si no, 0 decimales.
 * Solo lectura: no altera lo que se envía al servidor.
 */
(function () {
  function parseNum(v) {
    if (v === null || v === undefined) return NaN;
    var n = parseFloat(String(v).replace(",", "."));
    return isNaN(n) ? NaN : n;
  }

  function lastWord(text) {
    var parts = String(text || "").trim().split(/\s+/);
    return parts.length ? parts[parts.length - 1] : "";
  }

  function optionText(select) {
    if (!select || select.selectedIndex < 0) return "";
    return select.options[select.selectedIndex].text || "";
  }

  function submittedValue(checkbox) {
    if (!checkbox) return null;
    return checkbox.checked ? checkbox.value : null;
  }

  function calcTotal(o) {
    var producto = document.getElementById(o.productoId);
    var moldesEl = document.getElementById(o.moldesId);
    var pesoEl = document.getElementById(o.pesoId);
    var precioEl = document.getElementById(o.precioId);
    var roundEl = o.roundId ? document.getElementById(o.roundId) : null;
    if (!producto || !moldesEl || !pesoEl || !precioEl) return null;

    var tipo = lastWord(optionText(producto));
    if (tipo !== "Kilo" && tipo !== "Unidad") return null;

    var moldes = parseNum(moldesEl.value);
    var peso = parseNum(pesoEl.value);
    var precio = parseNum(precioEl.value);
    if (isNaN(precio)) return null;

    var base;
    if (tipo === "Kilo") {
      if (isNaN(peso)) return null;
      base = peso * precio;
    } else {
      if (isNaN(moldes)) return null;
      base = precio * moldes;
    }

    // Igual que PHP ($request->tipo == 0 => 2 decimales): ausente (null) y "0"
    // redondean a 2 decimales; solo el valor "1" redondea a entero.
    // (En JS null == 0 es false, al revés que en PHP; por eso se compara contra 1.)
    var enviado = submittedValue(roundEl);
    var total = (enviado == 1) ? Math.round(base) : Math.round(base * 100) / 100;
    return { total: total, tipo: tipo };
  }

  function fmt(n) {
    return n.toLocaleString("es-BO", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  window.initVentaTotal = function (o) {
    var totalVivo = document.getElementById(o.totalId);
    var restoVivo = o.restoId ? document.getElementById(o.restoId) : null;
    var acuentaEl = o.acuentaId ? document.getElementById(o.acuentaId) : null;
    var ids = [o.productoId, o.moldesId, o.pesoId, o.precioId];
    if (o.roundId) ids.push(o.roundId);
    if (o.acuentaId) ids.push(o.acuentaId);

    function refresh() {
      var r = calcTotal(o);
      if (!r) {
        if (totalVivo) totalVivo.textContent = "—";
        if (restoVivo) restoVivo.textContent = "—";
        return null;
      }
      if (totalVivo) totalVivo.textContent = fmt(r.total);
      if (restoVivo && acuentaEl) {
        var acuenta = parseNum(acuentaEl.value);
        restoVivo.textContent = isNaN(acuenta) ? "—" : fmt(r.total - acuenta);
      }
      return r;
    }

    ids.forEach(function (id) {
      var el = document.getElementById(id);
      if (!el) return;
      el.addEventListener("input", refresh);
      el.addEventListener("change", refresh);
    });
    refresh();

    // Confirmación con resumen antes de enviar.
    var form = document.getElementById(o.formId);
    if (form && typeof Swal !== "undefined") {
      form.addEventListener("submit", function (e) {
        if (form.dataset.confirmado === "1") return;
        e.preventDefault();
        var r = refresh();
        var clienteSel = o.clienteId ? document.getElementById(o.clienteId) : null;
        var loteSel = o.loteId ? document.getElementById(o.loteId) : null;
        var filas = [
          ["Cliente", clienteSel ? optionText(clienteSel) : "—"],
          ["Producto", optionText(document.getElementById(o.productoId))],
          ["Lote", loteSel ? optionText(loteSel) : "—"],
          ["Moldes", document.getElementById(o.moldesId).value || "—"],
          ["Peso", document.getElementById(o.pesoId).value || "—"],
          ["Precio", document.getElementById(o.precioId).value || "—"],
          ["Total", r ? fmt(r.total) + " Bs" : "—"]
        ];
        if (acuentaEl) filas.push(["A cuenta", acuentaEl.value || "0.00"]);
        var html = '<div style="text-align:left">' + filas.map(function (f) {
          return "<p><strong>" + f[0] + ":</strong> " + String(f[1]).replace(/</g, "&lt;") + "</p>";
        }).join("") + "</div>";
        Swal.fire({
          title: "Confirmar venta",
          html: html,
          icon: "question",
          showCancelButton: true,
          confirmButtonText: "Vender",
          cancelButtonText: "Revisar"
        }).then(function (res) {
          if (res.isConfirmed) {
            form.dataset.confirmado = "1";
            var carga = document.getElementById("contenedor_carga");
            if (carga) carga.style.visibility = "visible";
            var btn = form.querySelector('button[type="submit"], button:not([type])');
            if (btn) btn.disabled = true;
            form.submit();
          }
        });
      });
    }
  };
})();
