@if (session('ok'))<div class="flash" role="status">{{ session('ok') }}</div>@endif
@if ($errors->any() && ! request()->routeIs('login', 'sim.login', 'eksekutif.login', 'daftar', 'password.request', 'password.reset', 'akun.password'))
<div class="flash bad" role="alert">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
@endif
