@extends('admin.dashboard')

@section('titulomain', 'Resumen de ventas')

@section('contenido')
<style>
  :root{ --brand:#8b3a62; --brand2:#6f2c4d; --bg:#fff; --muted:#6b7280; --bd:#ead1db; }
  .wrap{max-width:1280px;margin:0 auto;}
  .card{background:var(--bg);border:1px solid rgba(139,58,98,.10);border-radius:18px;padding:22px;box-shadow:0 12px 26px rgba(139,58,98,.08),0 2px 6px rgba(0,0,0,.04);}
  .resumen-card{border-radius:18px;overflow:clip;overflow:hidden;}
  .title{font-size:28px;font-weight:900;color:var(--brand2);margin:0 0 6px;display:flex;gap:8px;align-items:center;}
  .sub{color:var(--muted);margin:0 0 16px;}
  .grid{display:grid;gap:14px;}
  .grid-2{grid-template-columns:1fr; gap:18px;}
  @media (min-width:768px){ .grid-2{grid-template-columns:1fr 1fr; gap:28px;} }
  .grid-3{grid-template-columns:1fr}
  @media (min-width:768px){ .grid-3{grid-template-columns:repeat(3,1fr);} }
  .label{display:block;font-weight:800;font-size:13px;margin-bottom:6px;color:#4b5563;text-transform:uppercase;letter-spacing:.02em;}
  .input,.select{width:100%;border:1.6px solid var(--bd);padding:12px 1px;border-radius:12px;outline:none;background:#fff;transition:.15s;}
  .input:focus,.select:focus{border-color:var(--brand);box-shadow:0 0 0 3px rgba(139,58,98,.15);}
  .actions{display:flex;align-items:center;gap:10px;flex-wrap:wrap;justify-content:flex-end;}
  .btn{appearance:none;border:0;border-radius:12px;padding:12px 16px;font-weight:900;cursor:pointer;}
  .btn-primary{background:var(--brand);color:#fff;} .btn-primary:hover{background:var(--brand2);}
  .btn-outline{background:#fff;color:var(--brand2);border:2px solid rgba(139,58,98,.25);} .btn-outline:hover{border-color:var(--brand2);}
  .btn-ghost{background:transparent;color:var(--brand2);border:2px dashed rgba(139,58,98,.25);} .btn-ghost:hover{border-color:var(--brand2);}
  .kpis{display:grid;gap:12px;grid-template-columns:1fr;margin-top:14px;}
  @media (min-width:768px){ .kpis{grid-template-columns:repeat(4,1fr);} }
  .kpi{background:#fff;border:1px solid rgba(139,58,98,.1);border-radius:16px;padding:16px;}
  .kpi .k{font-size:13px;color:#6b7280;margin:0;text-transform:uppercase;letter-spacing:.03em;}
  .kpi .v{font-size:24px;font-weight:900;margin:4px 0 0;color:#1f2937;}
  .section{margin-top:18px;}
  .table{width:100%;border-collapse:collapse;}
  .table th,.table td{border:1px solid #eee;padding:10px;text-align:left;font-size:15px;}
  .table th{background:#faf5f7;font-weight:800;}
  .muted{color:#6b7280;}
  .chips{display:flex;gap:8px;margin-top:8px;flex-wrap:wrap;}
  .chip{border:1px dashed var(--bd);background:#fff;color:var(--brand2);padding:6px 10px;border-radius:999px;font-size:12px;cursor:pointer;}
  .table-wrap{overflow-x:auto;border:1px solid #f0d7de;border-radius:12px;}
</style>

<div class="wrap">
  <div class="card resumen-card">
    {{-- Título interno removido --}}
  

    <form method="GET" action="{{ route('reportes.ventas.resumen') }}">
      <div class="grid grid-2">
        <div>
          <label class="label" for="desde">Desde</label>
          <input type="date" id="desde" name="desde"
                 value="{{ old('desde', data_get($filters,'desde', data_get($filters,'from'))) }}"
                 class="input" required>
          <div class="chips">
            <button type="button" class="chip" data-range="hoy">Hoy</button>
            <button type="button" class="chip" data-range="mes">Mes actual</button>
            <button type="button" class="chip" data-range="anio">Año actual</button>
          </div>
        </div>
        <div>
          <label class="label" for="hasta">Hasta</label>
          <input type="date" id="hasta" name="hasta"
                 value="{{ old('hasta', data_get($filters,'hasta', data_get($filters,'to'))) }}"
                 class="input" required>
        </div>
      </div>

      <div class="grid grid-3 section">
        <div>
          <label class="label" for="status">Estado</label>
          @php $sel = old('status', data_get($filters,'status', data_get($filters,'estado','todos'))); @endphp
          <select id="status" name="status" class="select">
            <option value="todos"     {{ $sel==='todos'?'selected':'' }}>Todos</option>
          </select>
        </div>

        <div>
          <label class="label" for="payment_method_id">Método de pago</label>
          @php $pmSel = old('payment_method_id', data_get($filters,'payment_method_id')); @endphp
          <select id="payment_method_id" name="payment_method_id" class="select">
            <option value="">Todos</option>
            @foreach($metodos as $m)
              <option value="{{ $m->id }}" {{ (string)$pmSel===(string)$m->id ? 'selected' : '' }}>
                {{ $m->nombre }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="actions" style="align-items:flex-end;">
          <button class="btn btn-primary">Aplicar</button>

          {{-- Descargar PDF con los mismos filtros en nueva pestaña --}}
          <button type="submit"
                  class="btn btn-outline"
                  formaction="{{ route('reportes.ventas.resumen.pdf') }}"
                  formtarget="_blank">
            ⬇️ Descargar PDF
          </button>

          <button type="button" class="btn btn-ghost"
                  onclick="window.location='{{ route('reportes.ventas.resumen') }}'">
            Restablecer
          </button>
        </div>
      </div>
    </form>

    {{-- KPIs --}}
    <div class="kpis">
      <div class="kpi"><p class="k">Ingresos</p><p class="v">$ {{ number_format(data_get($kpis,'ingresos',0), 2, ',', '.') }}</p></div>
      <div class="kpi"><p class="k">Órdenes</p><p class="v">{{ number_format(data_get($kpis,'ordenes',0)) }}</p></div>
      <div class="kpi"><p class="k">Ticket promedio</p><p class="v">$ {{ number_format(data_get($kpis,'ticketPromedio',0), 2, ',', '.') }}</p></div>
      <div class="kpi">
        <p class="k">Por estado</p>
        <p class="v">
          @php
            $p  = (int) data_get($porEstado, 'pagado', 0);
            $pe = (int) data_get($porEstado, 'pendiente', 0);
            $c  = (int) data_get($porEstado, 'cancelado', 0);
          @endphp
          ✔︎ {{ $p }} &nbsp; | &nbsp; ⏳ {{ $pe }} &nbsp; | &nbsp; ✖︎ {{ $c }}
        </p>
      </div>
    </div>

    <div class="grid grid-2 section">
      <div class="card" style="padding:14px;">
        <h3 class="title" style="font-size:18px;">Por método de pago</h3>
        <div class="table-wrap">
          <table class="table">
            <thead><tr><th>Método</th><th>Órdenes</th><th>Subtotal</th></tr></thead>
            <tbody>
              @forelse($porMetodo as $row)
                <tr>
                  <td>{{ data_get($row,'metodo','—') }}</td>
                  <td>{{ number_format((int) data_get($row,'ordenes', data_get($row,'conteo',0))) }}</td>
                  <td>$ {{ number_format((float) data_get($row,'subtotal', data_get($row,'total',0)), 2, ',', '.') }}</td>
                </tr>
              @empty
                <tr><td colspan="3" class="muted">Sin datos.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="card" style="padding:14px;">
        <h3 class="title" style="font-size:18px;">Ventas por día</h3>
        <div class="table-wrap">
          <table class="table">
            <thead><tr><th>Fecha</th><th>Órdenes</th><th>Monto</th></tr></thead>
            <tbody>
              @forelse($porDia as $d)
                <tr>
                  <td>{{ \Carbon\Carbon::parse(data_get($d,'fecha'))->format('d/m/Y') }}</td>
                  <td>{{ number_format((int) data_get($d,'ordenes',0)) }}</td>
                  <td>$ {{ number_format((float) data_get($d,'monto',0), 2, ',', '.') }}</td>
                </tr>
              @empty
                <tr><td colspan="3" class="muted">Sin datos.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    @if(!empty($topProductos) && count($topProductos) > 0)
      <div class="card section" style="padding:14px;">
        <h3 class="title" style="font-size:18px;">Top productos (ingresos)</h3>
        <div class="table-wrap">
          <table class="table">
            <thead><tr><th>Producto</th><th>Unidades</th><th>Ingresos</th></tr></thead>
            <tbody>
              @forelse($topProductos as $r)
                <tr>
                  <td>{{ data_get($r,'producto', data_get($r,'nombre','—')) }}</td>
                  <td>{{ number_format((int) data_get($r,'unidades',0)) }}</td>
                  <td>$ {{ number_format((float) data_get($r,'ingresos',0), 2, ',', '.') }}</td>
                </tr>
              @empty
                <tr><td colspan="3" class="muted">Sin datos.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    @endif
  </div>
</div>

<script>
  (function(){
    const $d=document.getElementById('desde'); const $h=document.getElementById('hasta');
    const chips=document.querySelectorAll('.chip');
    const fmt=d=>d.toISOString().slice(0,10);
    chips.forEach(c=>c.addEventListener('click',()=>{
      const now=new Date();
      if(c.dataset.range==='hoy'){ $d.value=fmt(now); $h.value=fmt(now); }
      if(c.dataset.range==='mes'){ const s=new Date(now.getFullYear(),now.getMonth(),1); $d.value=fmt(s); $h.value=fmt(now); }
      if(c.dataset.range==='anio'){ const s=new Date(now.getFullYear(),0,1); $d.value=fmt(s); $h.value=fmt(now); }
    }));
  })();
</script>
@endsection
