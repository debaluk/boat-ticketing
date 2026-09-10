@extends('layouts.app')

@section('content')

<div class="heading">

    <div style="display:flex; align-items:flex-start; justify-content:space-between; width:100%;">

        <div>
            <h1>Master Agent</h1>

            <p>
                Pengelolaan data agent yang digunakan
                dalam operasional ticketing.
            </p>
        </div>

        <button
            type="button"
            class="btn btn-primary"
            data-modal-open="agentModal"
            data-mode="create"
        >
            + Tambah Agent
        </button>

    </div>

</div>


{{-- =========================================================
   MASTER AGENT
========================================================= --}}

<x-datatable
    id="agentTable"
    page-size="10"
>

    <x-slot name="header">

        <th>
            Kode
            <span class="table-sort"></span>
        </th>

        <th>
            Nama Agent
            <span class="table-sort"></span>
        </th>

        <th>
            No. HP
            <span class="table-sort"></span>
        </th>

        <th>
            Email
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

        @forelse ($agents as $agent)

            <tr data-id="{{ $agent->id }}">

                <td>
                    {{ $agent->code }}
                </td>

                <td>
                    {{ $agent->name }}
                </td>

                <td>
                    {{ $agent->phone ?: '-' }}
                </td>

                <td>
                    {{ $agent->email ?: '-' }}
                </td>

                <td>

                    @if ($agent->status === 'active')

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

                    {{-- =================================================
                       EDIT
                    ================================================== --}}

                    <button
                        type="button"
                        class="action-icon action-edit"

                        data-modal-open="agentModal"
                        data-mode="edit"

                        data-id="{{ $agent->id }}"
                        data-code="{{ $agent->code }}"
                        data-name="{{ $agent->name }}"
                        data-phone="{{ $agent->phone }}"
                        data-email="{{ $agent->email }}"
                        data-address="{{ $agent->address }}"
                        data-status="{{ $agent->status }}"

                        title="Edit Agent"
                        aria-label="Edit Agent"
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


                    {{-- =================================================
                       STATUS ACTION
                    ================================================== --}}

                    @if ($agent->status === 'active')

                        <button
                            type="button"
                            class="action-icon action-danger"

                            data-confirm-open

                            data-id="{{ $agent->id }}"

                            data-confirm-title="Nonaktifkan Agent"

                            data-confirm-message="Agent {{ $agent->code }} akan dinonaktifkan dan tidak dapat digunakan untuk operasional baru."

                            data-confirm-action="{{ route(
                                'master.agents.deactivate',
                                $agent->id
                            ) }}"

                            data-confirm-method="PATCH"

                            title="Nonaktifkan Agent"
                            aria-label="Nonaktifkan Agent"
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

                            data-id="{{ $agent->id }}"

                            data-confirm-title="Aktifkan Agent"

                            data-confirm-message="Agent {{ $agent->code }} akan diaktifkan kembali."

                            data-confirm-action="{{ route(
                                'master.agents.activate',
                                $agent->id
                            ) }}"

                            data-confirm-method="PATCH"

                            title="Aktifkan Agent"
                            aria-label="Aktifkan Agent"
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
                    Belum ada data agent.
                </td>

            </tr>

        @endforelse

    </x-slot>

</x-datatable>



{{-- =========================================================
   MODAL TAMBAH / EDIT
========================================================= --}}

<x-modal
    id="agentModal"

    title="Agent"

    create-title="Tambah Agent"

    edit-title="Edit Agent"
>

    <form
        method="POST"

        data-store="{{ route('master.agents.store') }}"

        data-update="{{ route(
            'master.agents.update',
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

            <label>Kode Agent</label>

            <input
                type="text"
                name="code"
                class="form-control"
                maxlength="50"
                required
            >

        </div>


        <div class="form-group">

            <label>Nama Agent</label>

            <input
                type="text"
                name="name"
                class="form-control"
                maxlength="150"
                required
            >

        </div>


        <div class="form-group">

            <label>No. HP</label>

            <input
                type="text"
                name="phone"
                class="form-control"
                maxlength="30"
            >

        </div>


        <div class="form-group">

            <label>Email</label>

            <input
                type="email"
                name="email"
                class="form-control"
                maxlength="150"
            >

        </div>


        <div class="form-group">

            <label>Alamat</label>

            <textarea
                name="address"
                class="form-control"
                rows="3"
            ></textarea>

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

        {{-- PENTING: STATUS ROUTE MENGGUNAKAN PATCH --}}

        <input
            type="hidden"
            name="_method"
            value="PATCH"
            data-confirm-method
        >


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