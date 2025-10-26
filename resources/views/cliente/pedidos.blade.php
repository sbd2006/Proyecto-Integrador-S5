@extends('layouts.app')

@section('contenido')
<div class="contenedor-pedidos">
    <h1 class="titulo-seccion">🛍️ Mis Pedidos</h1>

    <div id="lista-pedidos" class="lista-pedidos">
        <p class="mensaje-cargando">Cargando tus pedidos...</p>
    </div>
</div>

<style>
    body {
        background-color: #fff8fa;
        font-family: 'Poppins', sans-serif;
        color: #4b1e2f;
    }

    .contenedor-pedidos {
        max-width: 1000px;
        margin: 40px auto;
        padding: 25px;
        background: #ffffff;
        border-radius: 18px;
        box-shadow: 0 6px 16px rgba(166, 77, 121, 0.15);
    }

    .titulo-seccion {
        color: #a64d79;
        text-align: center;
        font-weight: bold;
        font-size: 28px;
        margin-bottom: 30px;
        border-bottom: 3px solid #f3c3d4;
        display: inline-block;
        padding-bottom: 8px;
    }

    .lista-pedidos {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 22px;
    }

    .pedido-card {
        background-color: #fff7f9;
        border: 1px solid #f3c3d4;
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.07);
        transition: all 0.3s ease;
    }

    .pedido-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
    }

    .pedido-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .pedido-header h3 {
        color: #a64d79;
        margin: 0;
        font-size: 18px;
        font-weight: 600;
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

    .pedido-detalles {
        color: #4b1e2f;
        font-size: 15px;
        margin-top: 8px;
    }

    .pedido-detalles li {
        list-style-type: disc;
        margin-left: 16px;
    }

    .pedido-total {
        font-weight: bold;
        color: #a64d79;
        margin-top: 10px;
    }

    .pedido-fecha {
        margin-top: 6px;
        color: #6b7280;
        font-size: 14px;
    }

    .mensaje-cargando {
        text-align: center;
        color: #999;
        grid-column: 1 / -1;
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
axios.defaults.headers.common['X-CSRF-TOKEN'] = token;

function badgeEstado(estado) {
    return `<span class="estado ${estado}">${estado.replace('_', ' ')}</span>`;
}

async function cargarPedidos() {
    try {
        const res = await axios.get('{{ route("cliente.pedidos.json") }}');
        const pedidos = res.data;
        const cont = document.getElementById('lista-pedidos');
        cont.innerHTML = '';

        if (!pedidos.length) {
            cont.innerHTML = '<p class="mensaje-cargando">Aún no has realizado ningún pedido 🍰</p>';
            return;
        }

        pedidos.forEach(p => {
            const card = document.createElement('div');
            card.className = 'pedido-card';

            const detalles = p.detalles.map(d => `
                <li>${d.producto.nombre} <strong>x${d.cantidad}</strong></li>
            `).join('');

            const fecha = new Date(p.created_at);
            const fechaFormateada = fecha.toLocaleDateString('es-ES', {
                day: '2-digit', month: '2-digit', year: 'numeric'
            });
            const horaFormateada = fecha.toLocaleTimeString('es-ES', {
                hour: '2-digit', minute: '2-digit'
            });

            card.innerHTML = `
                <div class="pedido-header">
                    <h3>Pedido #${p.id}</h3>
                    ${badgeEstado(p.estado)}
                </div>

                <ul class="pedido-detalles">${detalles}</ul>
                <p class="pedido-total">Total: $${parseFloat(p.total).toFixed(2)}</p>
                <p class="pedido-fecha">📅 Fecha: ${fechaFormateada} — 🕒 ${horaFormateada}</p>
            `;

            cont.appendChild(card);
        });
    } catch (err) {
        console.error('❌ Error cargando pedidos del cliente:', err);
        document.getElementById('lista-pedidos').innerHTML =
            '<p class="mensaje-cargando text-red-500">Error al cargar tus pedidos.</p>';
    }
}

cargarPedidos();
</script>
@endsection
