<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Boat;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BoatController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $boats = Boat::orderBy('code')->get();

        return view('master.boats.index', compact('boats'));
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                'unique:boats,code',
            ],

            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'registration_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'capacity' => [
                'required',
                'integer',
                'min:1',
            ],

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],
        ]);

        $boat = Boat::create($validated);

        return response()->json([
            'success' => true,

            'message' =>
                "Boat {$boat->code} berhasil ditambahkan.",

            'action' => 'create',

            'id' => $boat->id,

            'status' => $boat->status,

            'row_html' => $this->renderRow($boat),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Boat $boat
    ) {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('boats', 'code')
                    ->ignore($boat->id),
            ],

            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'registration_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'capacity' => [
                'required',
                'integer',
                'min:1',
            ],

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],
        ]);

        $boat->update($validated);

        $boat->refresh();

        return response()->json([
            'success' => true,

            'message' =>
                "Boat {$boat->code} berhasil diperbarui.",

            'action' => 'update',

            'id' => $boat->id,

            'status' => $boat->status,

            'row_html' => $this->renderRow($boat),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | DEACTIVATE
    |--------------------------------------------------------------------------
    */

    public function deactivate(Boat $boat)
    {
        $boat->update([
            'status' => 'inactive',
        ]);

        $boat->refresh();

        return response()->json([
            'success' => true,

            'message' =>
                "Boat {$boat->code} berhasil dinonaktifkan.",

            'action' => 'deactivate',

            'id' => $boat->id,

            'status' => $boat->status,

            'row_html' => $this->renderRow($boat),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | ACTIVATE
    |--------------------------------------------------------------------------
    */

    public function activate(Boat $boat)
    {
        $boat->update([
            'status' => 'active',
        ]);

        $boat->refresh();

        return response()->json([
            'success' => true,

            'message' =>
                "Boat {$boat->code} berhasil diaktifkan.",

            'action' => 'activate',

            'id' => $boat->id,

            'status' => $boat->status,

            'row_html' => $this->renderRow($boat),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | RENDER ROW
    |--------------------------------------------------------------------------
    */

    private function renderRow(Boat $boat)
	{
		$statusBadge = $boat->status === 'active'
			? '<span class="badge badge-success">ACTIVE</span>'
			: '<span class="badge badge-muted">INACTIVE</span>';

		if ($boat->status === 'active') {

			$statusAction = '
				<button
					type="button"
					class="action-icon action-danger"

					data-confirm-open

					data-id="' . $boat->id . '"

					data-confirm-title="Nonaktifkan Boat"

					data-confirm-message="Boat ' . e($boat->code) . ' akan dinonaktifkan dan tidak dapat digunakan untuk operasional baru."

					data-confirm-action="' .
						route(
							'master.boats.deactivate',
							$boat->id
						) .
					'"

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
			';

		} else {

			$statusAction = '
				<button
					type="button"
					class="action-icon action-success"

					data-confirm-open

					data-id="' . $boat->id . '"

					data-confirm-title="Aktifkan Boat"

					data-confirm-message="Boat ' . e($boat->code) . ' akan diaktifkan kembali."

					data-confirm-action="' .
						route(
							'master.boats.activate',
							$boat->id
						) .
					'"

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
			';
		}


		return '
			<tr data-id="' . $boat->id . '">

				<td>
					' . e($boat->code) . '
				</td>

				<td>
					' . e($boat->name) . '
				</td>

				<td>
					' . e($boat->registration_number ?: '-') . '
				</td>

				<td>
					' . e($boat->capacity) . '
				</td>

				<td>
					' . $statusBadge . '
				</td>

				<td class="table-action">

					<button
						type="button"
						class="action-icon action-edit"

						data-modal-open="boatModal"
						data-mode="edit"

						data-id="' . $boat->id . '"
						data-code="' . e($boat->code) . '"
						data-name="' . e($boat->name) . '"
						data-registration_number="' .
							e($boat->registration_number) .
						'"
						data-capacity="' . $boat->capacity . '"
						data-status="' . $boat->status . '"

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

					' . $statusAction . '

				</td>

			</tr>
		';
	}
}