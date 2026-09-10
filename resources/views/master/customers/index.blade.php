@extends('layouts.app')

@section('content')

<div class="heading">


<div style="display:flex; align-items:flex-start; justify-content:space-between; width:100%;">

    <div>
        <h1>Master Customer</h1>

        <p>
            Pengelolaan data customer yang digunakan
            dalam operasional ticketing.
        </p>
    </div>

    <button
        type="button"
        class="btn btn-primary"
        data-modal-open="customerModal"
        data-mode="create"
    >
        + Tambah Customer
    </button>

</div>


</div>

{{-- =========================================================
MASTER CUSTOMER
========================================================= --}}

<x-datatable
id="customerTable"
page-size="10"

>


<x-slot name="header">

    <th>
        Kode
        <span class="table-sort"></span>
    </th>

    <th>
        Nama Customer
        <span class="table-sort"></span>
    </th>

    <th>
        Phone
        <span class="table-sort"></span>
    </th>

    <th>
        Email
        <span class="table-sort"></span>
    </th>

    <th>
        Negara
        <span class="table-sort"></span>
    </th>

    <th>
        Status
        <span class="table-sort"></span>
    </th>

    <th
        data-sort="false"
        width="100"
    >
        Aksi
    </th>

</x-slot>


<x-slot name="body">

    @forelse ($customers as $customer)

        <tr data-id="{{ $customer->id }}">

            <td>
                {{ $customer->code }}
            </td>

            <td>
                {{ $customer->name }}
            </td>

            <td>
                {{ $customer->phone ?: '-' }}
            </td>

            <td>
                {{ $customer->email ?: '-' }}
            </td>

            <td>
                {{ $customer->country_name ?: '-' }}
            </td>

            <td>

                @if ($customer->status === 'active')

                    <span class="badge badge-success">
                        ACTIVE
                    </span>

                @else

                    <span class="badge badge-muted">
                        INACTIVE
                    </span>

                @endif

            </td>


            <td class="table-action">

                {{-- EDIT --}}

                <button
                    type="button"
                    class="action-icon action-edit"

                    data-modal-open="customerModal"
                    data-mode="edit"

                    data-id="{{ $customer->id }}"
                    data-code="{{ $customer->code }}"
                    data-name="{{ $customer->name }}"
                    data-phone="{{ $customer->phone }}"
                    data-email="{{ $customer->email }}"
                    data-address="{{ $customer->address }}"
                    data-country_id="{{ $customer->country_id }}"
                    data-status="{{ $customer->status }}"

                    title="Edit Customer"
                    aria-label="Edit Customer"
                >

                    <svg
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >

                        <path d="M12 20h9"/>

                        <path
                            d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"
                        />

                    </svg>

                </button>


                {{-- STATUS ACTION --}}

                @if ($customer->status === 'active')

                    <button
                        type="button"
                        class="action-icon action-danger"

                        data-confirm-open

                        data-id="{{ $customer->id }}"

                        data-confirm-title="Nonaktifkan Customer"

                        data-confirm-message="Customer {{ $customer->code }} akan dinonaktifkan dan tidak dapat digunakan untuk transaksi baru."

                        data-confirm-action="{{ route(
                            'master.customers.deactivate',
                            $customer->id
                        ) }}"

                        title="Nonaktifkan Customer"
                        aria-label="Nonaktifkan Customer"
                    >

                        <svg
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >

                            <circle
                                cx="12"
                                cy="12"
                                r="9"
                            />

                            <line
                                x1="8"
                                y1="12"
                                x2="16"
                                y2="12"
                            />

                        </svg>

                    </button>

                @else

                    <button
                        type="button"
                        class="action-icon action-success"

                        data-confirm-open

                        data-id="{{ $customer->id }}"

                        data-confirm-title="Aktifkan Customer"

                        data-confirm-message="Customer {{ $customer->code }} akan diaktifkan kembali."

                        data-confirm-action="{{ route(
                            'master.customers.activate',
                            $customer->id
                        ) }}"

                        title="Aktifkan Customer"
                        aria-label="Aktifkan Customer"
                    >

                        <svg
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >

                            <polyline
                                points="20 6 9 17 4 12"
                            />

                        </svg>

                    </button>

                @endif

            </td>

        </tr>

    @empty

        <tr>

            <td
                colspan="7"
                class="table-empty"
            >
                Belum ada data customer.
            </td>

        </tr>

    @endforelse

</x-slot>


</x-datatable>

{{-- =========================================================
CUSTOMER MODAL
========================================================= --}}

<x-modal
id="customerModal"
title="Customer"
create-title="Tambah Customer"
edit-title="Edit Customer"

>


