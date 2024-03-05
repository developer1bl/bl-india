<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $client = Client::all();

        return response()->json([
                                'data' => $client?? [],
                                'success' => true
                                ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
        $client = Client::where('id', auth()->user()->id)->first();

        if ($client) {

            return response()->json([
                                    'data' => $client,
                                    'sucess' => true,
                                    'message' => '',
                                    ],200);
        } else {
            
            return response()->json([
                                    'data' => [],
                                    'sucess' => false,
                                    'message' => 'Client not found',
                                    ],404);
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
     * @param integer $id
     * @param Request $request
     * @return Response
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email,'.$id,
            'phone' => 'required|string|max:20|unique:clients,email,'.$id,
            'password' => 'required'
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        $client = Client::find($id);

        if ($client) {
            
            $result = $client->update($request->all());

            if ($result) {
                 
                return response()->json([
                                        'success' => true,
                                        'message' => 'Client Updated successfully'
                                         ], 201);
            } else {
                
                return response()->json([
                                        'success' => false,
                                        'message' => 'Client not updated'
                                         ], 500);
            }
            
        } else {
            
            return response()->json([
                                    'success' => false,
                                    'message' => 'Client not found'
                                    ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param int $id
     * @return response
     */
    public function destroy(string $id)
    {
        $client = Client::find($id);

        if(!empty($client)) {

            $client->tokens()->delete();

            $result = $client->delete();

            if ($result) {

                return response()->json([
                                        'success' => true,
                                        'message' => 'Client Deleted successfully'
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
                                   'message' => 'Client not found'
                                    ], 404);
        }
    }
}
