<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClientUser;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Helpers\MediaHelper;
use App\Exceptions\UserExistPreviouslyException;

class ClientUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clientUser = ClientUser::orderByDesc('client_users_id')->get();

        return response()->json([
                                'data' => $clientUser ?? [],
                                'success' => true
                                ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_user_name' => 'required|string|max:255',
            'client_user_slug' => ['required' ,'string', 'max:255',
                                     Rule::unique('client_users', 'client_users_slug')
                                           ->whereNull('deleted_at')],
            'client_user_img_id' => 'required|integer|exists:media,media_id',
            'client_user_order' => 'nullable|integer',
            'status' => 'nullable|boolean',
        ]);

        // Check for validation failure
        if ($validator->fails()) {
            return response()->json([
                                       'success' => false,
                                       'message' => $validator->errors()
                                       ], 403);
        }

        if (ClientUser::withTrashed()
                        ->where('client_users_slug', $request->client_users_slug)
                        ->exists())
        {
            throw new UserExistPreviouslyException('Oops! It appears that the chosen client user slug is already in use. Please select a different one and try again.');
        }

        $clientUserImagePath = MediaHelper::getMediaPath($request->client_user_img_id ?? null);

        $data = [
            'client_users_name' => $request->client_user_name,
            'client_users_slug' => $request->client_user_slug,
            'client_users_img_url' => $clientUserImagePath,
            'client_users_order' => $request->client_user_order ?? 0,
            'status' => true,
        ];

        //create client user
        $clientUser = ClientUser::create($data);

        if ($clientUser) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Client user created successfully'
                                    ], 201);
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
     * @param string $name
     * @return Response
     */
    public function restore(string $name)
    {
        $clientUser = ClientUser::withTrashed(true)
                                  ->where('client_users_slug', $name)
                                  ->first();

        if ($clientUser) {

            $clientUser->restore();
            return response()->json([
                                    'success' => true,
                                    'message' => 'Client user restored successfully'
                                    ], 202);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Client user not found'
                                    ], 404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param string $id
     * @return response
     */
    public function show(string $id)
    {
        $clientUser = ClientUser::find($id);

        if ($clientUser) {

            return response()->json([
                                    'data' => $clientUser,
                                    'success' => true,
                                    'message' => ''
                                    ], 200);
        } else {

            return response()->json([
                                    'data' => [],
                                    'success' => false,
                                    'message' => 'Client User not found'
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
        $clientUser = ClientUser::find($id);

        if (!$clientUser) {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Client User not found'
                                    ], 404);
        }

        $validator = Validator::make($request->all(), [
            'client_user_name' => 'required|string|max:255',
            'client_user_slug' => ['required' ,'string', 'max:255',
                                     Rule::unique('client_users', 'client_users_slug')
                                           ->ignore($id, 'client_users_id')
                                           ->whereNull('deleted_at')],
            'client_user_img_id' => 'required|integer|exists:media,media_id',
            'status' => 'required|boolean',

        ]);

        // Check for validation failure
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->errors()
                                    ], 403);
        }

        $clientUserImagePath = MediaHelper::getMediaPath($request->client_user_img_id ?? null);

        $data = [
            'client_users_name' => $request->client_user_name,
            'client_users_slug' => $request->client_user_slug,
            'client_users_img_url' => $clientUserImagePath,
            'client_users_order' => $request->client_user_order ?? 0,
            'status' => $request->status,
        ];

        $result = $clientUser->update($data);

        if ($result) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Client user updated successfully'
                                    ], 201);
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
        $clientUser = ClientUser::find($id);

        if (!$clientUser) {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Client User not found'
                                    ], 404);
        }

        $result = $clientUser->delete();

        if ($result) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Client user deleted successfully'
                                    ], 201);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, please try again later'
                                    ], 422);
        }
    }
}
