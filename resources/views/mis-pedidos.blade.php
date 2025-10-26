@extends('layouts.app')

@section('content')
<div class="container">
  <h1>Mis Pedidos</h1>
  <div id="pedidos-cliente"></div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
  axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
  const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  axios.defaults.headers.common['X-CSRF-TOKEN'] = token;

  async function cargarMisPedidos() {
    try {
      const res = await axios.get('{{ route("mis.pedidos.json") }}');
      const cont = document.getElementById('pedidos-cliente');
      const pedidos = res.data;
      cont.innerHTML = pedidos.map(p => {
        const detalles = p.detalles.map(d => `${d.producto.nombre} x${d.cantidad}`).join(', ');
        return `<div><strong>#${p.id}</strong> - Estado: <b>${p.estado}</b><br>${detalles}<br>Total: ${p.total}</div><hr>`;
      }).join('');
    } catch (err) {
      console.error('Error cargando mis pedidos', err);
    }
  }

  cargarMisPedidos();
  setInterval(cargarMisPedidos, 5000);
</script>
@endsection
