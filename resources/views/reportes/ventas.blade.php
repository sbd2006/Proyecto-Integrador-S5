@extends('layout.dashboard')
@section('titulomain') @endsection

@section('reportes_content')
<style>
  :root{
    --brand:#8b3a62; --brand2:#6f2c4d; --bg:#fff; --muted:#6b7280; --bd:#ead1db;
  }
  .wrap{max-width: 1024px; margin: 0 auto;}
  .card{background:var(--bg); border:1px solid rgba(139,58,98,.10); border-radius:16px; padding:18px; box-shadow:0 10px 22px rgba(139,58,98,.08),0 2px 6px rgba(0,0,0,.04);}
  .title{font-size:24px;font-weight:800;color:var(--brand2);margin:0 0 4px; display:flex; gap:8px; align-items:center;}
  .sub{color:var(--muted); margin:0 0 12px;}
  .grid{display:grid; grid-template-columns:1fr; gap:12px;}
  @media (min-width:768px){ .grid-2{grid-template-columns:1fr 1fr;} .grid-3{grid-template-columns:repeat(3,1fr);} }
  .label{display:block;font-weight:700;font-size:13px;margin-bottom:6px;color:#4b5563;}
  .input,.select{width:100%;border:1.5px solid var(--bd);padding:10px 12px;border-radius:10px;outline:none;background:#fff;transition:.15s;border-color;}
  .input:focus,.select:focus{border-color:var(--brand); box-shadow:0 0 0 3px rgba(139,58,98,.15);}
  .actions{display:flex;align-items:center;gap:10px;flex-wrap:wrap;justify-content:flex-end;}
  .btn{appearance:none;border:0;border-radius:10px;padding:10px 14px;font-weight:700;cursor:pointer;}
  .btn-primary{background:var(--brand);color:#fff;}
  .btn-primary:hover{background:var(--brand2);}
  .link{color:var(--brand);text-decoration:none;font-weight:700;}
  .link:hover{text-decoration:underline;}
  .kpis{display:grid;gap:10px;grid-template-columns:1fr; margin-top:14px;}
  @media (min-width:768px){ .kpis{grid-template-columns:repeat(4,1fr);} }
  .kpi{background:#fff;border:1px solid rgba(139,58,98,.1);border-radius:14px;padding:14px;}
  .kpi .k{font-size:12px;color:#6b7280;margin:0;}
  .kpi .v{font-size:20px;font-weight:800;margin:2px 0 0;color:#1f2937;}
  .table{width:100%;border-collapse:collapse;}
  .table th,.table td{border:1px solid #eee;padding:8px;text-align:left;font-size:14px;}
  .table th{background:#faf5f7;}
  .muted{color:#6b7280;}
  .chips{display:flex;gap:8px;margin-top:6px;flex-wrap:wrap;}
  .chip{border:1px dashed var(--bd);background:#fff;color:var(--brand2);padding:6px 10px;border-radius:999px;font-size:12px;cursor:pointer;}
</style>

<div class="wrap">
  <div class="card">
    <h1 class="title">📊 Resumen de ventas</h1>
    <p class="sub">Visión general de ingresos, órdenes y métodos de pago en el período.</p>

    <form method="GET" action="{{ route('reportes.ventas.resumen') }}">
      <div class="grid grid-2">
        <div>
          <label class="label" for="desde">Desde</label>
          <input type="date" id="desde" name="desde" value="{{ old('desde', $desde) }}" class="input" required>
          <div class="chips">
            <button type="button" class="chip" data-range="hoy">Hoy</button>
            <button type="button" class="chip" data-range="mes">Mes actual</button>
            <button type="button" class="chip" data-range="anio">Año actual</button>
          </div>
        </div>
        <div>
          <label class="label" for="hasta">Hasta</label>
          <input type="date" id="hasta" name="hasta" value="{{ old('hasta', $hasta) }}" class="input" required>
        </div>
      </div>

      <div class="grid grid-3" style="margin-top:10px;">
        <div>
          <label class="label" for="status">Estado</label>
          <select id="status" name="status" class="select">
            @php $sel = old('status', $status); @endphp
            <option value="todos"    {{ $sel==='todos'?'selected':'' }}>Todos</option>
            <option value="pagado"   {{ $sel==='pagado'?'selected':'' }}>Pagado</option>
            <option value="pendiente"{{ $sel==='pendiente'?'selected':'' }}>Pendiente</option>
            <option value="cancelado"{{ $sel==='cancelado'?'selected':'' }}>Cancelado</option>
          </select>
        </div>
        <div>
          <label class="label" for="payment_method_id">Método de pago</label>
          <select id="payment_method_id" name="payment_method_id" class="select">
            <option value="">Todos</option>
            @foreach($metodos as $m)
              <option value="{{ $m->id }}" {{ (string)$payment_method_id===(string)$m->id ? 'selected' : '' }}>
                {{ $m->nombre }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="actions" style="align-items:flex-end;">
          <button class="btn btn-primary">Aplicar</button>
          <a href="{{ route('reportes.ventas.resumen') }}" class="link">Restablecer</a>
        </div>
      </div>
    </form>

    {{-- KPIs --}}
    <div class="kpis">
      <div class="kpi">
        <p class="k">Ingresos</p>
        <p class="v">$ {{ number_format($total, 2, ',', '.') }}</p>
      </div>
      <div class="kpi">
        <p class="k">Órdenes</p>
        <p class="v">{{ number_format($ordenes) }}</p>
      </div>
      <div class="kpi">
        <p class="k">Ticket promedio</p>
        <p class="v">$ {{ number_format($ticket, 2, ',', '.') }}</p>
      </div>
      <div class="kpi">
        <p class="k">Por estado</p>
        <p class="v">
          @php
            $p = (int)($conteoPorEstado['pagado'] ?? 0);
            $pe = (int)($conteoPorEstado['pendiente'] ?? 0);
            $c = (int)($conteoPorEstado['cancelado'] ?? 0);
          @endphp
          ✔︎ {{ $p }} &nbsp; | &nbsp; ⏳ {{ $pe }} &nbsp; | &nbsp; ✖︎ {{ $c }}
        </p>
      </div>
    </div>

    <div class="grid grid-2" style="margin-top:16px;">
      <div class="card" style="padding:12px;">
        <h3 class="title" style="font-size:16px;">Por método de pago</h3>
        <table class="table">
          <thead>
            <tr>
              <th>Método</th>
              <th>Órdenes</th>
              <th>Subtotal</th>
            </tr>
          </thead>
          <tbody>
            @forelse($porMetodo as $row)
              <tr>
                <td>{{ $row['metodo'] }}</td>
                <td>{{ number_format($row['conteo']) }}</td>
                <td>$ {{ number_format($row['total'], 2, ',', '.') }}</td>
              </tr>
            @empty
              <tr><td colspan="3" class="muted">Sin datos.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="card" style="padding:12px;">
        <h3 class="title" style="font-size:16px;">Ventas por día</h3>
        <table class="table">
          <thead>
            <tr>
              <th>Fecha</th>
              <th>Órdenes</th>
              <th>Monto</th>
            </tr>
          </thead>
          <tbody>
            @forelse($porDia as $d)
              <tr>
                <td>{{ \Carbon\Carbon::parse($d->fecha)->format('d/m/Y') }}</td>
                <td>{{ number_format($d->ordenes) }}</td>
                <td>$ {{ number_format($d->monto, 2, ',', '.') }}</td>
              </tr>
            @empty
              <tr><td colspan="3" class="muted">Sin datos.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    @if($tieneDetalle)
      <div class="card" style="padding:12px; margin-top:16px;">
        <h3 class="title" style="font-size:16px;">Top productos (ingresos)</h3>
        <table class="table">
          <thead>
            <tr>
              <th>Producto</th>
              <th>Unidades</th>
              <th>Ingresos</th>
            </tr>
          </thead>
          <tbody>
            @forelse($topProductos as $r)
              <tr>
                <td>{{ $r->nombre }}</td>
                <td>{{ number_format($r->unidades) }}</td>
                <td>$ {{ number_format($r->ingresos, 2, ',', '.') }}</td>
              </tr>
            @empty
              <tr><td colspan="3" class="muted">Sin datos.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    @endif

  </div>
</div>

<script>
  (function(){
    const $d = document.getElementById('desde');
    const $h = document.getElementById('hasta');
    const chips = document.querySelectorAll('.chip');

    function fmt(d){ return d.toISOString().slice(0,10); }
    function setHoy(){ const now=new Date(); $d.value=fmt(now); $h.value=fmt(now); }
    function setMes(){
      const now=new Date(); const start=new Date(now.getFullYear(), now.getMonth(), 1);
      $d.value=fmt(start); $h.value=fmt(now);
    }
    function setAnio(){
      const now=new Date(); const start=new Date(now.getFullYear(), 0, 1);
      $d.value=fmt(start); $h.value=fmt(now);
    }
    chips.forEach(c => c.addEventListener('click', () => {
      const r=c.getAttribute('data-range');
      if(r==='hoy') setHoy();
      if(r==='mes') setMes();
      if(r==='anio') setAnio();
    }));
  })();
</script>
@endsection

{{-- compatibilidad con el layout --}}
@section('content')   @yield('reportes_content') @endsection
@section('contenido') @yield('reportes_content') @endsection
@section('main')      @yield('reportes_content') @endsection
@section('cuerpo')    @yield('reportes_content') @endsection


