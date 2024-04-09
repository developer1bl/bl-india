<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkFlow;
use Illuminate\Support\Facades\Validator;
use App\Helpers\MediaHelper;

class WorkFlowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $workFlow = WorkFlow::orderByDesc('id')->get();

        return response()->json([
                                'data' => $workFlow ?? [],
                                'success' => true
                                ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create(Request $request)
    {
        $validator  = Validator::make($request->all(),[
            'name' => 'required|string',
            'description' => 'required|string',
            'step_image_id' => 'required|exists:media,media_id',
            'step_img_alt' => 'nullable|string',
            'flow_order' => 'nullable|integer',
            'flow_status' => 'nullable|boolean',
        ]);

         //if the request have some validation errors
         if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        // Check if the maximum number of workflow steps has been reached
        if (WorkFlow::count() >= 5) {

            return response()->json([
                                    'success' =>false,
                                    'message' => 'Maximum number of workflow steps reached.'
                                    ], 400);
        }

        $workflowImagePath = MediaHelper::getMediaPath($request->step_image_id ?? null);

        $data = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'step_img_url' => $workflowImagePath,
            'step_img_alt' => $request->input('step_img_alt'),
            'flow_order' => $request->input('flow_order') ?? 0,
            'flow_status' => true,
        ];

        $workFlow = WorkFlow::create($data);

        if ($workFlow) {

            return response()->json([
                                    'data' => $workFlow,
                                    'success' => true,
                                    'message' => 'Work Flow Step created successfully'
                                    ], 201);
        } else {

            return response()->json([
                                    'data' => [],
                                    'success' => false,
                                    'message' => 'Something went wrong'
                                    ], 422);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param string $name
     */
    public function restore(string $name)
    {
        $workFlow = WorkFlow::withTrashed()->where('name', $name)->first();

        if ($workFlow) {

            // Check if the maximum number of workflow steps has been reached
            if (WorkFlow::count() >= 5) {

                return response()->json([
                                        'success' =>false,
                                        'message' => 'Maximum number of workflow steps reached.'
                                        ], 400);
            }

            $workFlow->restore();
            return response()->json([
                                    'data' => $workFlow,
                                    'success' => true,
                                    'message' => 'work flow steps restored successfully'
                                    ], 200);
        } else {

            return response()->json([
                                    'data' => [],
                                    'success' => false,
                                    'message' => 'Something went wrong'
                                    ], 403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $workFlow = WorkFlow::find($id);

        if ($workFlow) {

            return response()->json([
                                    'data' => $workFlow,
                                    'success' => true
                                    ], 200);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'WorkFlow not found'
                                    ], 403);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $workFlow = WorkFlow::find($id);

        if (!$workFlow) {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Work Flow Step not found'
                                    ], 403);
        }

        $validator  = Validator::make($request->all(),[
            'name' => 'required|string',
            'description' => 'required|string',
            'step_image_id' => 'nullable|string',
            'step_img_alt' => 'nullable|string',
            'flow_order' => 'nullable|integer',
            'flow_status' => 'nullable|boolean',
        ]);

         //if the request have some validation errors
         if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        $data = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'step_img_url' => $request->input('step_image'),
            'step_img_alt' => $request->input('step_img_alt'),
            'flow_order' => $request->input('flow_order') ?? 0,
            'flow_status' => $request->input('flow_status'),
        ];

        $workFlow = $workFlow->update($data);

        if ($workFlow) {

            return response()->json([
                                    'data' => 'Workflow updated successfully',
                                    'success' => true
                                    ], 200);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong'
                                    ], 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $workFlow = WorkFlow::find($id);

        if ($workFlow) {

            $workFlow->delete();

            return response()->json([
                                    'data' => 'work flow steps deleted successfully',
                                    'success' => true
                                    ], 200);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Work Flow Step not found'
                                    ], 403);
        }
    }
}
