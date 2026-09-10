@extends('layouts.app')

@section('content')

<div class="heading">

    <div style="display:flex; align-items:flex-start; justify-content:space-between; width:100%;">

        <div>
            <h1>Master Boat</h1>

            <p>
                Pengelolaan data boat yang digunakan
                dalam operasional ticketing.
            </p>
        </div>

        <button
            type="button"
            class="btn btn-primary"
            data-modal-open="boatModal"
            data-mode="create"
        >
            + Tambah Boat
        </button>

    </div>

</div>


{{-- =========================================================
   MASTER BOAT
========================================================= --}}

<x-datatable
    id="boatTable"
    page-size="10"
>

    <x-slot name="header">

        <th>
            Kode
            <span class="table-sort"></span>
        </th>

        <th>
            Nama Boat
            <span class="table-sort"></span>
        </th>

        <th>
            Registrasi
            <span class="table-sort"></span>
        </th>

        <th>
            Kapasitas
            <span class="table-sort"></span>
        </th>

        <th>
            Status
            <span class="table-sort"></span>
        </th>

        <th data-sort="false" width="100">
            Aksi
        </th>

    </x-slot>


    <x-slot name="body">

        @forelse ($boats as $boat)

            <tr data-id="{{ $boat->id }}">

                <td>
                    {{ $boat->code }}
                </td>

                <td>
                    {{ $boat->name }}
                </td>

                <td>
                    {{ $boat->registration_number ?: '-' }}
                </td>

                <td>
                    {{ $boat->capacity }}
                </td>

                <td>

                    @if ($boat->status === 'active')

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

        data-modal-open="boatModal"
        data-mode="edit"

        data-id="{{ $boat->id }}"
        data-code="{{ $boat->code }}"
        data-name="{{ $boat->name }}"
        data-registration_number="{{ $boat->registration_number }}"
        data-capacity="{{ $boat->capacity }}"
        data-status="{{ $boat->status }}"

        title="Edit Boat"
        aria-label="Edit Boat"
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
    @if ($boat->status === 'active')

        <button
            type="button"
            class="action-icon action-danger"

            data-confirm-open

            data-id="{{ $boat->id }}"

            data-confirm-title="Nonaktifkan Boat"

            data-confirm-message="Boat {{ $boat->code }} akan dinonaktifkan dan tidak dapat digunakan untuk operasional baru."

            data-confirm-action="{{ route(
                'master.boats.deactivate',
                $boat->id
            ) }}"

            title="Nonaktifkan Boat"
            aria-label="Nonaktifkan Boat"
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

            data-id="{{ $boat->id }}"

            data-confirm-title="Aktifkan Boat"

            data-confirm-message="Boat {{ $boat->code }} akan diaktifkan kembali."

            data-confirm-action="{{ route(
                'master.boats.activate',
                $boat->id
            ) }}"

            title="Aktifkan Boat"
            aria-label="Aktifkan Boat"
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
                    colspan="6"
                    class="table-empty"
                >
                    Belum ada data boat.
                </td>

            </tr>

        @endforelse

    </x-slot>

</x-datatable>


{{-- =========================================================
   MODAL TAMBAH / EDIT
========================================================= --}}

<x-modal
    id="boatModal"

    title="Boat"

    create-title="Tambah Boat"

    edit-title="Edit Boat"
>

    <form
        method="POST"

        data-store="{{ route('master.boats.store') }}"

        data-update="{{ route(
            'master.boats.update',
            ':id'
        ) }}"
    >

        @csrf

        <input
            type="hidden"
            name="_method"
            value="POST"
        >


        <div class="form-group">

            <label>Kode Boat</label>

            <input
                type="text"
                name="code"
                class="form-control"
                maxlength="50"
                required
            >

        </div>


        <div class="form-group">

            <label>Nama Boat</label>

            <input
                type="text"
                name="name"
                class="form-control"
                maxlength="150"
                required
            >

        </div>


        <div class="form-group">

            <label>Nomor Registrasi</label>

            <input
                type="text"
                name="registration_number"
                class="form-control"
                maxlength="100"
            >

        </div>


        <div class="form-group">

            <label>Kapasitas</label>

            <input
                type="number"
                name="capacity"
                class="form-control"
                min="1"
                required
            >

        </div>


        <div class="form-group">

            <label>Status</label>

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

@endsection