<x-panel title="Pengaturan Akun" sub="Perbarui data akun dan password.">
  <div class="grid gap-5 lg:grid-cols-2">
    <div class="box">@include('profile.partials.update-profile-information-form')</div>
    <div class="box">@include('profile.partials.update-password-form')</div>
  </div>
</x-panel>