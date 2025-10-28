@extends('admin.dashboard')

@section('title', 'Gestión de Pedidos')
@section('titulomain')

@section('contenido')
<div class="contenedor-pedidos">
    <div class="titulo-seccion">
        <h2>📦 Órdenes Recibidas</h2>
    </div>

    <div id="lista-pedidos" class="lista-pedidos">
        <p class="mensaje-cargando">Cargando pedidos...</p>
    </div>
</div>

<style>
    /* Contenedor general */
    .contenedor-pedidos {
        max-width: 1100px;
        margin: 0 auto;
        background: #fff9fb;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(166, 77, 121, 0.15);
    }

    .titulo-seccion h2 {
        color: #a64d79;
        font-size: 26px;
        text-align: center;
        margin-bottom: 25px;
        border-bottom: 2px solid #f3c3d4;
        padding-bottom: 10px;
    }

    /* Lista de tarjetas */
    .lista-pedidos {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 18px;
    }

    .pedido-card {
        background-color: #fff;
        border: 1px solid #f3c3d4;
        border-radius: 12px;
        padding: 18px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }

    .pedido-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 14px rgba(0,0,0,0.12);
    }

    .pedido-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .pedido-header h3 {
        color: #a64d79;
        font-size: 18px;
        margin: 0;
    }

    .estado {
        font-size: 14px;
        font-weight: bold;
        padding: 4px 10px;
        border-radius: 6px;
        color: #fff;
        text-transform: capitalize;
    }

    .estado.pendiente { background-color: #facc15; color: #4b1e2f; }
    .estado.en_preparacion { background-color: #fb923c; }
    .estado.listo { background-color: #22c55e; }
    .estado.entregado { background-color: #16a34a; }
    .estado.cancelado { background-color: #ef4444; }

    .pedido-info p {
        margin: 4px 0;
        color: #4b1e2f;
        font-size: 15px;
    }

    .pedido-detalles {
        margin: 10px 0;
        padding-left: 18px;
        color: #4b1e2f;
        font-size: 14px;
    }

    .pedido-detalles li {
        list-style-type: disc;
        margin-bottom: 2px;
    }

    .pedido-total {
        font-weight: bold;
        color: #a64d79;
        margin-top: 8px;
    }

    .acciones {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 12px;
    }

    .acciones button {
        border: none;
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 13px;
        cursor: pointer;
        color: #fff;
        transition: 0.2s;
    }

    .acciones button:hover { opacity: 0.9; }

    .btn-preparar { background-color: #3b82f6; }
    .btn-listo { background-color: #22c55e; }
    .btn-entregado { background-color: #059669; }
    .btn-cancelar { background-color: #ef4444; }

    .mensaje-cargando {
        text-align: center;
        color: #777;
        grid-column: 1 / -1;
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;

function renderEstadoBadge(estado) {
    return `<span class="estado ${estado}">${estado.replace('_', ' ')}</span>`;
}

async function cargarPedidos() {
    try {
        const res = await axios.get('{{ route("pedidos.json") }}');
        const pedidos = res.data;
        const cont = document.getElementById('lista-pedidos');
        cont.innerHTML = '';

        if (!pedidos.length) {
            cont.innerHTML = '<p class="mensaje-cargando">No hay pedidos registrados.</p>';
            return;
        }

        pedidos.forEach(p => {
            const card = document.createElement('div');
            card.className = 'pedido-card';
            const detalles = p.detalles.map(d => `<li>${d.producto.nombre} <strong>x${d.cantidad}</strong></li>`).join('');

            card.innerHTML = `
                <div class="pedido-header">
                    <h3>Pedido #${p.id}</h3>
                    ${renderEstadoBadge(p.estado)}
                </div>
                <div class="pedido-info">
                    <p><strong>Cliente:</strong> ${p.cliente?.name ?? '—'}</p>
                </div>
                <ul class="pedido-detalles">${detalles}</ul>
                <div class="pedido-total">Total: $${parseFloat(p.total).toFixed(2)}</div>

                <div class="acciones">
                    <button onclick="cambiarEstado(${p.id}, 'en_preparacion')" class="btn-preparar">🧑‍🍳 Preparar</button>
                    <button onclick="cambiarEstado(${p.id}, 'listo')" class="btn-listo">✅ Listo</button>
                    <button onclick="cambiarEstado(${p.id}, 'entregado')" class="btn-entregado">📦 Entregado</button>
                    <button onclick="cambiarEstado(${p.id}, 'cancelado')" class="btn-cancelar">❌ Cancelar</button>
                </div>
            `;
            cont.appendChild(card);
        });
    } catch (err) {
        console.error('Error cargando pedidos:', err);
        document.getElementById('lista-pedidos').innerHTML = 
            '<p class="mensaje-cargando text-red-500">Error al cargar los pedidos.</p>';
    }
}

async function cambiarEstado(id, estado) {
    try {
        await axios.patch(`/pedidos/${id}/estado`, { estado });
        cargarPedidos();
    } catch (err) {
        console.error('Error actualizando estado', err);
    }
}

cargarPedidos();
setInterval(cargarPedidos, 5000);
</script>
@endsection