<form
    id="customerForm"
    method="POST"

    action="{{ route('master.customers.store') }}"

    data-store="{{ route('master.customers.store') }}"

    data-update="{{ route(
        'master.customers.update',
        ':id'
    ) }}"
>

    @csrf

    <input
        type="hidden"
        name="_method"
        id="customerMethod"
        value="POST"
    >


    {{-- KODE --}}

    <div class="form-group">

        <label>
            Kode Customer
        </label>

        <input
            type="text"
            name="code"
            class="form-control"
            maxlength="30"
            readonly
        >

    </div>


    {{-- NAMA --}}

    <div class="form-group">

        <label>
            Nama Customer
        </label>

        <input
            type="text"
            name="name"
            class="form-control"
            maxlength="100"
            required
        >

    </div>


    {{-- PHONE --}}

    <div class="form-group">

        <label>
            Phone
        </label>

        <input
            type="text"
            name="phone"
            class="form-control"
            maxlength="30"
        >

    </div>


    {{-- EMAIL --}}

    <div class="form-group">

        <label>
            Email
        </label>

        <input
            type="email"
            name="email"
            class="form-control"
            maxlength="150"
        >

    </div>


    {{-- ADDRESS --}}

    <div class="form-group">

        <label>
            Address
        </label>

        <textarea
            name="address"
            class="form-control"
            rows="3"
        ></textarea>

    </div>


    {{-- COUNTRY --}}

    <div class="form-group">

        <label>
            Country
        </label>

        <select
            name="country_id"
            class="form-control"
        >

            <option value="">
                -- Pilih Country --
            </option>

            @foreach ($countries as $country)

                <option value="{{ $country->id }}">
                    {{ $country->name }}
                </option>

            @endforeach

        </select>

    </div>


    {{-- STATUS --}}

    <div class="form-group">

        <label>
            Status
        </label>

        <select
            name="status"
            class="form-control"
        >

            <option value="active">
                Active
            </option>

            <option value="inactive">
                Inactive
            </option>

        </select>

    </div>


    {{-- FOOTER --}}

    <div class="modal-footer">

        <button
            type="button"
            class="btn btn-secondary"
            data-modal-close
        >
            Batal
        </button>

        <button
            type="submit"
            class="btn btn-primary"
        >
            Simpan
        </button>

    </div>

</form>


</x-modal>

{{-- =========================================================
CONFIRM MODAL
========================================================= --}}

<x-modal
id="confirmModal"
title="Konfirmasi"

>


<div class="confirm-content">

    <div
        class="confirm-title"
        data-confirm-title
    ></div>

    <div
        class="confirm-message"
        data-confirm-message
    ></div>

</div>


<form
    method="POST"
    data-confirm-form
>

    @csrf

    <div class="modal-footer">

        <button
            type="button"
            class="btn btn-secondary"
            data-modal-close
        >
            Batal
        </button>

        <button
            type="submit"
            class="btn btn-danger"
        >
            Ya, Lanjutkan
        </button>

    </div>

</form>


</x-modal>

{{-- =========================================================
CUSTOMER AJAX
========================================================= --}}

<script>

