<x-auth-kartu aktif="masuk" judul="Masuk ke akunmu" ket="Gunakan email dan password yang kamu daftarkan.">
  @if (session('status'))
    <div role="status" class="mb-4 rounded-xl bg-green-50 px-4 py-3 text-sm text-green-900 ring-1 ring-green-200">{{ session('status') }}</div>
  @endif
  @if ($errors->any())
    <div role="alert" class="mb-4 flex items-start gap-2 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-900 ring-1 ring-red-200">
      <x-icon n="alert" class="mt-0.5 h-5 w-5"/><span>Email atau password tidak cocok. Periksa kembali, lalu coba lagi.</span>
    </div>
  @endif

  <form method="post" action="{{ route('login') }}" class="space-y-5" x-data="{lihat:false, kirim:false}" @submit="kirim=true">@csrf
    <div>
      <label class="lbl" for="email">Email</label>
      <div class="relative">
        <x-icon n="mail" class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-[#9B8878]"/>
        <input id="email" type="email" name="email" value="{{ old('email') }}" class="inp pl-11 @error('email') !border-red-400 @enderror"
               required autofocus autocomplete="username" placeholder="nama@email.com">
      </div>
    </div>

    <div>
      <label class="lbl" for="password">Password</label>
      <div class="relative">
        <x-icon n="lock" class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-[#9B8878]"/>
        <input id="password" :type="lihat ? 'text' : 'password'" name="password" class="inp pl-11 pr-24" required autocomplete="current-password" placeholder="Masukkan password">
        <button type="button" @click="lihat=!lihat" :aria-pressed="lihat" class="absolute inset-y-0 right-3 my-auto h-8 rounded-lg px-2 text-xs font-semibold text-[#8B1E2D] hover:bg-[#8B1E2D]/5" x-text="lihat ? 'Sembunyikan' : 'Lihat'">Lihat</button>
      </div>
    </div>

    <div class="flex items-center justify-between gap-3 text-sm">
      <label class="flex items-center gap-2"><input type="checkbox" name="remember" class="rounded border-[#CDBFA6] text-[#8B1E2D] focus:ring-[#8B1E2D]"> Ingat saya</label>
           @php $waPanitia = config('gandrung.wa_panitia'); @endphp
      @if ($waPanitia)
        <a href="https://wa.me/{{ $waPanitia }}?text={{ rawurlencode("Halo panitia Gandrung Sewu, saya lupa password akun penari.\nNama lengkap: \nEmail akun: ") }}"
           target="_blank" rel="noopener" class="font-semibold text-[#8B1E2D] hover:underline">Lupa password?</a>
      @endif
    </div>

    <button class="btn w-full" :disabled="kirim"><span x-text="kirim ? 'Memproses...' : 'Masuk'">Masuk</span></button>
  </form>

  <p class="mt-6 text-center text-sm text-[#6B5A50]">Belum punya akun? <a href="{{ route('register') }}" class="font-semibold text-[#8B1E2D] hover:underline">Daftar sebagai penari</a></p>
</x-auth-kartu>