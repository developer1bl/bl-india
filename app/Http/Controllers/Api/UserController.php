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
     * 
     * @return Response
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
     * 
     * @param string $request
     * @return Response
     */
    public function restore(string $request)
    {
        $user = User::withTrashed(true)->whereName($request)->first();

        if ($user) {
            
            $user->restore();

            return response()->json([
                                    'success' => true,
                                    'message' => 'user restored successfully'
                                    ],200);
        }else{

            return response()->json([
                                   'success' => false,
                                   'message' => 'user not found'
                                    ],404);
        }
    }

    /**
     * Display the specified resource.
     * 
     *  @param integer $id
     *  @return Response
     */
    public function show(string $id)
    {
        $user = User::with('roles')->find($id);

        if ($user) {
            
            return response()->json([
                                    'data'=> $user,
                                    'sucess' => true,
                                    'message' => ''
                                    ],200);
        } else {
            
            return response()->json([
                                    'data'=> [],
                                    'sucess' => false,
                                    'message' => 'User not found'
                                    ],404);
        }
    }

    /**
     * update the specified resource
     *  
     * @param integer $id
     * @param Request $request
     * @return Response
     */
    public function updateUserSelf(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            'phone' => 'required|string|max:20|unique:users,email,'.$id,
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => $validator->messages()
            ], 403);
        }

        $user = User::find($id);
    
        if (!empty($user)) {
           
            $result = $user->update($request->all());

             if ($result) {
                
                return response()->json([
                                        'success' => true,
                                        'message' => 'User updated successfully'
                                        ], 202);

             } else {
                
                return response()->json([
                                        'success' => false,
                                         'message' => 'something went wrong'
                                        ], 403);
             }
        
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'User not found'
                                    ], 404);
        }
        
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param integer $id
     * @param Request $request
     * @return Response
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
     * 
     *  @param int $id
     *  @return response
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if(!empty($user)) {

            $user->tokens()->delete();

            $result = $user->delete();

            if ($result) {

                return response()->json([
                                        'success' => true,
                                        'message' => 'User Deleted successfully'
                                        ], 202);
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
}
