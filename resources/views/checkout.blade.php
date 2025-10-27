@extends('cliente.pedidos')
{{-- Quitamos el título del layout --}}
@section('titulomain') @endsection

@section('contenido')
<style>
  :root {
    --brand: #a64d79;
    --brand-600: #8b3f67;
    --bg: #fff7fa;
    --card: #ffffff;
    --muted: #6b7280;
    --ring: #f4cfe0;
    --ok: #d1fae5;
  }

  /* ✅ Para que el redimensionado nunca sobrepase el contenedor */
  *,
  *::before,
  *::after {
    box-sizing: border-box;
  }

  body {
    background: var(--bg);
  }

  .wrap {
    max-width: 860px;
    margin: auto auto;
    margin-top: 85px;
    margin-bottom: 24px;
    padding: 0 16px;
  }

  .title {
    color: var(--brand);
    font-weight: 800;
    margin-bottom: 16px;
  }

  .alert {
    background: var(--ok);
    padding: 12px 14px;
    border-radius: 10px;
    margin-bottom: 16px;
  }

  .errors {
    background: #fee2e2;
    color: #7f1d1d;
    padding: 10px 12px;
    border-radius: 10px;
    margin-bottom: 16px;
  }

  .card {
    background: var(--card);
    border: 1px solid #eee;
    border-radius: 14px;
    box-shadow: 0 8px 18px rgba(0, 0, 0, .05);
    padding: 18px;
  }

  .grid {
    display: grid;
    gap: 16px;
  }

  .field label {
    display: block;
    font-weight: 600;
    margin-bottom: 6px;
    color: #374151;
  }

  .field input[type="number"],
  .field input[type="email"],
  .field textarea {
    width: 100%;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 10px 12px;
    outline: none;
    transition: .15s;
    background: #fff;
  }

  .field input:focus,
  .field textarea:focus {
    border-color: var(--brand);
    box-shadow: 0 0 0 4px var(--ring);
  }

  /* ✅ El textarea solo crece verticalmente y no invade el botón */
  .field textarea {
    resize: vertical;
    /* solo alto */
    min-height: 84px;
    /* altura base agradable */
    max-height: 360px;
    /* límite para no romper la tarjeta */
  }

  /* radios como cards */
  .methods {
    display: grid;
    gap: 12px;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  }

  .method {
    position: relative;
  }

  .method input {
    position: absolute;
    inset: 0;
    opacity: 0;
  }

  .method .cardx {
    display: flex;
    gap: 12px;
    align-items: center;
    border: 2px solid #eee;
    border-radius: 14px;
    padding: 12px 14px;
    background: #fff;
    cursor: pointer;
    transition: .15s;
  }

  .method .icon {
    font-size: 28px;
    line-height: 1;
  }

  .method .txt b {
    display: block;
    color: #111827;
  }

  .method .txt span {
    display: block;
    color: var(--muted);
    font-size: .92rem;
  }

  .method input:checked+.cardx {
    border-color: var(--brand);
    box-shadow: 0 0 0 4px var(--ring);
  }

  .method .cardx:hover {
    transform: translateY(-2px);
  }

  .actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 6px;
  }

  .bttn {
    background: var(--brand);
    color: #fff;
    border: 0;
    border-radius: 12px;
    padding: 10px 16px;
    font-weight: 700;
    cursor: pointer;
    transition: .15s;
  }

  .bttn:hover {
    background: var(--brand-600);
    transform: translateY(-1px);
  }

  .hint {
    color: var(--muted);
    font-size: .92rem;
    margin-top: 6px;
  }
</style>

<div class="wrap">
  <h1 class="title">Pago</h1>

  @if(session('ok'))
  <div class="alert">{{ session('ok') }}</div>
  @endif

  @if($errors->any())
  <div class="errors">
    <ul style="margin:0;padding-left:18px;">
      @foreach($errors->all() as $e)
      <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
  @endif

  <form method="POST" action="{{ route('checkout.pagar') }}" class="card grid">
    @csrf

    <input type="hidden" name="pedido_id" value="{{ $pedido->id }}">


    <div class="field">
      <label>Total</label>
      <input type="number" name="total" step="0.01" min="0"
        value="{{ old('total', $pedido->total) }}" readonly required>

      <p class="hint">Este valor es solo de prueba.</p>
    </div>

    @guest
    <div class="field">
      <label>Correo (opcional, para mostrarlo en la factura)</label>
      <input type="email" name="email" value="{{ old('email') }}" placeholder="tucorreo@dominio.com">
    </div>
    @endguest

    <div class="field">
      <label>Método de pago</label>
      <div class="methods">
        @foreach($metodos as $m)
        @php
        $icon = ['bancolombia'=>'🏦','datafono'=>'💳','efectivo'=>'💵','nequi'=>'📲'][$m->slug] ?? '💠';
        @endphp
        <label class="method">
          <input type="radio" name="payment_method_id" value="{{ $m->id }}"
            {{ old('payment_method_id')==$m->id ? 'checked' : '' }} required>
          <div class="cardx">
            <div class="icon">{{ $icon }}</div>
            <div class="txt">
              <b>{{ $m->nombre }}</b>
              <span>{{ $m->descripcion ?? $m->instrucciones }}</span>
            </div>
          </div>
        </label>
        @endforeach
      </div>
    </div>

    <div class="field">
      <label>Notas</label>
      <textarea name="notas" rows="2" placeholder="Instrucciones o comentario…">{{ old('notas') }}</textarea>
    </div>

    <div class="actions">
      <button type="submit" class="bttn">Simular pago</button>
    </div>
  </form>
</div>
@endsection