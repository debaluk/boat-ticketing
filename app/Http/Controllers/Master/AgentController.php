<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AgentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $agents = Agent::query()
            ->orderBy('code')
            ->get();

        return view(
            'master.agents.index',
            compact('agents')
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

                'code' => [
                    'required',
                    'string',
                    'max:50',
                    'unique:agents,code',
                ],

                'name' => [
                    'required',
                    'string',
                    'max:150',
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

            ]);


            $agent = Agent::create([

                'uuid' =>
                    (string) Str::uuid(),

                'code' =>
                    $validated['code'],

                'name' =>
                    $validated['name'],

                'phone' =>
                    $validated['phone'] ?? null,

                'email' =>
                    $validated['email'] ?? null,

                'address' =>
                    $validated['address'] ?? null,

                'status' =>
                    'active',

            ]);


            $agent->refresh();


            return response()->json([

                'success' => true,

                'message' =>
                    "Agent {$agent->code} berhasil ditambahkan.",

                'action' =>
                    'create',

                'id' =>
                    $agent->id,

                'status' =>
                    $agent->status,

                'row_html' =>
                    $this->renderRow($agent),

            ], 201);


        } catch (ValidationException $e) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Data agent belum lengkap atau tidak valid.',

                'errors' =>
                    $e->errors(),

            ], 422);


        } catch (\Throwable $e) {

            report($e);

            return response()->json([

                'success' => false,

                'message' =>
                    'Terjadi kesalahan saat menambahkan agent.',

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
        Agent $agent
    ) {
        try {

            $validated = $request->validate([

                'code' => [
                    'required',
                    'string',
                    'max:50',

                    Rule::unique(
                        'agents',
                        'code'
                    )->ignore($agent->id),
                ],

                'name' => [
                    'required',
                    'string',
                    'max:150',
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

            ]);


            $agent->update([

                'code' =>
                    $validated['code'],

                'name' =>
                    $validated['name'],

                'phone' =>
                    $validated['phone'] ?? null,

                'email' =>
                    $validated['email'] ?? null,

                'address' =>
                    $validated['address'] ?? null,

            ]);


            $agent->refresh();


            return response()->json([

                'success' => true,

                'message' =>
                    "Agent {$agent->code} berhasil diperbarui.",

                'action' =>
                    'update',

                'id' =>
                    $agent->id,

                'status' =>
                    $agent->status,

                'row_html' =>
                    $this->renderRow($agent),

            ]);


        } catch (ValidationException $e) {

            return response()->json([

                'success' => false,

                'message' =>
                    'Data agent belum lengkap atau tidak valid.',

                'errors' =>
                    $e->errors(),

            ], 422);


        } catch (\Throwable $e) {

            report($e);

            return response()->json([

                'success' => false,

                'message' =>
                    'Terjadi kesalahan saat memperbarui agent.',

            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | DEACTIVATE
    |--------------------------------------------------------------------------
    */

    public function deactivate(Agent $agent)
    {
        try {

            $agent->update([
                'status' => 'inactive',
            ]);

            $agent->refresh();


            return response()->json([

                'success' => true,

                'message' =>
                    "Agent {$agent->code} berhasil dinonaktifkan.",

                'action' =>
                    'deactivate',

                'id' =>
                    $agent->id,

                'status' =>
                    $agent->status,

                'row_html' =>
                    $this->renderRow($agent),

            ]);


        } catch (\Throwable $e) {

            report($e);

            return response()->json([

                'success' => false,

                'message' =>
                    'Terjadi kesalahan saat menonaktifkan agent.',

            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | ACTIVATE
    |--------------------------------------------------------------------------
    */

    public function activate(Agent $agent)
    {
        try {

            $agent->update([
                'status' => 'active',
            ]);

            $agent->refresh();


            return response()->json([

                'success' => true,

                'message' =>
                    "Agent {$agent->code} berhasil diaktifkan.",

                'action' =>
                    'activate',

                'id' =>
                    $agent->id,

                'status' =>
                    $agent->status,

                'row_html' =>
                    $this->renderRow($agent),

            ]);


        } catch (\Throwable $e) {

            report($e);

            return response()->json([

                'success' => false,

                'message' =>
                    'Terjadi kesalahan saat mengaktifkan agent.',

            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | RENDER ROW
    |--------------------------------------------------------------------------
    */

    private function renderRow(Agent $agent)
    {
        /*
        |--------------------------------------------------------------------------
        | STATUS BADGE
        |--------------------------------------------------------------------------
        */

        $statusBadge =
            $agent->status === 'active'

                ? '<span class="badge badge-success">ACTIVE</span>'

                : '<span class="badge badge-muted">INACTIVE</span>';


        /*
        |--------------------------------------------------------------------------
        | STATUS ACTION
        |--------------------------------------------------------------------------
        */

        if ($agent->status === 'active') {

            $statusAction = '

                <button
                    type="button"
                    class="action-icon action-danger"

                    data-confirm-open

                    data-id="' .
                        $agent->id .
                    '"

                    data-confirm-title="Nonaktifkan Agent"

                    data-confirm-message="Agent ' .
                        e($agent->code) .
                        ' akan dinonaktifkan dan tidak dapat digunakan untuk transaksi baru."

                    data-confirm-action="' .
                        route(
                            'master.agents.deactivate',
                            $agent->id
                        ) .
                    '"

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

            ';

        } else {

            $statusAction = '

                <button
                    type="button"
                    class="action-icon action-success"

                    data-confirm-open

                    data-id="' .
                        $agent->id .
                    '"

                    data-confirm-title="Aktifkan Agent"

                    data-confirm-message="Agent ' .
                        e($agent->code) .
                        ' akan diaktifkan kembali."

                    data-confirm-action="' .
                        route(
                            'master.agents.activate',
                            $agent->id
                        ) .
                    '"

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

            ';
        }


        /*
        |--------------------------------------------------------------------------
        | ROW
        |--------------------------------------------------------------------------
        */

        return '

            <tr data-id="' .
                $agent->id .
            '">

                <td>
                    ' .
                    e($agent->code) .
                '
                </td>

                <td>
                    ' .
                    e($agent->name) .
                '
                </td>

                <td>
                    ' .
                    e($agent->phone ?: '-') .
                '
                </td>

                <td>
                    ' .
                    e($agent->email ?: '-') .
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

                        data-modal-open="agentModal"
                        data-mode="edit"

                        data-id="' .
                            $agent->id .
                        '"

                        data-code="' .
                            e($agent->code) .
                        '"

                        data-name="' .
                            e($agent->name) .
                        '"

                        data-phone="' .
                            e($agent->phone) .
                        '"

                        data-email="' .
                            e($agent->email) .
                        '"

                        data-address="' .
                            e($agent->address) .
                        '"

                        data-status="' .
                            e($agent->status) .
                        '"

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

                    ' .
                    $statusAction .
                    '

                </td>

            </tr>

        ';
    }
}