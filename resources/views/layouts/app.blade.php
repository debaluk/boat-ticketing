<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Boat Ticketing' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 18px;
            font-size: 12px;
            color: #718096;
        }

        .breadcrumb a {
            color: #718096;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            color: #0969e8;
        }

        .breadcrumb .separator {
            color: #a0aec0;
        }

        .breadcrumb .current {
            color: #17263b;
            font-weight: 600;
        }

        .nav a.nav-disabled {
            opacity: .45;
            cursor: not-allowed;
        }

        .nav a.nav-disabled:hover {
            background: transparent;
        }
    </style>
</head>
<body>
    {{-- =========================
         SIDEBAR
    ========================== --}}
    <aside class="side">
        <div class="logo">
            ⛴ BOAT TICKETING
            <small>OPERATIONS PLATFORM</small>
        </div>

        <div class="navtitle">DERMAGA</div>
        <nav class="nav">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                ⌂ Dashboard
            </a>

            <a href="{{ route('dermaga.control-center') }}" class="{{ request()->routeIs('dermaga.control-center') ? 'active' : '' }}">
                ⚓ Control Center
            </a>

            <a href="#" class="nav-disabled" aria-disabled="true" tabindex="-1">
                ▣ Check-in / Scan
            </a>
            <a href="#" class="nav-disabled" aria-disabled="true" tabindex="-1">
                ♟ Boarding
            </a>
            <a href="#" class="nav-disabled" aria-disabled="true" tabindex="-1">
                ⛴ Dispatch
            </a>
            <a href="#" class="nav-disabled" aria-disabled="true" tabindex="-1">
                ▤ Manifest
            </a>
        </nav>

        <div class="navtitle">OPERASIONAL</div>
        <nav class="nav">
            <a href="{{ route('master.customers.index') }}" class="{{ request()->routeIs('master.customers.*') ? 'active' : '' }}">
                ♟ Customer
            </a>

            <a href="{{ route('ticketing.index') }}" class="{{ request()->routeIs('ticketing.*') ? 'active' : '' }}">
                🎟 Ticketing / Kasir
            </a>

            <a href="#" class="nav-disabled" aria-disabled="true" tabindex="-1">
                ♟ Pooling
            </a>
            <a href="#" class="nav-disabled" aria-disabled="true" tabindex="-1">
                ☷ Antrian Boat
            </a>
            <a href="#" class="nav-disabled" aria-disabled="true" tabindex="-1">
                ⚠ Exception
            </a>
        </nav>

        <div class="navtitle">MASTER DATA</div>
        <nav class="nav">
            <a href="{{ route('master.boats.index') }}" class="{{ request()->routeIs('master.boats.*') ? 'active' : '' }}">
                ⛴ Boat
            </a>

            <a href="#" class="nav-disabled" aria-disabled="true" tabindex="-1">
                ▦ Trip / Jadwal
            </a>

            <a href="{{ route('master.agents.index') }}" class="{{ request()->routeIs('master.agents.*') ? 'active' : '' }}">
                ♟ Agent
            </a>

            <a href="#" class="nav-disabled" aria-disabled="true" tabindex="-1">
                ▤ Tarif
            </a>
        </nav>
    </aside>

    {{-- =========================
         MAIN
    ========================== --}}
    <main class="main">
        <header class="top">
            <b>Boat Ticketing</b>
            <span class="online">● LOCAL ONLINE</span>
            <span id="cloud">● CLOUD SYNC</span>
            <span class="spacer"></span>
            <span>🔔 0</span>

            @auth
                <span class="user">{{ auth()->user()->name }}</span>
                <form class="logout-form" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            @endauth
        </header>

        <div class="wrap">
            @php
                $breadcrumbs = match (true) {
                    request()->routeIs('dashboard') => [
                        ['label' => 'Dashboard', 'url' => null],
                    ],
                    request()->routeIs('dermaga.control-center') => [
                        ['label' => 'Dermaga', 'url' => null],
                        ['label' => 'Control Center', 'url' => null],
                    ],
                    request()->routeIs('ticketing.*') => [
                        ['label' => 'Operasional', 'url' => null],
                        ['label' => 'Ticketing / Kasir', 'url' => null],
                    ],
                    request()->routeIs('master.customers.*') => [
                        ['label' => 'Master Data', 'url' => null],
                        ['label' => 'Customer', 'url' => null],
                    ],
                    request()->routeIs('master.boats.*') => [
                        ['label' => 'Master Data', 'url' => null],
                        ['label' => 'Boat', 'url' => null],
                    ],
                    request()->routeIs('master.agents.*') => [
                        ['label' => 'Master Data', 'url' => null],
                        ['label' => 'Agent', 'url' => null],
                    ],
                    default => [],
                };
            @endphp

            @if (!empty($breadcrumbs))
                <nav class="breadcrumb" aria-label="Breadcrumb">
                    <a href="{{ route('dashboard') }}">Home</a>
                    @foreach ($breadcrumbs as $breadcrumb)
                        <span class="separator">/</span>
                        @if ($breadcrumb['url'])
                            <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['label'] }}</a>
                        @else
                            <span class="current">{{ $breadcrumb['label'] }}</span>
                        @endif
                    @endforeach
                </nav>
            @endif

            @yield('content')
        </div>
    </main>

    <script>
        async function status() {
            const element = document.getElementById('cloud');
            try {
                await fetch('{{ url('/offline-status') }}', { cache: 'no-store' });
                element.textContent = '● CLOUD SYNC';
                element.style.color = '#19a15f';
            } catch (error) {
                element.textContent = '⚠ CLOUD OFFLINE';
                element.style.color = '#d56b10';
            }
        }

        status();
        setInterval(status, 15000);
    </script>
</body>
</html>
