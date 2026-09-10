@props([
    'id',
    'title' => 'Form',
    'createTitle' => 'Tambah Data',
    'editTitle' => 'Edit Data',
])

<div
    id="{{ $id }}"
    class="modal"
    data-modal
    aria-hidden="true"
>

    <div
        class="modal-box"
        role="dialog"
        aria-modal="true"
        aria-labelledby="{{ $id }}Title"
    >

        {{-- HEADER --}}
        <div class="modal-header">

            <h2
                id="{{ $id }}Title"
                class="modal-title"
                data-modal-title

                data-create="{{ $createTitle }}"
                data-edit="{{ $editTitle }}"
            >
                {{ $title }}
            </h2>

            <button
                type="button"
                class="modal-close"
                data-modal-close
                aria-label="Tutup"
            >
                ×
            </button>

        </div>


        {{-- BODY --}}
        <div class="modal-body">

            {{ $slot }}

        </div>

    </div>

</div>