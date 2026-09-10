<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CustomerController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $customers = Customer::query()
            ->leftJoin(
                'countries',
                'countries.id',
                '=',
                'customers.country_id'
            )
            ->select(
                'customers.*',
                'countries.name as country_name'
            )
            ->orderBy('customers.code')
            ->get();

        $countries = Country::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view(
            'master.customers.index',
            compact('customers', 'countries')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        try {

            $validated = $request->validate([

                'name' => [
                    'required',
                    'string',
                    'max:100',
                ],

                'phone' => [
                    'nullable',
                    'string',
                    'max:30',
                ],

                'email' => [
                    'nullable',
                    'email',
                    'max:150',
                ],

                'address' => [
                    'nullable',
                    'string',
                ],

                'country_id' => [
                    'nullable',
                    'exists:countries,id',
                ],

                'status' => [
                    'required',
                    Rule::in([
                        'active',
                        'inactive',
                    ]),
                ],
            ]);

            /*
            |--------------------------------------------------------------------------
            | GENERATE CODE
            |--------------------------------------------------------------------------
            */

            $validated['code'] =
                $this->generateCustomerCode();


            /*
            |--------------------------------------------------------------------------
            | GENERATE UUID
            |--------------------------------------------------------------------------
            */

            $validated['uuid'] =
                (string) Str::uuid();


            /*
            |--------------------------------------------------------------------------
            | CREATE
            |--------------------------------------------------------------------------
            */

            $customer = Customer::create($validated);


            /*
            |--------------------------------------------------------------------------
            | LOAD COUNTRY
            |--------------------------------------------------------------------------
            */

            $customer->load('country');


            /*
            |--------------------------------------------------------------------------
            | RESPONSE
            |--------------------------------------------------------------------------
            */

            return response()->json([

                'success' => true,

                'message' =>
                    "Customer {$customer->code} berhasil ditambahkan.",

                'action' => 'create',

                'id' => $customer->id,

                'status' => $customer->status,

                'row_html' =>
                    $this->renderRow($customer),

            ], 201);


        } catch (ValidationException $e) {

            /*
            |--------------------------------------------------------------------------
            | VALIDATION ERROR
            |--------------------------------------------------------------------------
            */

            return response()->json([

                'success' => false,

                'message' =>
                    'Data customer belum lengkap atau tidak valid.',

                'errors' =>
                    $e->errors(),

            ], 422);


        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | SERVER ERROR
            |--------------------------------------------------------------------------
            */

            report($e);

            return response()->json([

                'success' => false,

                'message' =>
                    'Terjadi kesalahan saat menambahkan customer.',

            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Customer $customer
    ) {
        try {

            $validated = $request->validate([

                'name' => [
                    'required',
                    'string',
                    'max:100',
                ],

                'phone' => [
                    'nullable',
                    'string',
                    'max:30',
                ],

                'email' => [
                    'nullable',
                    'email',
                    'max:150',
                ],

                'address' => [
                    'nullable',
                    'string',
                ],

                'country_id' => [
                    'nullable',
                    'exists:countries,id',
                ],

                'status' => [
                    'required',
                    Rule::in([
                        'active',
                        'inactive',
                    ]),
                ],
            ]);


            /*
            |--------------------------------------------------------------------------
            | UPDATE
            |--------------------------------------------------------------------------
            */

            $customer->update($validated);


            /*
            |--------------------------------------------------------------------------
            | REFRESH
            |--------------------------------------------------------------------------
            */

            $customer->refresh();

            $customer->load('country');


            /*
            |--------------------------------------------------------------------------
            | RESPONSE
            |--------------------------------------------------------------------------
            */

            return response()->json([

                'success' => true,

                'message' =>
                    "Customer {$customer->code} berhasil diperbarui.",

                'action' => 'update',

                'id' => $customer->id,

                'status' => $customer->status,

                'row_html' =>
                    $this->renderRow($customer),

            ]);


        } catch (ValidationException $e) {

            /*
            |--------------------------------------------------------------------------
            | VALIDATION ERROR
            |--------------------------------------------------------------------------
            */

            return response()->json([

                'success' => false,

                'message' =>
                    'Data customer belum lengkap atau tidak valid.',

                'errors' =>
                    $e->errors(),

            ], 422);


        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | SERVER ERROR
            |--------------------------------------------------------------------------
            */

            report($e);

            return response()->json([

                'success' => false,

                'message' =>
                    'Terjadi kesalahan saat memperbarui customer.',

            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | DEACTIVATE
    |--------------------------------------------------------------------------
    */

    public function deactivate(Customer $customer)
    {
        try {

            $customer->update([
                'status' => 'inactive',
            ]);

            $customer->refresh();

            $customer->load('country');

            return response()->json([

                'success' => true,

                'message' =>
                    "Customer {$customer->code} berhasil dinonaktifkan.",

                'action' => 'deactivate',

                'id' => $customer->id,

                'status' => $customer->status,

                'row_html' =>
                    $this->renderRow($customer),

            ]);

        } catch (\Throwable $e) {

            report($e);

            return response()->json([

                'success' => false,

                'message' =>
                    'Terjadi kesalahan saat menonaktifkan customer.',

            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | ACTIVATE
    |--------------------------------------------------------------------------
    */

    public function activate(Customer $customer)
    {
        try {

            $customer->update([
                'status' => 'active',
            ]);

            $customer->refresh();

            $customer->load('country');

            return response()->json([

                'success' => true,

                'message' =>
                    "Customer {$customer->code} berhasil diaktifkan.",

                'action' => 'activate',

                'id' => $customer->id,

                'status' => $customer->status,

                'row_html' =>
                    $this->renderRow($customer),

            ]);

        } catch (\Throwable $e) {

            report($e);

            return response()->json([

                'success' => false,

                'message' =>
                    'Terjadi kesalahan saat mengaktifkan customer.',

            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | RENDER ROW
    |--------------------------------------------------------------------------
    */

   private function renderRow(Customer $customer)
{
    /*
    |--------------------------------------------------------------------------
    | COUNTRY
    |--------------------------------------------------------------------------
    */

    $countryName = '-';

    if ($customer->country_id) {

        $country = Country::find(
            $customer->country_id
        );

        if ($country) {
            $countryName = $country->name;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | STATUS BADGE
    |--------------------------------------------------------------------------
    */

    $statusBadge =
        $customer->status === 'active'

            ? '<span class="badge badge-success">ACTIVE</span>'

            : '<span class="badge badge-muted">INACTIVE</span>';


    /*
    |--------------------------------------------------------------------------
    | STATUS ACTION
    |--------------------------------------------------------------------------
    */

    if ($customer->status === 'active') {

        $statusAction = '

            <button
                type="button"
                class="action-icon action-danger"

                data-confirm-open

                data-id="' . $customer->id . '"

                data-confirm-title="Nonaktifkan Customer"

                data-confirm-message="Customer ' .
                    e($customer->code) .
                    ' akan dinonaktifkan dan tidak dapat digunakan untuk transaksi baru."

                data-confirm-action="' .
                    route(
                        'master.customers.deactivate',
                        $customer->id
                    ) .
                '"

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

        ';

    } else {

        $statusAction = '

            <button
                type="button"
                class="action-icon action-success"

                data-confirm-open

                data-id="' . $customer->id . '"

                data-confirm-title="Aktifkan Customer"

                data-confirm-message="Customer ' .
                    e($customer->code) .
                    ' akan diaktifkan kembali."

                data-confirm-action="' .
                    route(
                        'master.customers.activate',
                        $customer->id
                    ) .
                '"

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

        ';
    }


    /*
    |--------------------------------------------------------------------------
    | ROW
    |--------------------------------------------------------------------------
    */

    return '

        <tr data-id="' .
            $customer->id .
        '">

            <td>
                ' .
                e($customer->code) .
            '
            </td>

            <td>
                ' .
                e($customer->name) .
            '
            </td>

            <td>
                ' .
                e($customer->phone ?: '-') .
            '
            </td>

            <td>
                ' .
                e($customer->email ?: '-') .
            '
            </td>

            <td>
                ' .
                e($countryName) .
            '
            </td>

            <td>
                ' .
                $statusBadge .
            '
            </td>

            <td class="table-action">

                <button
                    type="button"
                    class="action-icon action-edit"

                    data-modal-open="customerModal"
                    data-mode="edit"

                    data-id="' .
                        $customer->id .
                    '"

                    data-code="' .
                        e($customer->code) .
                    '"

                    data-name="' .
                        e($customer->name) .
                    '"

                    data-phone="' .
                        e($customer->phone) .
                    '"

                    data-email="' .
                        e($customer->email) .
                    '"

                    data-address="' .
                        e($customer->address) .
                    '"

                    data-country_id="' .
                        e($customer->country_id) .
                    '"

                    data-status="' .
                        e($customer->status) .
                    '"

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

                ' .
                $statusAction .
                '

            </td>

        </tr>

    ';
}
    /*
    |--------------------------------------------------------------------------
    | GENERATE CUSTOMER CODE
    |--------------------------------------------------------------------------
    */

    private function generateCustomerCode(): string
    {
        $lastCustomer = Customer::withTrashed()
            ->orderByDesc('id')
            ->first();

        $nextNumber = $lastCustomer
            ? $lastCustomer->id + 1
            : 1;

        return 'CUS-' .
            str_pad(
                $nextNumber,
                6,
                '0',
                STR_PAD_LEFT
            );
    }
}