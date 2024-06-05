<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Associate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Helpers\MediaHelper;
use App\Exceptions\UserExistPreviouslyException;

class AssociateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $associate = Associate::orderByDesc('associate_id')->get();

        return response()->json([
                                'data' => $associate ?? [],
                                'success' => true
                                ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'associate_name' => ['required','string','max:255', Rule::unique('associates', 'associate_name')->whereNull('deleted_at')],
            'associate_img_id' => ['required','integer','exists:media,media_id'],
            'associate_order' => ['nullable','integer'],
            'associate_status' => ['nullable','boolean'],
        ]);

        // Check for validation failure
        if ($validator->fails()) {
            return response()->json([
                                    'success' => false,
                                    'message' => $validator->errors()
                                    ], 403);
        }

        //check for duplicated association
        if (Associate::withTrashed()
                  ->where('associate_name', $request->associate_name)
                  ->exists())
        {
            throw new UserExistPreviouslyException('Oops! It appears that the chosen Associate Name is already in use. Please select a different one and try again.');
        }

        $associateImagePath = MediaHelper::getMediaPath($request->associate_img_id ?? null);

        $data = [
            'associate_name' => $request->associate_name,
            'associate_img_url' => $associateImagePath,
            'associate_order' => $request->associate_order ?? 0,
            'associate_status' => true
        ];

        $associate = Associate::create($data);

        if ($associate) {

            return response()->json([
                                   'success' => true,
                                   'message' => 'Associate Created Successfully',
                                    ], 200);
        } else {

            return response()->json([
                                   'success' => false,
                                   'message' => 'Something went wrong'
                                    ], 403);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param string $name
     * @return Response
     */
    public function restore(string $name)
    {
        $associate = Associate::withTrashed()->where('associate_name', $name)->first();

        if ($associate) {

            $associate->restore();

            return response()->json([
                                   'success' => true,
                                   'message' => 'Associate Restored Successfully',
                                    ], 200);
        } else {

            return response()->json([
                                   'success' => false,
                                   'message' => 'Something went wrong'
                                    ], 403);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param string $id
     * @return Response
     */
    public function show(string $id)
    {
        $associate = Associate::find($id);

        if ($associate) {

            return response()->json([
                                    'success' => true,
                                    'data' => $associate,
                                    'message' => ''
                                    ], 200);
        } else {

            return response()->json([
                                   'success' => false,
                                   'data' => [],
                                   'message' => 'Associates not found.'
                                    ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param string $id
     * @param Request $request
     * @return Response
     */
    public function update(Request $request, string $id)
    {
        $associate = Associate::find($id);

        if (!$associate) {

            return response()->json([
                                   'success' => false,
                                   'message' => 'Associates not found.'
                                    ], 404);
        }

        $validator = Validator::make($request->all(),[
            'associate_name' => ['required','string','max:255', Rule::unique('associates', 'associate_name')
                                                                      ->ignore($id, 'associate_id')
                                                                      ->whereNull('deleted_at')],
            'associate_img_id' => ['required','integer','exists:media,media_id'],
            'associate_order' => ['nullable','integer'],
            'associate_status' => ['nullable','boolean'],
        ]);

        // Check for validation failure
        if ($validator->fails()) {
            return response()->json([
                                    'success' => false,
                                    'message' => $validator->errors()
                                    ], 403);
        }

        $associateImagePath = MediaHelper::getMediaPath($request->associate_img_id ?? null);

        $data = [
            'associate_name' => $request->associate_name,
            'associate_img_url' => $associateImagePath,
            'associate_order' => $request->associate_order ?? 0,
            'associate_status' => true
        ];

        $associate = $associate->update($data);

        if ($associate) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Associate Updated Successfully',
                                    ], 200);
        } else {

            return response()->json([
                                   'success' => false,
                                   'message' => 'Something went wrong, please try again'
                                    ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return Response
     */
    public function destroy(string $id)
    {
        $associate = Associate::find($id);

        if (!$associate) {

            return response()->json([
                                   'success' => false,
                                   'message' => 'Associates not found.'
                                    ], 404);
        }

        $associate = $associate->delete();

        if ($associate) {

            return response()->json([
                                   'success' => true,
                                   'message' => 'Associate Deleted Successfully',
                                    ], 200);
        } else {

            return response()->json([
                                   'success' => false,
                                   'message' => 'Something went wrong, please try again'
                                    ], 422);
        }
    }
}
