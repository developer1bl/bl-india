<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Exceptions\UserExistPreviouslyException;

class HolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $holidays = Holiday::OrderbyDesc('holiday_id')->get();

        return response()->json([
                                'data' => $holidays?? [],
                                'success' => true,
                                ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'holiday_name' => ['required','string','max:255', Rule::unique('holidays', 'holiday_name')->whereNull('deleted_at')],
            'holiday_date' => 'required|date',
            'holiday_type' => 'required|boolean',
        ]);

         //if the request have some validation errors
         if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        $data = [
            'holiday_name' => $request->holiday_name,
            'holiday_date' => date('Y-m-d', strtotime($request->holiday_date)),
            'holiday_type' => $request->holiday_type,
        ];

        if (Holiday::withTrashed()->where('holiday_name',$request->holiday_name)->exists())
        {
            throw new UserExistPreviouslyException('Oops! It appears that the chosen Holiday Name is already in use. Please select a different one and try again');
        }

        $holiday = Holiday::create($data);

        if ($holiday) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Holiday created successfully'
                                    ], 202);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, please try again later'
                                    ], 422);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param string $request
     * @return Response
     */
    public function restore(string $request)
    {
        $holiday = Holiday::withTrashed()->where('holiday_name',$request)->first();

        if ($holiday) {

            $holiday->restore();

            return response()->json([
                                   'success' => true,
                                   'message' => 'Holiday restored successfully'
                                    ], 202);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, please try again later'
                                    ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $holiday = Holiday::find($id);

        if ($holiday) {

            return response()->json([
                                    'data' => $holiday,
                                    'success' => true,
                                    'message' => ''
                                    ], 200);
        } else {

            return response()->json([
                                    'data' => [],
                                    'success' => false,
                                    'message' => 'Holiday not found'
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
     * @param string $id,
     * @param Request $request
     * @return Response
     */
    public function update(Request $request, string $id)
    {
        $holiday = Holiday::find($id);

        if (!$holiday) {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Holiday not found'
                                    ],404);
        }

        $validator = Validator::make($request->all(), [
            'holiday_name' => ['required','string','max:255', Rule::unique('holidays', 'holiday_name')
                                                                    ->ignore($id, 'holiday_id')
                                                                    ->whereNull('deleted_at')],
            'holiday_date' => 'required|date',
            'holiday_type' => 'required|boolean',
        ]);

         //if the request have some validation errors
         if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        $data = [
            'holiday_name' => $request->holiday_name,
            'holiday_date' => date('Y-m-d', strtotime($request->holiday_date)),
            'holiday_type' => $request->holiday_type,
        ];

        $result = $holiday->update($data);

        if ($result) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Holiday updated successfully'
                                    ], 202);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, please try again later'
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
        $holiday = Holiday::find($id);

        if (!$holiday) {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Holiday not found'
                                    ],404);
        }

        $result = $holiday->delete();

        if ($result) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Holiday deleted successfully'
                                    ], 202);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, please try again later'
                                    ], 422);
        }
    }
}
