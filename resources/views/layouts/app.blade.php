<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Boat Ticketing' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .breadcrumb { display:flex; align-items:center; gap:8px; margin:0 0 18px; font-size:12px; color:#718096; }
        .breadcrumb a { color:#718096; text-decoration:none; }
        .breadcrumb a:hover { color:#0969e8; }
        .breadcrumb .separator { color:#a0aec0; }
        .breadcrumb .current { color:#17263b; font-weight:600; }
    </style>
</head>
<body>
    <aside class="side">
        <div class="logo">⛴ BOAT TICKETING <small>OPERATIONS PLATFORM</small></div>

        <div class="navtitle">DERMAGA</div>
        <nav class="nav">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">⌂ Dashboard</a>
            <a href="{{ route('dermaga.control-center') }}" class="{{ request()->routeIs('dermaga.control-center') ? 'active' : '' }}">⚓ Control Center</a>
            <a href="{{ route('dermaga.checkin') }}" class="{{ request()->routeIs('dermaga.checkin') ? 'active' : '' }}">▣ Check-in / Scan</a>
            <a href="{{ route('dermaga.boarding') }}" class="{{ request()->routeIs('dermaga.boarding*') ? 'active' : '' }}">♟ Boarding</a>
            <a href="{{ route('dermaga.dispatch') }}" class="{{ request()->routeIs('dermaga.dispatch*') ? 'active' : '' }}">⛴ Dispatch</a>
            <a href="{{ route('dermaga.manifest') }}" class="{{ request()->routeIs('dermaga.manifest') ? 'active' : '' }}">▤ Manifest</a>
        </nav>

        <div class="navtitle">OPERASIONAL</div>
        <nav class="nav">
            <a href="{{ route('master.customers.index') }}" class="{{ request()->routeIs('master.customers.*') ? 'active' : '' }}">♟ Customer</a>
            <a href="{{ route('ticketing.index') }}" class="{{ request()->routeIs('ticketing.*') ? 'active' : '' }}">🎟 Ticketing / Kasir</a>
            <a href="{{ route('operasional.pooling') }}" class="{{ request()->routeIs('operasional.pooling*') ? 'active' : '' }}">♟ Pooling</a>
            <a href="{{ route('operasional.queue') }}" class="{{ request()->routeIs('operasional.queue*') ? 'active' : '' }}">☷ Antrian Boat</a>
            <a href="{{ route('operasional.exception') }}" class="{{ request()->routeIs('operasional.exception') ? 'active' : '' }}">⚠ Exception</a>
        </nav>

        <div class="navtitle">MASTER DATA</div>
        <nav class="nav">
            <a href="{{ route('master.boats.index') }}" class="{{ request()->routeIs('master.boats.*') ? 'active' : '' }}">⛴ Boat</a>
            <a href="{{ route('master.schedules.index') }}" class="{{ request()->routeIs('master.schedules.*') ? 'active' : '' }}">▦ Trip / Jadwal</a>
            <a href="{{ route('master.agents.index') }}" class="{{ request()->routeIs('master.agents.*') ? 'active' : '' }}">♟ Agent</a>
            <a href="{{ route('master.services.index') }}" class="{{ request()->routeIs('master.services.*') ? 'active' : '' }}">▤ Tarif</a>
        </nav>
    </aside>

    <main class="main">
        <header class="top">
            <b>Boat Ticketing</b>
            <span class="online">● LOCAL ONLINE</span>
            <span id="cloud">● CLOUD SYNC</span>
            <span class="spacer"></span>
            <span>🔔 0</span>
            @auth
                <span class="user">{{ auth()->user()->name }}</span>
                <form class="logout-form" method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="logout-btn">Logout</button></form>
            @endauth
        </header>

        <div class="wrap">
            @php
                $breadcrumbs = match (true) {
                    request()->routeIs('dashboard') => [['label'=>'Dashboard','url'=>null]],
                    request()->routeIs('dermaga.*') => [['label'=>'Dermaga','url'=>null], ['label'=>match(true, request()->routeIs('dermaga.control-center')=>'Control Center', request()->routeIs('dermaga.checkin')=>'Check-in / Scan', request()->routeIs('dermaga.boarding*')=>'Boarding', request()->routeIs('dermaga.dispatch*')=>'Dispatch', default=>'Manifest'),'url'=>null]],
                    request()->routeIs('ticketing.*') => [['label'=>'Operasional','url'=>null],['label'=>'Ticketing / Kasir','url'=>null]],
                    request()->routeIs('operasional.*') => [['label'=>'Operasional','url'=>null],['label'=>match(true, request()->routeIs('operasional.pooling*')=>'Pooling', request()->routeIs('operasional.queue*')=>'Antrian Boat', default=>'Exception'),'url'=>null]],
                    request()->routeIs('master.*') => [['label'=>'Master Data','url'=>null],['label'=>match(true, request()->routeIs('master.boats.*')=>'Boat', request()->routeIs('master.customers.*')=>'Customer', request()->routeIs('master.agents.*')=>'Agent', request()->routeIs('master.schedules.*')=>'Trip / Jadwal', default=>'Tarif'),'url'=>null]],
                    default => [],
                };
            @endphp
            @if ($breadcrumbs)
                <nav class="breadcrumb" aria-label="Breadcrumb">
                    <a href="{{ route('dashboard') }}">Home</a>
                    @foreach ($breadcrumbs as $breadcrumb)<span class="separator">/</span><span class="current">{{ $breadcrumb['label'] }}</span>@endforeach
                </nav>
            @endif
            @yield('content')
        </div>
    </main>

    <script>
        async function status() {
            const element = document.getElementById('cloud');
            try { await fetch('{{ url('/offline-status') }}', {cache:'no-store'}); element.textContent='● CLOUD SYNC'; element.style.color='#19a15f'; }
            catch (error) { element.textContent='⚠ CLOUD OFFLINE'; element.style.color='#d56b10'; }
        }
        status(); setInterval(status,15000);
    </script>
</body>
</html>
