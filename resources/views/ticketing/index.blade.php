@extends('layouts.app')

@section('title', 'Ticketing / Kasir')

@section('content')

<div class="wrap">

    {{-- =====================================================
         BREADCRUMB
    ====================================================== --}}
    <div class="crumb">
        Operasional / Ticketing
    </div>


    {{-- =====================================================
         PAGE TITLE
    ====================================================== --}}
    <div class="page-title">

        <div>
            <h1>Ticketing / Kasir</h1>

            <p>
                Penjualan tiket dan alokasi penumpang
                ke antrean boat.
            </p>
        </div>

        <button type="button" class="btn">
            ? Refresh
        </button>

    </div>


    {{-- =====================================================
         MAIN GRID
    ====================================================== --}}
    <div class="layout">


        {{-- =================================================
             LEFT : TRANSACTION
        ================================================== --}}
        <section class="card">

            <div class="card-head">

                <span>
                    TRANSAKSI TIKET
                </span>

                <span class="draft-label">
                    Draft #TK-20260812-0027
                </span>

            </div>


            <div class="card-body">


                {{-- =================================================
                     STEPS
                ================================================== --}}
                <div class="steps">

                    <div class="step active">
                        <span class="circle">1</span>
                        Data Tiket
                    </div>

                    <div class="line"></div>

                    <div class="step">
                        <span class="circle">2</span>
                        Pembayaran
                    </div>

                    <div class="line"></div>

                    <div class="step">
                        <span class="circle">3</span>
                        Cetak Tiket
                    </div>

                </div>


                {{-- =================================================
                     1. DATA PENUMPANG
                ================================================== --}}
                <div class="section">

                    <div class="section-title">
                        1. DATA PENUMPANG
                    </div>


                    <form method="POST" action="{{ route('ticketing.store') }}" id="ticketForm">
                        @csrf

                        <div class="form-grid">


                        {{-- JUMLAH PENUMPANG --}}
                        <div class="field">

                            <label>
                                Jumlah Penumpang *
                            </label>

                            <div class="counter">

                                <button
                                    type="button"
                                    onclick="qty(-1)"
                                >
                                    -
                                </button>

                                <input
                                    id="qty"
                                    name="passenger_count"
                                    type="text"
                                    value="2"
                                    readonly
                                >

                                <button
                                    type="button"
                                    onclick="qty(1)"
                                >
                                    +
                                </button>

                            </div>

                            <small class="field-help">
                                Maksimal 4 orang / boat
                            </small>

                        </div>


                        {{-- CUSTOMER --}}
                        <div class="field">

                            <label>
                                Nama Pemesan / PIC
                            </label>

                            <input
                                type="text"
                                name="customer_name"
                                placeholder="Nama pelanggan"
                            >

                        </div>


                        {{-- PHONE --}}
                        <div class="field">

                            <label>
                                Nomor HP
                            </label>

                            <input
                                type="text"
                                name="customer_phone"
                                placeholder="08xxxxxxxxxx"
                            >

                        </div>


                        {{-- AGENT --}}
                        <div class="field">

                            <label>
                                Guide / Agen
                            </label>

                            <select name="guide_id">

                                <option value="">
                                    -- Tanpa Guide / Agen --
                                </option>

                                <option value="1">
                                    Andi Tour
                                </option>

                                <option value="2">
                                    Sari Travel
                                </option>

                            </select>

                        </div>

                    </div>

                        </div>

                </div>


                {{-- =================================================
                     2. MODE KEBERANGKATAN
                ================================================== --}}
                <div class="section">

                    <div class="section-title">
                        2. MODE KEBERANGKATAN
                    </div>


                    <div class="mode">


                        {{-- POOLING --}}
                        <div
                            class="mode-box selected"
                            onclick="mode(this, false)"
                        >

                            <strong>
                                POOLING / MENUNGGU
                            </strong>

                            <small>
                                Penumpang masuk ke antrean.
                                Sistem otomatis menggabungkan
                                penumpang berdasarkan FIFO
                                hingga minimal 2 orang.
                            </small>

                        </div>


                        {{-- DIRECT --}}
                        <div
                            class="mode-box"
                            onclick="mode(this, true)"
                        >

                            <strong>
                                DIRECT / BUY EMPTY SEAT
                            </strong>

                            <small>
                                Untuk 1 penumpang yang ingin
                                langsung berangkat. Sistem
                                mengenakan minimum 2 seat.
                            </small>

                        </div>

                    </div>

                </div>


                {{-- =================================================
                     3. TARIF & PEMBAYARAN
                ================================================== --}}
                <div class="section">

                    <div class="section-title">
                        3. TARIF & PEMBAYARAN
                    </div>


                    {{-- SUMMARY --}}
                    <div class="summary">

                        <div class="sumrow">

                            <span>
                                Tarif per orang
                            </span>

                            <b>
                                Rp 50.000
                            </b>

                        </div>


                        <div class="sumrow">

                            <span>
                                Jumlah penumpang
                            </span>

                            <b id="people">
                                2 orang
                            </b>

                        </div>


                        <div class="sumrow">

                            <span>
                                Seat yang dikenakan
                            </span>

                            <b id="seat">
                                2 seat
                            </b>

                        </div>


                        <div class="sumrow">

                            <span>
                                Komisi Guide / Agen
                            </span>

                            <b>
                                Rp 0
                            </b>

                        </div>


                        <div class="sumrow total">

                            <span>
                                TOTAL BAYAR
                            </span>

                            <span id="total">
                                Rp 100.000
                            </span>

                        </div>

                    </div>


                    {{-- PAYMENT METHOD --}}
                    <div class="pay-method">

                        <div class="pay selected">
                            Tunai
                        </div>

                        <div class="pay">
                            EDC
                        </div>

                        <div class="pay">
                            QRIS
                        </div>

                    </div>

                </div>


                {{-- =================================================
                     ACTIONS
                ================================================== --}}
                <div class="actions">

                    <button
                        type="button"
                        class="btn danger"
                    >
                        BATAL
                    </button>


                    <button
                        type="submit"
                        class="btn"
                    >
                        SIMPAN DRAFT
                    </button>


                    <button
                        type="button"
                        class="btn primary"
                        onclick="pay()"
                    >
                        PROSES PEMBAYARAN
                    </button>

                </div>

            </form>

            </div>

        </section>



        {{-- =================================================
             RIGHT : OPERATIONAL INFO
        ================================================== --}}
        <aside class="right-stack">


            {{-- =================================================
                 ALOKASI BOAT
            ================================================== --}}
            <div class="card">

                <div class="card-head">

                    <span>
                        ALOKASI BOAT
                    </span>

                    <span class="badge partial">
                        AUTO FIFO
                    </span>

                </div>


                <div class="card-body">

                    <div class="boat">


                        {{-- BOAT HEADER --}}
                        <div class="boat-top">

                            <div>

                                <small class="muted">
                                    Boat berikutnya
                                </small>

                                <div class="boat-name">
                                    BOAT 03
                                </div>

                            </div>

                            <span class="badge partial">
                                PARTIAL
                            </span>

                        </div>


                        {{-- SEAT --}}
                        <div class="capacity">

                            <span class="seat full">
                                1
                            </span>

                            <span class="seat full">
                                2
                            </span>

                            <span class="seat">
                                3
                            </span>

                            <span class="seat">
                                4
                            </span>

                        </div>


                        {{-- BOAT INFO --}}
                        <div class="boat-info">

                            <span>
                                Kapasitas
                            </span>

                            <b>
                                2 / 4 orang
                            </b>

                        </div>


                        <div class="boat-info">

                            <span>
                                Status
                            </span>

                            <b>
                                Menunggu pooling
                            </b>

                        </div>


                        <div class="boat-info">

                            <span>
                                Estimasi
                            </span>

                            <b>
                                ± 5 menit
                            </b>

                        </div>


                        <div class="progress">
                            <span></span>
                        </div>

                    </div>


                    <div class="notice mt-10">

                        Setelah pembayaran berhasil,
                        tiket otomatis masuk ke
                        <b>Boat 03</b> sesuai antrean FIFO.
                        Kasir tidak memilih boat secara manual.

                    </div>

                </div>

            </div>



            {{-- =================================================
                 MANIFEST
            ================================================== --}}
            <div class="card">

                <div class="card-head">
                    MANIFEST SEMENTARA — BOAT 03
                </div>


                <div class="card-body">


                    <div class="passenger">

                        <span>
                            01 &nbsp; <b>Andi</b>
                        </span>

                        <span class="badge ready">
                            Dewasa
                        </span>

                    </div>


                    <div class="passenger">

                        <span>
                            02 &nbsp; <b>Sari</b>
                        </span>

                        <span class="badge ready">
                            Dewasa
                        </span>

                    </div>


                    <div class="passenger">

                        <span>
                            03 &nbsp;

                            <span class="muted">
                                Kosong
                            </span>
                        </span>

                        <span>
                            —
                        </span>

                    </div>


                    <div class="passenger">

                        <span>
                            04 &nbsp;

                            <span class="muted">
                                Kosong
                            </span>
                        </span>

                        <span>
                            —
                        </span>

                    </div>

                </div>

            </div>



            {{-- =================================================
                 TRANSACTION CONTROL
            ================================================== --}}
            <div class="card">

                <div class="card-head">
                    KONTROL TRANSAKSI
                </div>


                <div class="card-body">


                    <div class="sumrow">

                        <span>
                            Kasir
                        </span>

                        <b>
                            Budi Wijaya
                        </b>

                    </div>


                    <div class="sumrow">

                        <span>
                            Loket
                        </span>

                        <b>
                            Dermaga Utama
                        </b>

                    </div>


                    <div class="sumrow">

                        <span>
                            Waktu
                        </span>

                        <b id="clock">
                            --:--:--
                        </b>

                    </div>


                    <div class="notice">

                        Printer thermal
                        <b class="text-green">
                            TERHUBUNG
                        </b>

                        <br>

                        Setelah pembayaran,
                        sistem mencetak tiket
                        dan nomor antrean.

                    </div>

                </div>

            </div>

        </aside>

    </div>



    {{-- =====================================================
         FOOTER STATUS
    ====================================================== --}}
    <div class="footer">

        <span class="ok">
            Sistem Online
        </span>

        <span>
            Printer:
            <b>THERMAL-01</b>
        </span>

        <span>
            Antrean aktif:
            <b>5 boat</b>
        </span>

        <span>
            Boat tersedia:
            <b>10</b>
        </span>

        <span class="shortcut">
            F1 Ticketing ·
            F2 Antrean ·
            F3 Dispatch ·
            F4 Cetak Ulang
        </span>

    </div>

