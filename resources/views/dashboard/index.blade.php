<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        {{ $title ?? 'Boat Ticketing' }}
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    @stack('styles')
</head>

<body>

    {{-- =========================================================
         SIDEBAR
    ========================================================== --}}

    <aside class="side">

        <div class="logo">
            ⛴ BOAT TICKETING

            <small>
                OPERATIONS PLATFORM
            </small>
        </div>


        {{-- =====================================================
             DERMAGA
        ====================================================== --}}

        <div class="navtitle">
            DERMAGA
        </div>

        <nav class="nav">

            <a
                href="{{ route('dashboard') }}"
                class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"
            >
                ⌂ Dashboard
            </a>

            <a
                href="{{ url('/dermaga/control-center') }}"
                class="{{ request()->is('dermaga/control-center*') ? 'active' : '' }}"
            >
                ⚓ Control Center
            </a>

            <a href="#">
                ▣ Check-in / Scan
            </a>

            <a href="#">
                ♟ Boarding
            </a>

            <a href="#">
                ⛴ Dispatch
            </a>

            <a href="#">
                ▤ Manifest
            </a>

        </nav>


        {{-- =====================================================
             OPERASIONAL
        ====================================================== --}}

        <div class="navtitle">
            OPERASIONAL
        </div>

        <nav class="nav">

            <a
                href="{{ route('master.customers.index') }}"
                class="{{ request()->routeIs('master.customers.*') ? 'active' : '' }}"
            >
                ♟ Customer
            </a>

            <a
                href="{{ route('ticketing.index') }}"
                class="{{ request()->routeIs('ticketing.*') ? 'active' : '' }}"
            >
                🎟 Ticketing / Kasir
            </a>

            <a href="#">
                ♟ Pooling
            </a>

            <a href="#">
                ☷ Antrian Boat
            </a>

            <a href="#">
                ⚠ Exception
            </a>

        </nav>


        {{-- =====================================================
             MASTER DATA
        ====================================================== --}}

        <div class="navtitle">
            MASTER DATA
        </div>

        <nav class="nav">

            <a
                href="{{ route('master.boats.index') }}"
                class="{{ request()->routeIs('master.boats.*') ? 'active' : '' }}"
            >
                ⛴ Boat
            </a>

            <a href="#">
                ▦ Trip / Jadwal
            </a>

            <a
                href="{{ route('master.agents.index') }}"
                class="{{ request()->routeIs('master.agents.*') ? 'active' : '' }}"
            >
                ♟ Agent
            </a>

            <a href="#">
                ▤ Tarif
            </a>

        </nav>

    </aside>


    {{-- =========================================================
         MAIN
    ========================================================== --}}

    <main class="main">


        {{-- =====================================================
             TOP BAR
        ====================================================== --}}

        <header class="top">

            <b>
                Boat Ticketing
            </b>


            <span class="online">
                ● LOCAL ONLINE
            </span>


            <span id="cloud">
                ● CLOUD SYNC
            </span>


            <span class="spacer"></span>


            <span>
                🔔 0
            </span>


            @auth

                <span class="user">
                    {{ auth()->user()->name }}
                </span>


                <form
                    class="logout-form"
                    method="POST"
                    action="{{ route('logout') }}"
                >

                    @csrf

                    <button
                        type="submit"
                        class="logout-btn"
                    >
                        Logout
                    </button>

                </form>

            @endauth

        </header>


        {{-- =====================================================
             PAGE CONTENT
        ====================================================== --}}

        <div class="wrap">

            @yield('content')

        </div>

    </main>


    {{-- =========================================================
         CLOUD STATUS
    ========================================================== --}}

    <script>

        async function status()
        {
            const element =
                document.getElementById('cloud');

            if (!element) {
                return;
            }

            try {

                await fetch(
                    '{{ url('/offline-status') }}',
                    {
                        cache: 'no-store'
                    }
                );

                element.textContent =
                    '● CLOUD SYNC';

                element.style.color =
                    '#19a15f';

            } catch (error) {

                element.textContent =
                    '⚠ CLOUD OFFLINE';

                element.style.color =
                    '#d56b10';

            }
        }


        status();

        setInterval(
            status,
            15000
        );

    </script>


    @stack('scripts')

</body>

</html>