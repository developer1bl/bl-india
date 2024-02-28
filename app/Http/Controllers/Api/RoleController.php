<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::all();

        return response()->json([
                                'roles' => $roles ?? [],
                                'success' => true
                                ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:roles,name',
            'role_description' => 'required|string',
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => $validator->messages()
            ], 403);
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
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
}
