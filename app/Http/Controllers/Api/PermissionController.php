<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permission;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Exceptions\UserExistPreviouslyException;;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permission = Permission::with('roles')->get();

        return response()->json([
            'data' => $permission ?? [],
            'success' => true,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions', 'name')->whereNull('deleted_at')],
            'permissions_description' => 'nullable|string|max:255',
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => $validator->messages()
            ], 403);
        }

        if (Permission::withTrashed()
            ->whereName($request->name)
            ->exists()
        ) {
            throw new UserExistPreviouslyException('Oops! It appears that the chosen Permission Name is already in use. Please select a different one and try again');
        }


        $result = Permission::create(['name' => $request->name, 'permissions_description' => $request->permissions_description]);

        if ($result) {

            return response()->json([
                'success' => true,
                'message' => 'Permission created successfully'
            ], 201);
        } else {

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param string $request
     * @return response
     */
    public function restore(string $request)
    {
        $permission = Permission::withTrashed(true)->whereName($request)->first();

        if ($permission) {

            $permission->restore();

            return response()->json([
                'success' => true,
                'message' => 'Permission restored successfully'
            ], 200);
        } else {

            return response()->json([
                'success' => false,
                'message' => 'Permission not found'
            ], 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $permission = Permission::find($id);

        if ($permission) {

            return response()->json([
                'data' => $permission,
                'success' => true,
                'message' => 'Permission retrieved successfully'
            ], 200);
        } else {

            return response()->json([
                'data' => [],
                'success' => false,
                'message' => 'Permission not found'
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
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions', 'name')->ignore($id)->whereNull('deleted_at')],
            'permissions_description' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => $validator->messages()
            ], 403);
        }

        $permission = Permission::find($id);

        if ($permission) {

            $result = $permission->update($request->all());

            if ($result) {

                return response()->json([
                    'success' => true,
                    'message' => 'Permission Updated successfully'
                ], 201);
            } else {

                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong'
                ], 500);
            }
        } else {

            return response()->json([
                'success' => false,
                'message' => 'Permission not found'
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $permission = Permission::find($id);

        if ($permission) {

            $permission->update(['is_active' => false]);
            $permission->delete();

            return response()->json([
                'success' => true,
                'message' => 'Permission deleted successfully'
            ], 200);
        } else {

            return response()->json([
                'success' => false,
                'message' => 'Permission not found'
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return Response
     *
     **/
    public function deleteSelectedPermission(Request $request)
    {
        $permission_ids = explode(',', $request->input('permission_ids'));

        if (!empty($permission_ids)) {

            if (Permission::whereIn('id', $permission_ids)->exists()) {

                Permission::whereIn('id', $permission_ids)->delete();

                return response()->json([
                    'success' => true,
                    'message' => "All Selected permission deleted successfully",
                ], 200);
            } else {

                return response()->json([
                    'success' => false,
                    'message' => "Selected permission not found",
                ], 404);
            }
        } else {

            return response()->json([
                'success' => false,
                'message' => "No permission selected",
            ], 404);
        }
    }
}