(function () {

    'use strict';


    /*
    |--------------------------------------------------------------------------
    | GET FORM
    |--------------------------------------------------------------------------
    */

    const form =
        document.getElementById(
            'customerForm'
        );


    if (!form) {

        console.error(
            'Customer AJAX: customerForm tidak ditemukan.'
        );

        return;
    }


    const methodInput =
        document.getElementById(
            'customerMethod'
        );


    /*
    |--------------------------------------------------------------------------
    | OPEN CREATE / EDIT
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'click',
        function (event) {

            const button =
                event.target.closest(
                    '[data-modal-open="customerModal"]'
                );


            if (!button) {
                return;
            }


            const mode =
                button.dataset.mode;


            /*
            |--------------------------------------------------------------------------
            | CREATE
            |--------------------------------------------------------------------------
            */

            if (mode === 'create') {

                form.reset();

                form.action =
                    form.dataset.store;

                methodInput.value =
                    'POST';


                const codeInput =
                    form.querySelector(
                        '[name="code"]'
                    );


                const statusInput =
                    form.querySelector(
                        '[name="status"]'
                    );


                if (codeInput) {
                    codeInput.value = '';
                }


                if (statusInput) {
                    statusInput.value =
                        'active';
                }


                return;
            }


            /*
            |--------------------------------------------------------------------------
            | EDIT
            |--------------------------------------------------------------------------
            */

            if (mode === 'edit') {

                const id =
                    button.dataset.id;


                form.action =
                    form.dataset.update.replace(
                        ':id',
                        id
                    );


                methodInput.value =
                    'PUT';


                form.querySelector(
                    '[name="code"]'
                ).value =
                    button.dataset.code || '';


                form.querySelector(
                    '[name="name"]'
                ).value =
                    button.dataset.name || '';


                form.querySelector(
                    '[name="phone"]'
                ).value =
                    button.dataset.phone || '';


                form.querySelector(
                    '[name="email"]'
                ).value =
                    button.dataset.email || '';


                form.querySelector(
                    '[name="address"]'
                ).value =
                    button.dataset.address || '';


                form.querySelector(
                    '[name="country_id"]'
                ).value =
                    button.dataset.country_id || '';


                form.querySelector(
                    '[name="status"]'
                ).value =
                    button.dataset.status ||
                    'active';

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | SUBMIT CUSTOMER
    |--------------------------------------------------------------------------
    */

    form.addEventListener(
        'submit',
        async function (event) {

            event.preventDefault();
            event.stopPropagation();


            const submitButton =
                form.querySelector(
                    'button[type="submit"]'
                );


            if (!submitButton) {

                console.error(
                    'Customer AJAX: tombol submit tidak ditemukan.'
                );

                return;
            }


            const originalText =
                submitButton.innerHTML;


            submitButton.disabled =
                true;

            submitButton.innerHTML =
                'Menyimpan...';


            try {

                /*
                |--------------------------------------------------------------------------
                | CSRF
                |--------------------------------------------------------------------------
                */

                const csrf =
                    form.querySelector(
                        'input[name="_token"]'
                    )?.value;


                /*
                |--------------------------------------------------------------------------
                | FORM DATA
                |--------------------------------------------------------------------------
                */

                const formData =
                    new FormData(form);


                /*
                |--------------------------------------------------------------------------
                | AJAX
                |--------------------------------------------------------------------------
                */

                const response =
                    await fetch(
                        form.action,
                        {
                            method: 'POST',

                            headers: {

                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest',

                                'X-CSRF-TOKEN':
                                    csrf

                            },

                            body:
                                formData
                        }
                    );


                /*
                |--------------------------------------------------------------------------
                | CONTENT TYPE
                |--------------------------------------------------------------------------
                */

                const contentType =
                    response.headers.get(
                        'content-type'
                    ) || '';


                /*
                |--------------------------------------------------------------------------
                | NON JSON
                |--------------------------------------------------------------------------
                */

                if (
                    !contentType.includes(
                        'application/json'
                    )
                ) {

                    const text =
                        await response.text();


                    console.error(
                        'Customer AJAX response bukan JSON:',
                        text
                    );


                    alert(
                        'Response server bukan JSON. ' +
                        'Periksa Console browser.'
                    );


                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | JSON
                |--------------------------------------------------------------------------
                */

                const data =
                    await response.json();


                /*
                |--------------------------------------------------------------------------
                | VALIDATION / SERVER ERROR
                |--------------------------------------------------------------------------
                */

                if (
                    !response.ok ||
                    !data.success
                ) {

                    let message =
                        data.message ||
                        'Gagal menyimpan customer.';


                    if (data.errors) {

                        message =
                            Object.values(
                                data.errors
                            )
                            .flat()
                            .join('\n');

                    }


                    alert(message);

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | TABLE
                |--------------------------------------------------------------------------
                */

                const table =
                    document.getElementById(
                        'customerTable'
                    );


                const tbody =
                    table?.querySelector(
                        'tbody'
                    );


                if (
                    tbody &&
                    data.row_html
                ) {

                    const oldRow =
                        tbody.querySelector(
                            `tr[data-id="${data.id}"]`
                        );


                    if (oldRow) {

                        oldRow.outerHTML =
                            data.row_html;

                    } else {

                        const emptyRow =
                            tbody.querySelector(
                                '.table-empty'
                            );


                        if (emptyRow) {

                            emptyRow
                                .closest('tr')
                                ?.remove();

                        }


                        tbody.insertAdjacentHTML(
                            'afterbegin',
                            data.row_html
                        );

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | CLOSE MODAL
                |--------------------------------------------------------------------------
                */

                const closeButton =
                    document.querySelector(
                        '#customerModal [data-modal-close]'
                    );


                if (closeButton) {

                    closeButton.click();

                }


                /*
                |--------------------------------------------------------------------------
                | RESET FORM
                |--------------------------------------------------------------------------
                */

                form.reset();

                form.action =
                    form.dataset.store;

                methodInput.value =
                    'POST';


                form.querySelector(
                    '[name="code"]'
                ).value = '';


                form.querySelector(
                    '[name="status"]'
                ).value =
                    'active';


            } catch (error) {

                console.error(
                    'Customer AJAX error:',
                    error
                );


                alert(
                    'Terjadi kesalahan saat menyimpan customer.'
                );


            } finally {

                submitButton.disabled =
                    false;

                submitButton.innerHTML =
                    originalText;

            }

        }
    );

})();

</script>

@endsection
