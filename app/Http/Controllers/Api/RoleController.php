<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Exceptions\UserExistPreviouslyException;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::all();

        return response()->json([
                                'data' => $roles ?? [],
                                'success' => true
                                ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:100', Rule::unique('roles', 'name')->whereNull('deleted_at')],
            'role_description' => 'required|string',
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        if (Role::withTrashed()
                  ->where('name', $request->name)
                  ->exists())
        {
            throw new UserExistPreviouslyException('Oops! It appears that the chosen Rule Name is already in use. Please select a different one and try again');
        }

        $result = Role::create([ 'name' => $request->name, 'role_description' => $request->role_description]);

        if($result){

            return response()->json([
                                    'success' => true,
                                    'message' => 'Role created successfully'
                                    ], 201);

        }else{

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong'
                                    ], 422);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param string $request
     * @return response object
     */
    public function restore(string $request)
    {
        $role = Role::withTrashed(true)->whereName($request)->first();

        if ($role) {

            $role->restore();

            return response()->json([
                                  'success' => true,
                                  'message' => 'Role restored successfully'
                                    ], 200);
        } else {

            return response()->json([
                                  'success' => false,
                                  'message' => 'Role not found'
                                    ], 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::find($id);

        if ($role) {

            return response()->json([
                                    'data' => $role,
                                    'success' => true,
                                    'message' => ''
                                    ], 200);
        } else {

            return response()->json([
                                    'data' => [],
                                    'success' => false,
                                    'message' => 'Role not found'
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
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'max:100', 'string', Rule::unique('roles','name')->ignore($id, 'id')],
            'role_description' => 'required|string',
            'is_active' => 'required|boolean',
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        $role = Role::find($id);

        if($role){

            $result = $role->update([
                                    'name' => $request->name,
                                    'is_active' => $request->is_active
                                    ]);

            if ($result) {

                return response()->json([
                                        'success' => true,
                                        'message' => 'Role Updated successfully'
                                        ], 201);
            } else {

                return response()->json([
                                        'success' => false,
                                        'message' => 'Something went wrong'
                                        ], 500);
            }
        }else{

            return response()->json([
                                    'success' => false,
                                    'message' => 'Role not found'
                                    ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::find($id);

        if ($role) {

            $role->update(['is_active' => false]);
            $role->delete();

            return response()->json([
                                    'success' => true,
                                    'message' => 'Role deleted successfully'
                                    ], 202);

        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Role not found'
                                    ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return Response
     */
    public function deleteSelectedRole(Request $request){

        $role_Ids = explode(',', $request->input('role_ids'));

        if(!empty($role_Ids)){

            if (Role::whereIn('id', $role_Ids)->exists()) {

                Role::whereIn('id', $role_Ids)->delete();

                return response()->json([
                                        'success' => true,
                                        'message' => "All Selected role deleted successfully",
                                        ],200);
            } else {

                return response()->json([
                                        'success' => false,
                                        'message' => "Selected role not found",
                                        ],404);
            }

        }else {

            return response()->json([
                                    'success' => false,
                                    'message' => "No role selected",
                                    ],404);

        }
    }
}
