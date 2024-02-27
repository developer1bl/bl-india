<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::with('roles')->get();

        return response()->json(['data'=> $user],200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
    
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            'phone' => 'required|string|max:20|unique:users,email,'.$id,
            'role_id' => 'nullable|exists:roles,id', // Assuming roles are stored in the 'roles' table
            'is_active' => 'required|boolean',
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => $validator->messages()
            ], 403);
        }

        $user = User::find($id);

        if(!empty($user)) {

            $result = $user->update($request->all());

            if ($result) {

                return response()->json([
                                      'success' => true,
                                      'message' => 'User Updated successfully'
                                        ], 201);
            }else{

                return response()->json([
                                        'success' => false,
                                        'message' => 'Something went wrong'
                                        ], 500);
            }

        }else{

            return response()->json([
                                    'success' => false,
                                    'message' => 'User not found'
                                    ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
