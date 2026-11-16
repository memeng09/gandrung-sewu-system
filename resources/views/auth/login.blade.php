<x-auth-kartu aktif="masuk" judul="Selamat Datang Kembali!" ket="Masuk untuk melihat status pendaftaran dan mengunduh e-sertifikat.">
  @if (session('status'))<div class="mb-4 rounded-xl bg-green-100 px-4 py-3 text-sm text-green-900">{{ session('status') }}</div>@endif
  <form method="post" action="{{ route('login') }}" class="space-y-4" x-data="{lihat:false}">@csrf
    <div><label class="lbl" for="email">Email</label>
      <input id="email" type="email" name="email" value="{{ old('email') }}" class="inp" required autofocus autocomplete="username">
      @error('email')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror</div>
    <div><label class="lbl" for="password">Password</label>
      <div class="relative"><input id="password" :type="lihat ? 'text' : 'password'" name="password" class="inp pr-20" required autocomplete="current-password">
        <button type="button" @click="lihat=!lihat" class="absolute inset-y-0 right-3 text-xs font-semibold text-[#8B1E2D]" x-text="lihat ? 'Sembunyikan' : 'Lihat'"></button></div>
      @error('password')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror</div>
    <div class="flex items-center justify-between text-sm">
      <label class="flex items-center gap-2"><input type="checkbox" name="remember" class="rounded border-[#CDBFA6] text-[#8B1E2D] focus:ring-[#8B1E2D]"> Ingat saya</label>
      @if (Route::has('password.request'))<a href="{{ route('password.request') }}" class="font-semibold text-[#8B1E2D]">Lupa password?</a>@endif
    </div>
    <button class="btn w-full">Masuk</button>
  </form>
  <p class="mt-5 text-center text-sm text-[#6B5A50]">Belum punya akun? <a href="{{ route('register') }}" class="font-semibold text-[#8B1E2D]">Daftar sekarang</a></p>
</x-auth-kartu>