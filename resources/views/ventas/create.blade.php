@extends('admin.dashboard')
@section('titulomain') @endsection

@section('content')
<div class="container mx-auto max-w-3xl">
  @if(session('ok'))
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
      {{ session('ok') }}
      @if(session('factura_url'))
        — <a href="{{ session('factura_url') }}" target="_blank" class="underline">Ver factura PDF</a>
      @endif
    </div>
  @endif
  @if(session('error'))
    <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
  @endif
  @if ($errors->any())
    <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
      <ul class="list-disc ml-5">
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <div class="bg-white shadow rounded p-6">
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-2xl font-semibold">Venta rápida</h1>
      <a href="{{ route('reportes.ventas') }}" class="text-sm px-3 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700" target="_blank">
        📈 Reporte de ventas
      </a>
    </div>

    <form method="POST" action="{{ route('ventas.rapida.store') }}" class="space-y-5">
      @csrf

      <div>
        <label class="block text-sm font-medium mb-1" for="total">Total a cobrar</label>
        <input type="number" step="0.01" min="0.01" id="total" name="total"
               value="{{ old('total') }}" class="w-full border rounded px-3 py-2" placeholder="0.00" required>
      </div>

      <div>
        <p class="block text-sm font-medium mb-2">Método de pago</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          @foreach($metodos as $m)
            <label class="flex items-center border rounded p-3 cursor-pointer hover:bg-gray-50">
              <input type="radio" class="mr-3" name="payment_method_id" value="{{ $m->id }}"
                     {{ old('payment_method_id') == $m->id ? 'checked' : '' }} required>
              <span class="font-medium">
                @switch($m->slug)
                  @case('bancolombia') 🏦 @break
                  @case('datafono') 💳 @break
                  @case('efectivo') 💵 @break
                  @case('nequi') 📲 @break
                  @default 💠
                @endswitch
                {{ $m->nombre }}
              </span>
            </label>
          @endforeach
        </div>
        @error('payment_method_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-medium mb-1" for="email">Email del cliente (opcional)</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}"
               class="w-full border rounded px-3 py-2" placeholder="cliente@correo.com">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1" for="notas">Notas (opcional)</label>
        <textarea id="notas" name="notas" rows="3" class="w-full border rounded px-3 py-2"
                  style="resize: vertical; min-height: 80px; max-height: 200px;">{{ old('notas') }}</textarea>
      </div>

      <div class="flex items-center justify-end gap-3">
        <button type="reset" class="px-4 py-2 rounded border">Limpiar</button>
        <button type="submit" class="px-4 py-2 rounded bg-rose-600 text-white hover:bg-rose-700">
          Registrar venta
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