</div>

@endsection


@push('scripts')

<script>

let direct = false;


/*
|--------------------------------------------------------------------------
| JUMLAH PENUMPANG
|--------------------------------------------------------------------------
*/

function qty(delta)
{
    const input = document.getElementById('qty');

    let value = Math.max(
        1,
        Math.min(
            4,
            Number(input.value) + delta
        )
    );

    input.value = value;

    update();
}


/*
|--------------------------------------------------------------------------
| MODE KEBERANGKATAN
|--------------------------------------------------------------------------
*/

function mode(element, isDirect)
{
    document
        .querySelectorAll('.mode-box')
        .forEach(function (item) {

            item.classList.remove('selected');

        });

    element.classList.add('selected');

    direct = isDirect;

    update();
}


/*
|--------------------------------------------------------------------------
| UPDATE TOTAL
|--------------------------------------------------------------------------
*/

function update()
{
    const people =
        Number(
            document.getElementById('qty').value
        );

    const charged =
        direct && people === 1
            ? 2
            : people;


    document.getElementById('people')
        .textContent =
            people + ' orang';


    document.getElementById('seat')
        .textContent =
            charged + ' seat';


    document.getElementById('total')
        .textContent =
            'Rp ' +
            (charged * 50000)
                .toLocaleString('id-ID');
}


/*
|--------------------------------------------------------------------------
| PAYMENT
|--------------------------------------------------------------------------
*/

function pay()
{
    alert(
        'Prototype: lanjut ke layar Pembayaran.'
    );
}


/*
|--------------------------------------------------------------------------
| CLOCK
|--------------------------------------------------------------------------
*/

function tick()
{
    const clock =
        document.getElementById('clock');

    if (!clock) {
        return;
    }

    clock.textContent =
        new Date()
            .toLocaleTimeString('id-ID');
}


tick();

setInterval(
    tick,
    1000
);

</script>

@endpush