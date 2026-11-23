@php $pesan = [['ok', 'bg-green-50 text-green-900 ring-green-200', 'check', 'status'], ['err', 'bg-red-50 text-red-900 ring-red-200', 'alert', 'alert']]; @endphp
@foreach ($pesan as [$kunci, $cls, $ikon, $peran])
  @if (session($kunci))
    <div role="{{ $peran }}" x-data="{tampil:true}" x-show="tampil" class="flex items-start gap-3 rounded-xl px-4 py-3 text-sm ring-1 {{ $cls }}">
      <x-icon :n="$ikon" class="mt-0.5 h-5 w-5"/>
      <div class="flex-1">{{ session($kunci) }}</div>
      <button type="button" @click="tampil=false" aria-label="Tutup pesan" class="rounded p-0.5 opacity-60 hover:opacity-100"><x-icon n="x" class="h-4 w-4"/></button>
    </div>
  @endif
@endforeach
@if ($errors->any())
  <div role="alert" class="flex items-start gap-3 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-900 ring-1 ring-red-200">
    <x-icon n="alert" class="mt-0.5 h-5 w-5"/>
    <div class="flex-1 space-y-0.5">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
  </div>
@endif