<x-auth-kartu aktif="daftar" judul="Daftar Akun Penari" ket="Khusus penari yang sudah menerima kartu kode dari juri. Kode dimasukkan saat mengisi data diri.">
  <form method="post" action="{{ route('register') }}" class="space-y-4">@csrf
    <div><label class="lbl" for="name">Nama lengkap</label>
      <input id="name" name="name" value="{{ old('name') }}" class="inp" required autofocus autocomplete="name">
      @error('name')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror</div>
    <div><label class="lbl" for="email">Email</label>
      <input id="email" type="email" name="email" value="{{ old('email') }}" class="inp" required autocomplete="username">
      @error('email')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror</div>
    <div class="grid gap-4 sm:grid-cols-2">
      <div><label class="lbl" for="password">Password</label><input id="password" type="password" name="password" class="inp" required autocomplete="new-password">
        @error('password')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror</div>
      <div><label class="lbl" for="password_confirmation">Ulangi password</label><input id="password_confirmation" type="password" name="password_confirmation" class="inp" required autocomplete="new-password"></div>
    </div>
    <button class="btn w-full">Buat akun</button>
  </form>
  <p class="mt-5 text-center text-sm text-[#6B5A50]">Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-[#8B1E2D]">Masuk</a></p>
</x-auth-kartu>