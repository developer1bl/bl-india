<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Team;
use Illuminate\Support\Facades\Validator;
use App\Helpers\MediaHelper;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $team  = Team::orderByDesc('id')->get();

        return response()->json([
                                'data' => $team ?? [],
                               'success' => true
                                ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required','string','max:255'],
            'profile_id' => 'integer|exists:media,media_id',
            'designation' => 'nullable|string',
        ]);

         //if the request have some validation errors
         if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        //get image url path
        $teamUserImagePath = MediaHelper::getMediaPath($request->profile_id ?? null);

        //data array
        $data = [
            'name' => $request->name,
            'designation' => $request->designation,
            'profile_url' => $teamUserImagePath,
            'status' => true
        ];

        $team = Team::create($data);

        if ($team) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Team created successfully'
                                    ], 200);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, please try again later'
                                    ], 422);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function restore(string $name)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param string $id
     * @return response
     */
    public function show(string $id)
    {
        $team = Team::find($id);

        if ($team) {

            return response()->json([
                                    'data' => $team,
                                    'success' => true,
                                    'message' => ''
                                    ], 200);
            } else {

            return response()->json([
                                    'data' => [],
                                    'success' => false,
                                    'message' => 'Team member not found'
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
     * @param request $request
     * @param string $id
     * @return response
     */
    public function update(Request $request, string $id)
    {
        $team = Team::find($id);

        if (!$team) {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Team member not found'
                                    ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required','string','max:255'],
            'profile_id' => 'integer|exists:media,media_id',
            'designation' => 'nullable|string',
            'status' => 'nullable|boolean'
        ]);

         //if the request have some validation errors
         if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        //get image url path
        $teamUserImagePath = MediaHelper::getMediaPath($request->profile_id ?? null);

        //data array
        $data = [
            'name' => $request->name,
            'designation' => $request->designation,
            'profile_url' => $teamUserImagePath,
            'status' => $request->status
        ];

        $team = $team->update($data);

        if ($team) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Team Updated successfully'
                                    ], 200);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, please try again later'
                                    ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $team = Team::find($id);

        if (!$team) {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Team member not found'
                                    ], 404);
        }

        $team = $team->delete();

        if ($team) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Team member deleted successfully'
                                    ], 200);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, please try again later'
                                    ], 422);
        }
    }
}
