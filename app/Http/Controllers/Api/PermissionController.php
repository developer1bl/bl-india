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
        $permission = Permission::All();
        
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

        try {

            $result = Permission::create([ 'name' => $request->name, 'permissions_description' => $request->permissions_description]);

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
        } catch (\Exception $th) {
            
            throw new UserExistPreviouslyException('this permission was deleted previously, did you want to restore it?');
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
            'name' => ['required','string','max:255', Rule::unique('permissions', 'product_category_name')->ignore($id)],
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
            
        }else{

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
                                    ], 202);

        } else {
           
            return response()->json([
                                    'success' => false,
                                    'message' => 'Permission not found'
                                    ], 404);
        }
    }
}
