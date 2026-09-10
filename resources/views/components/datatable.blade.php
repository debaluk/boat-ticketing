@props([
    'id',
    'search' => true,
    'pagination' => true,
    'pageSize' => 10,
])

<div
    id="{{ $id }}"
    class="card data-table-component"

    data-datatable
    data-page-size="{{ $pageSize }}"
>

    {{-- TOOLBAR --}}

    @if ($search)

        <div class="table-toolbar">

            <input
                type="search"
                class="table-search"
                placeholder="Cari..."
                data-table-search
            >

            <select
                class="table-page-size"
                data-table-page-size
            >

                <option value="10">
                    10
                </option>

                <option value="25">
                    25
                </option>

                <option value="50">
                    50
                </option>

            </select>

        </div>

    @endif


    {{-- TABLE --}}

    <div class="table-wrapper">

        <table class="data-table">

            <thead>

                <tr>

                    {{ $header }}

                </tr>

            </thead>

            <tbody data-table-body>

                {{ $body }}

            </tbody>

        </table>

    </div>


    {{-- FOOTER --}}

    @if ($pagination)

        <div class="table-footer">

            <div
                class="table-info"
                data-table-info
            >
                -
            </div>

            <div
                class="table-pagination"
                data-table-pagination
            >
            </div>

        </div>

    @endif

</div>