<x-auth-kartu aktif="daftar" judul="Buat akun penari" ket="Khusus penari yang sudah menerima kartu kode dari juri. Kode dimasukkan setelah kamu masuk.">
  @if ($errors->any())
    <div role="alert" class="mb-4 flex items-start gap-2 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-900 ring-1 ring-red-200">
      <x-icon n="alert" class="mt-0.5 h-5 w-5"/><span>Ada isian yang perlu diperbaiki. Periksa pesan di bawah kolom.</span>
    </div>
  @endif

  <form method="post" action="{{ route('register') }}" class="space-y-4" x-data="{lihat:false, kirim:false}" @submit="kirim=true">@csrf
    <div>
      <label class="lbl" for="name">Nama lengkap</label>
      <div class="relative">
        <x-icon n="user" class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-[#9B8878]"/>
        <input id="name" name="name" value="{{ old('name') }}" class="inp pl-11 @error('name') !border-red-400 @enderror" required autofocus autocomplete="name" placeholder="Sesuai identitas">
      </div>
      @error('name')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
    </div>

    <div>
      <label class="lbl" for="email">Email</label>
      <div class="relative">
        <x-icon n="mail" class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-[#9B8878]"/>
        <input id="email" type="email" name="email" value="{{ old('email') }}" class="inp pl-11 @error('email') !border-red-400 @enderror" required autocomplete="username" placeholder="nama@email.com">
      </div>
      @error('email')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
      <div>
        <label class="lbl" for="password">Password</label>
        <input id="password" :type="lihat ? 'text' : 'password'" name="password" class="inp @error('password') !border-red-400 @enderror" required autocomplete="new-password" placeholder="Minimal 8 karakter">
        @error('password')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="lbl" for="password_confirmation">Ulangi password</label>
        <input id="password_confirmation" :type="lihat ? 'text' : 'password'" name="password_confirmation" class="inp" required autocomplete="new-password">
      </div>
    </div>
    <label class="flex items-center gap-2 text-sm"><input type="checkbox" x-model="lihat" class="rounded border-[#CDBFA6] text-[#8B1E2D] focus:ring-[#8B1E2D]"> Tampilkan password</label>

    <button class="btn w-full" :disabled="kirim"><span x-text="kirim ? 'Memproses...' : 'Buat akun'">Buat akun</span></button>
  </form>

  <p class="mt-6 text-center text-sm text-[#6B5A50]">Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-[#8B1E2D] hover:underline">Masuk</a></p>
</x-auth-kartu>