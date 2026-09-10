import 'bootstrap';
/*
|--------------------------------------------------------------------------
| BOAT TICKETING - APP UI ENGINE
|--------------------------------------------------------------------------
| 1. Modal Engine
| 2. Toast Notification
| 3. AJAX Form
| 4. Confirm Modal
| 5. DataTable
|    - Search
|    - Sort
|    - Pagination
|    - Page Size
|    - Refresh Row/Data
|--------------------------------------------------------------------------
*/

(() => {

    'use strict';


    /*
    |--------------------------------------------------------------------------
    | TOAST ENGINE
    |--------------------------------------------------------------------------
    */

    const Toast = {

        container() {

            let container =
                document.getElementById('toastContainer');

            if (!container) {

                container =
                    document.createElement('div');

                container.id =
                    'toastContainer';

                container.className =
                    'toast-container';

                document.body.appendChild(
                    container
                );
            }

            return container;
        },


        show(type, title, message) {

            const container =
                this.container();

            const toast =
                document.createElement('div');

            toast.className =
                `toast ${type}`;


            const titleElement =
                document.createElement('div');

            titleElement.className =
                'toast-title';

            titleElement.textContent =
                title;


            const messageElement =
                document.createElement('div');

            messageElement.className =
                'toast-message';

            messageElement.textContent =
                message || '';


            toast.appendChild(
                titleElement
            );

            toast.appendChild(
                messageElement
            );


            container.appendChild(
                toast
            );


            setTimeout(() => {

                toast.classList.add(
                    'is-hiding'
                );

                setTimeout(() => {

                    toast.remove();

                }, 200);

            }, 3500);
        },


        success(message) {

            this.show(
                'success',
                '✓ Berhasil',
                message
            );
        },


        error(message) {

            this.show(
                'error',
                '✕ Gagal',
                message
            );
        }

    };


    window.Toast = Toast;



    /*
    |--------------------------------------------------------------------------
    | MODAL ENGINE
    |--------------------------------------------------------------------------
    */

    const ModalEngine = {


        open(modal, trigger) {

            const form =
                modal.querySelector('form');

            const mode =
                trigger?.dataset?.mode ||
                'create';


            if (form) {

                this.prepareForm(
                    form,
                    trigger,
                    mode
                );
            }


            this.setTitle(
                modal,
                mode
            );


            modal.classList.add(
                'is-open'
            );

            modal.setAttribute(
                'aria-hidden',
                'false'
            );
        },


        close(modal) {

            if (!modal) {
                return;
            }


            modal.classList.remove(
                'is-open'
            );

            modal.setAttribute(
                'aria-hidden',
                'true'
            );


            const form =
                modal.querySelector(
                    'form[data-store]'
                );


            if (form) {

                form.reset();

                this.setMethod(
                    form,
                    'POST'
                );


                if (form.dataset.store) {

                    form.action =
                        form.dataset.store;
                }
            }
        },


        prepareForm(
            form,
            trigger,
            mode
        ) {

            /*
            |--------------------------------------------------------------------------
            | CREATE
            |--------------------------------------------------------------------------
            */

            if (mode === 'create') {

                form.reset();

                form.method =
                    'POST';


                if (form.dataset.store) {

                    form.action =
                        form.dataset.store;
                }


                this.setMethod(
                    form,
                    'POST'
                );


                return;
            }


            /*
            |--------------------------------------------------------------------------
            | EDIT
            |--------------------------------------------------------------------------
            */

            if (mode === 'edit') {

                const id =
                    trigger.dataset.id;


                const updateUrl =
                    form.dataset.update;


                if (
                    updateUrl &&
                    id
                ) {

                    form.action =
                        updateUrl.replace(
                            ':id',
                            id
                        );
                }


                form.method =
                    'POST';


                this.setMethod(
                    form,
                    'PUT'
                );


                this.fillForm(
                    form,
                    trigger
                );
            }
        },


        setMethod(
            form,
            method
        ) {

            let input =
                form.querySelector(
                    '[name="_method"]'
                );


            if (!input) {

                input =
                    document.createElement(
                        'input'
                    );

                input.type =
                    'hidden';

                input.name =
                    '_method';

                form.appendChild(
                    input
                );
            }


            input.value =
                method;
        },


        fillForm(
            form,
            trigger
        ) {

            Object.entries(
                trigger.dataset || {}
            ).forEach(
                ([key, value]) => {

                    const field =
                        form.querySelector(
                            `[name="${CSS.escape(key)}"]`
                        );


                    if (!field) {
                        return;
                    }


                    field.value =
                        value;
                }
            );
        },


        setTitle(
            modal,
            mode
        ) {

            const title =
                modal.querySelector(
                    '[data-modal-title]'
                );


            if (!title) {
                return;
            }


            if (
                mode === 'edit'
            ) {

                title.textContent =
                    title.dataset.edit ||
                    'Edit Data';

            } else {

                title.textContent =
                    title.dataset.create ||
                    'Tambah Data';
            }
        }

    };


    window.ModalEngine =
        ModalEngine;



    /*
    |--------------------------------------------------------------------------
    | DATA TABLE ENGINE
    |--------------------------------------------------------------------------
    */

    class DataTableEngine {


        constructor(element) {

            this.element =
                element;


            this.table =
                element.querySelector(
                    '.data-table'
                );


            this.tbody =
                element.querySelector(
                    '[data-table-body]'
                );


            this.searchInput =
                element.querySelector(
                    '[data-table-search]'
                );


            this.pageSizeSelect =
                element.querySelector(
                    '[data-table-page-size]'
                );


            this.info =
                element.querySelector(
                    '[data-table-info]'
                );


            this.pagination =
                element.querySelector(
                    '[data-table-pagination]'
                );


            if (
                !this.table ||
                !this.tbody
            ) {

                return;
            }


            this.rows =
                Array.from(
                    this.tbody.querySelectorAll(
                        'tr'
                    )
                );


            this.currentPage =
                1;


            this.sortColumn =
                null;


            this.sortDirection =
                'asc';


            this.init();
        }



        /*
        |--------------------------------------------------------------------------
        | INIT
        |--------------------------------------------------------------------------
        */

        init() {

            this.bindSearch();

            this.bindPageSize();

            this.bindSort();

            this.render();
        }



        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        bindSearch() {

            if (
                !this.searchInput
            ) {

                return;
            }


            this.searchInput.addEventListener(
                'input',
                () => {

                    this.currentPage =
                        1;

                    this.render();
                }
            );
        }



        /*
        |--------------------------------------------------------------------------
        | PAGE SIZE
        |--------------------------------------------------------------------------
        */

        bindPageSize() {

            if (
                !this.pageSizeSelect
            ) {

                return;
            }


            this.pageSizeSelect.addEventListener(
                'change',
                () => {

                    this.currentPage =
                        1;

                    this.render();
                }
            );
        }



        /*
        |--------------------------------------------------------------------------
        | SORT
        |--------------------------------------------------------------------------
        */

        bindSort() {

            const headers =
                this.table.querySelectorAll(
                    'thead th'
                );


            headers.forEach(
                (header, index) => {

                    if (
                        header.dataset.sort ===
                        'false'
                    ) {

                        return;
                    }


                    header.style.cursor =
                        'pointer';


                    header.addEventListener(
                        'click',
                        () => {

                            this.sort(
                                index
                            );

                        }
                    );
                }
            );
        }



        sort(column) {

            if (
                this.sortColumn ===
                column
            ) {

                this.sortDirection =
                    this.sortDirection ===
                    'asc'
                        ? 'desc'
                        : 'asc';

            } else {

                this.sortColumn =
                    column;

                this.sortDirection =
                    'asc';
            }


            this.currentPage =
                1;


            this.render();
        }



        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        getFilteredRows() {

            const keyword =
                this.searchInput
                    ? this.searchInput.value
                        .toLowerCase()
                        .trim()
                    : '';


            if (!keyword) {

                return [
                    ...this.rows
                ];
            }


            return this.rows.filter(
                row => {

                    return row.textContent
                        .toLowerCase()
                        .includes(
                            keyword
                        );
                }
            );
        }



        /*
        |--------------------------------------------------------------------------
        | SORT ROWS
        |--------------------------------------------------------------------------
        */

        getSortedRows(
            rows
        ) {

            if (
                this.sortColumn ===
                null
            ) {

                return rows;
            }


            const column =
                this.sortColumn;


            const direction =
                this.sortDirection ===
                'asc'
                    ? 1
                    : -1;


            return rows.sort(
                (a, b) => {

                    const aValue =
                        a.cells[column]
                            ?.textContent
                            .trim() || '';


                    const bValue =
                        b.cells[column]
                            ?.textContent
                            .trim() || '';


                    return (
                        aValue.localeCompare(
                            bValue,
                            undefined,
                            {
                                numeric: true,
                                sensitivity:
                                    'base'
                            }
                        )
                        *
                        direction
                    );
                }
            );
        }



        /*
        |--------------------------------------------------------------------------
        | RENDER
        |--------------------------------------------------------------------------
        */

        render() {

            let rows =
                this.getFilteredRows();


            rows =
                this.getSortedRows(
                    rows
                );


            const total =
                rows.length;


            const pageSize =
                parseInt(
                    this.pageSizeSelect?.value ||
                    this.element.dataset.pageSize ||
                    10,
                    10
                );


            const totalPages =
                Math.max(
                    1,
                    Math.ceil(
                        total / pageSize
                    )
                );


            if (
                this.currentPage >
                totalPages
            ) {

                this.currentPage =
                    totalPages;
            }


            const start =
                (
                    this.currentPage -
                    1
                ) * pageSize;


            const visibleRows =
                rows.slice(
                    start,
                    start + pageSize
                );


            /*
            |--------------------------------------------------------------------------
            | CLEAR
            |--------------------------------------------------------------------------
            */

            this.tbody.innerHTML =
                '';


            /*
            |--------------------------------------------------------------------------
            | EMPTY
            |--------------------------------------------------------------------------
            */

            if (
                !visibleRows.length
            ) {

                const colspan =
                    this.table
                        .querySelectorAll(
                            'thead th'
                        )
                        .length;


                const tr =
                    document.createElement(
                        'tr'
                    );


                const td =
                    document.createElement(
                        'td'
                    );


                td.colSpan =
                    colspan;


                td.className =
                    'table-empty';


                td.textContent =
                    'Tidak ada data.';


                tr.appendChild(
                    td
                );


                this.tbody.appendChild(
                    tr
                );

            } else {

                /*
                |--------------------------------------------------------------------------
                | ROWS
                |--------------------------------------------------------------------------
                */

                visibleRows.forEach(
                    row => {

                        this.tbody.appendChild(
                            row
                        );

                    }
                );
            }


            this.renderInfo(
                start,
                visibleRows.length,
                total
            );


            this.renderPagination(
                totalPages
            );


            this.updateSortIndicator();
        }



        /*
        |--------------------------------------------------------------------------
        | INFO
        |--------------------------------------------------------------------------
        */

        renderInfo(
            start,
            count,
            total
        ) {

            if (!this.info) {
                return;
            }


            if (!total) {

                this.info.textContent =
                    'Tidak ada data';

                return;
            }


            this.info.textContent =
                `Menampilkan ${start + 1}–${start + count} dari ${total} data`;
        }



        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        renderPagination(
            totalPages
        ) {

            if (
                !this.pagination
            ) {

                return;
            }


            this.pagination.innerHTML =
                '';


            /*
            |--------------------------------------------------------------------------
            | BUTTON HELPER
            |--------------------------------------------------------------------------
            */

            const createButton =
                (
                    text,
                    disabled,
                    callback,
                    active = false
                ) => {

                    const button =
                        document.createElement(
                            'button'
                        );


                    button.type =
                        'button';


                    button.textContent =
                        text;


                    button.disabled =
                        disabled;


                    if (active) {

                        button.classList.add(
                            'active'
                        );
                    }


                    button.addEventListener(
                        'click',
                        callback
                    );


                    this.pagination.appendChild(
                        button
                    );
                };



            /*
            |--------------------------------------------------------------------------
            | PREVIOUS
            |--------------------------------------------------------------------------
            */

            createButton(
                '‹',
                this.currentPage === 1,
                () => {

                    if (
                        this.currentPage >
                        1
                    ) {

                        this.currentPage--;

                        this.render();
                    }
                }
            );



            /*
            |--------------------------------------------------------------------------
            | PAGE NUMBERS
            |--------------------------------------------------------------------------
            */

            for (
                let page = 1;
                page <= totalPages;
                page++
            ) {

                createButton(
                    page,
                    false,
                    () => {

                        this.currentPage =
                            page;

                        this.render();
                    },
                    page ===
                    this.currentPage
                );
            }



            /*
            |--------------------------------------------------------------------------
            | NEXT
            |--------------------------------------------------------------------------
            */

            createButton(
                '›',
                this.currentPage ===
                    totalPages,
                () => {

                    if (
                        this.currentPage <
                        totalPages
                    ) {

                        this.currentPage++;

                        this.render();
                    }
                }
            );
        }



        /*
        |--------------------------------------------------------------------------
        | SORT INDICATOR
        |--------------------------------------------------------------------------
        */

        updateSortIndicator() {

            const headers =
                this.table.querySelectorAll(
                    'thead th'
                );


            headers.forEach(
                header => {

                    const indicator =
                        header.querySelector(
                            '.table-sort'
                        );


                    if (indicator) {

                        indicator.textContent =
                            '↕';
                    }
                }
            );


            if (
                this.sortColumn ===
                null
            ) {

                return;
            }


            const header =
                headers[
                    this.sortColumn
                ];


            if (!header) {
                return;
            }


            const indicator =
                header.querySelector(
                    '.table-sort'
                );


            if (!indicator) {
                return;
            }


            indicator.textContent =
                this.sortDirection ===
                'asc'
                    ? '↑'
                    : '↓';
        }



        /*
        |--------------------------------------------------------------------------
        | REPLACE ALL ROWS
        |--------------------------------------------------------------------------
        */

        replaceRows(
            rowsHtml
        ) {

            const temporary =
                document.createElement(
                    'tbody'
                );


            temporary.innerHTML =
                rowsHtml;


            this.rows =
                Array.from(
                    temporary.querySelectorAll(
                        'tr'
                    )
                );


            this.currentPage =
                1;


            this.render();
        }



        /*
        |--------------------------------------------------------------------------
        | ADD ROW
        |--------------------------------------------------------------------------
        */

        addRow(
            rowHtml
        ) {

            const temporary =
                document.createElement(
                    'tbody'
                );


            temporary.innerHTML =
                rowHtml;


            const row =
                temporary.querySelector(
                    'tr'
                );


            if (!row) {
                return;
            }


            this.rows.push(
                row
            );


            this.currentPage =
                1;


            this.render();
        }



        /*
        |--------------------------------------------------------------------------
        | UPDATE ROW
        |--------------------------------------------------------------------------
        */

        updateRow(
            id,
            rowHtml
        ) {

            const temporary =
                document.createElement(
                    'tbody'
                );


            temporary.innerHTML =
                rowHtml;


            const newRow =
                temporary.querySelector(
                    'tr'
                );


            if (!newRow) {
                return;
            }


            const index =
                this.rows.findIndex(
                    row =>
                        String(
                            row.dataset.id
                        ) ===
                        String(id)
                );


            if (
                index === -1
            ) {

                this.addRow(
                    rowHtml
                );

                return;
            }


            this.rows[index] =
                newRow;


            this.render();
        }



        /*
        |--------------------------------------------------------------------------
        | REMOVE ROW
        |--------------------------------------------------------------------------
        */

        removeRow(
            id
        ) {

            this.rows =
                this.rows.filter(
                    row =>
                        String(
                            row.dataset.id
                        ) !==
                        String(id)
                );


            this.render();
        }



        /*
        |--------------------------------------------------------------------------
        | FIND TABLE ROW
        |--------------------------------------------------------------------------
        */

        findRow(
            id
        ) {

            return this.rows.find(
                row =>
                    String(
                        row.dataset.id
                    ) ===
                    String(id)
            );
        }

    }


    window.DataTableEngine =
        DataTableEngine;



    /*
    |--------------------------------------------------------------------------
    | GET DATATABLE
    |--------------------------------------------------------------------------
    */

    function getDataTable(
        selector = null
    ) {

        let element;


        if (selector) {

            element =
                typeof selector ===
                'string'

                    ? document.querySelector(
                        selector
                    )

                    : selector;

        } else {

            element =
                document.querySelector(
                    '[data-datatable]'
                );
        }


        if (!element) {
            return null;
        }


        return element.__dataTable ||
            null;
    }



    window.getDataTable =
        getDataTable;



    /*
    |--------------------------------------------------------------------------
    | AJAX FORM ENGINE
    |--------------------------------------------------------------------------
    */

    async function submitAjaxForm(
        form
    ) {

        const button =
            form.querySelector(
                '[type="submit"]'
            );


        const originalText =
            button
                ? button.textContent
                : 'Simpan';


        if (button) {

            button.disabled =
                true;

            button.textContent =
                'Memproses...';
        }


        try {

            const response =
                await fetch(
                    form.action,
                    {
                        method: 'POST',

                        body:
                            new FormData(
                                form
                            ),

                        headers: {
                            'X-Requested-With':
                                'XMLHttpRequest',

                            'Accept':
                                'application/json'
                        }
                    }
                );


            const contentType =
                response.headers.get(
                    'content-type'
                ) || '';


            const data =
                contentType.includes(
                    'application/json'
                )
                    ? await response.json()
                    : {};


            /*
            |--------------------------------------------------------------------------
            | VALIDATION ERROR
            |--------------------------------------------------------------------------
            */

            if (
                !response.ok
            ) {

                let message =
                    data.message ||
                    'Data gagal diproses.';


                if (
                    data.errors
                ) {

                    message =
                        Object.values(
                            data.errors
                        )
                        .flat()
                        .join(' ');
                }


                throw new Error(
                    message
                );
            }


            /*
            |--------------------------------------------------------------------------
            | CLOSE MODAL
            |--------------------------------------------------------------------------
            */

            const modal =
                form.closest(
                    '.modal'
                );


            if (modal) {

                ModalEngine.close(
                    modal
                );
            }


            /*
            |--------------------------------------------------------------------------
            | SUCCESS
            |--------------------------------------------------------------------------
            */

            Toast.success(
                data.message ||
                'Data berhasil disimpan.'
            );


            /*
            |--------------------------------------------------------------------------
            | SERVER RETURN ROW
            |--------------------------------------------------------------------------
            */

            if (
                data.row_html
            ) {

                const table =
                    getDataTable(
                        data.table_selector
                    );


                if (table) {

                    if (
                        data.id &&
                        data.action ===
                        'update'
                    ) {

                        table.updateRow(
                            data.id,
                            data.row_html
                        );

                    } else {

                        table.addRow(
                            data.row_html
                        );
                    }
                }
            }


            /*
            |--------------------------------------------------------------------------
            | SERVER RETURN ALL ROWS
            |--------------------------------------------------------------------------
            */

            if (
                data.rows_html
            ) {

                const table =
                    getDataTable(
                        data.table_selector
                    );


                if (table) {

                    table.replaceRows(
                        data.rows_html
                    );
                }
            }


            /*
            |--------------------------------------------------------------------------
            | EVENT
            |--------------------------------------------------------------------------
            */

            document.dispatchEvent(
                new CustomEvent(
                    'ajax:success',
                    {
                        detail: data
                    }
                )
            );


        } catch (
            error
        ) {

            console.error(
                error
            );


            Toast.error(
                error.message ||
                'Terjadi kesalahan.'
            );


        } finally {

            if (button) {

                button.disabled =
                    false;

                button.textContent =
                    originalText;
            }
        }
    }



    /*
    |--------------------------------------------------------------------------
    | CONFIRM ENGINE
    |--------------------------------------------------------------------------
    */

    function openConfirm(
        button
    ) {

        const modal =
            document.getElementById(
                'confirmModal'
            );


        if (!modal) {

            Toast.error(
                'Confirm modal tidak ditemukan.'
            );

            return;
        }


        const title =
            button.dataset.confirmTitle ||
            'Konfirmasi';


        const message =
            button.dataset.confirmMessage ||
            'Apakah Anda yakin?';


        const action =
            button.dataset.confirmAction ||
            '';


        const titleElement =
            modal.querySelector(
                '[data-confirm-title]'
            );


        const messageElement =
            modal.querySelector(
                '[data-confirm-message]'
            );


        const form =
            modal.querySelector(
                '[data-confirm-form]'
            );


        if (titleElement) {

            titleElement.textContent =
                title;
        }


        if (messageElement) {

            messageElement.textContent =
                message;
        }


        if (form) {

            form.action =
                action;
        }


        modal.classList.add(
            'is-open'
        );


        modal.setAttribute(
            'aria-hidden',
            'false'
        );
    }



    /*
    |--------------------------------------------------------------------------
    | CONFIRM SUBMIT
    |--------------------------------------------------------------------------
    */

    async function submitConfirm(
        form
    ) {

        const button =
            form.querySelector(
                '[type="submit"]'
            );


        if (button) {

            button.disabled =
                true;

            button.textContent =
                'Memproses...';
        }


        try {

            const response =
                await fetch(
                    form.action,
                    {
                        method: 'POST',

                        body:
                            new FormData(
                                form
                            ),

                        headers: {
                            'X-Requested-With':
                                'XMLHttpRequest',

                            'Accept':
                                'application/json'
                        }
                    }
                );


            const contentType =
                response.headers.get(
                    'content-type'
                ) || '';


            const data =
                contentType.includes(
                    'application/json'
                )
                    ? await response.json()
                    : {};


            if (
                !response.ok
            ) {

                throw new Error(
                    data.message ||
                    'Operasi gagal.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | CLOSE
            |--------------------------------------------------------------------------
            */

            const modal =
                form.closest(
                    '.modal'
                );


            if (modal) {

                ModalEngine.close(
                    modal
                );
            }


            /*
            |--------------------------------------------------------------------------
            | TOAST
            |--------------------------------------------------------------------------
            */

            Toast.success(
                data.message ||
                'Operasi berhasil.'
            );


            /*
            |--------------------------------------------------------------------------
            | UPDATE ROW
            |--------------------------------------------------------------------------
            */

            if (
                data.row_html
            ) {

                const table =
                    getDataTable(
                        data.table_selector
                    );


                if (
                    table &&
                    data.id
                ) {

                    table.updateRow(
                        data.id,
                        data.row_html
                    );
                }
            }


            /*
            |--------------------------------------------------------------------------
            | REMOVE ROW
            |--------------------------------------------------------------------------
            */

            if (
                data.remove_id
            ) {

                const table =
                    getDataTable(
                        data.table_selector
                    );


                if (table) {

                    table.removeRow(
                        data.remove_id
                    );
                }
            }


            /*
            |--------------------------------------------------------------------------
            | RELOAD ROWS
            |--------------------------------------------------------------------------
            */

            if (
                data.rows_html
            ) {

                const table =
                    getDataTable(
                        data.table_selector
                    );


                if (table) {

                    table.replaceRows(
                        data.rows_html
                    );
                }
            }


            document.dispatchEvent(
                new CustomEvent(
                    'confirm:success',
                    {
                        detail: data
                    }
                )
            );


        } catch (
            error
        ) {

            console.error(
                error
            );


            Toast.error(
                error.message ||
                'Operasi gagal.'
            );


        } finally {

            if (button) {

                button.disabled =
                    false;

                button.textContent =
                    'Ya, Lanjutkan';
            }
        }
    }



    /*
    |--------------------------------------------------------------------------
    | GLOBAL CLICK
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'click',
        event => {


            /*
            |--------------------------------------------------------------------------
            | OPEN MODAL
            |--------------------------------------------------------------------------
            */

            const openButton =
                event.target.closest(
                    '[data-modal-open]'
                );


            if (openButton) {

                const modal =
                    document.getElementById(
                        openButton.dataset.modalOpen
                    );


                if (modal) {

                    ModalEngine.open(
                        modal,
                        openButton
                    );
                }


                return;
            }



            /*
            |--------------------------------------------------------------------------
            | CLOSE MODAL
            |--------------------------------------------------------------------------
            */

            const closeButton =
                event.target.closest(
                    '[data-modal-close]'
                );


            if (closeButton) {

                const modal =
                    closeButton.closest(
                        '.modal'
                    );


                if (modal) {

                    ModalEngine.close(
                        modal
                    );
                }


                return;
            }



            /*
            |--------------------------------------------------------------------------
            | CONFIRM
            |--------------------------------------------------------------------------
            */

            const confirmButton =
                event.target.closest(
                    '[data-confirm-open]'
                );


            if (confirmButton) {

                openConfirm(
                    confirmButton
                );

                return;
            }



            /*
            |--------------------------------------------------------------------------
            | CLICK OVERLAY
            |--------------------------------------------------------------------------
            */

            if (
                event.target.classList.contains(
                    'modal'
                )
            ) {

                ModalEngine.close(
                    event.target
                );
            }

        }
    );



    /*
    |--------------------------------------------------------------------------
    | GLOBAL SUBMIT
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'submit',
        event => {


            /*
            |--------------------------------------------------------------------------
            | CONFIRM FORM
            |--------------------------------------------------------------------------
            */

            const confirmForm =
                event.target.closest(
                    '[data-confirm-form]'
                );


            if (confirmForm) {

                event.preventDefault();

                submitConfirm(
                    confirmForm
                );

                return;
            }



            /*
            |--------------------------------------------------------------------------
            | MODAL FORM
            |--------------------------------------------------------------------------
            */

            const modalForm =
                event.target.closest(
                    '.modal form'
                );


            if (modalForm) {

                event.preventDefault();

                submitAjaxForm(
                    modalForm
                );
            }

        }
    );



    /*
    |--------------------------------------------------------------------------
    | ESCAPE
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'keydown',
        event => {

            if (
                event.key !==
                'Escape'
            ) {

                return;
            }


            document
                .querySelectorAll(
                    '.modal.is-open'
                )
                .forEach(
                    modal => {

                        ModalEngine.close(
                            modal
                        );
                    }
                );
        }
    );



    /*
    |--------------------------------------------------------------------------
    | INITIALIZE ALL DATATABLE
    |--------------------------------------------------------------------------
    */

    function initializeDataTables() {

        document
            .querySelectorAll(
                '[data-datatable]'
            )
            .forEach(
                element => {

                    if (
                        element.__dataTable
                    ) {

                        return;
                    }


                    const table =
                        new DataTableEngine(
                            element
                        );


                    element.__dataTable =
                        table;
                }
            );
    }


    initializeDataTables();



    /*
    |--------------------------------------------------------------------------
    | PUBLIC RE-INITIALIZE
    |--------------------------------------------------------------------------
    */

    window.initializeDataTables =
        initializeDataTables;


})();