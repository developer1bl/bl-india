<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Download;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Exceptions\UserExistPreviouslyException;

class DownloadController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $download = Download::with('downloadCategories')->get();

        return response()->json([
                                'data' => $download ?? [],
                                'success' => true,
                                ], 200);
        
    }

    /**
     * Show the form for creating a new resource.
     * 
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'download_name' => ['required', 'string', Rule::unique('downloads', 'download_name')->whereNull('deleted_at')],
            'download_slug' => ['required', 'string', Rule::unique('downloads', 'download_slug')->whereNull('deleted_at')],
            'download_documents' =>  'required',
            'download_status' => 'required|string',
            'download_category_id' => 'required|exists:download_categories,download_category_id',
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        if (Download::withTrashed()->whereDownload_name($request->download_name)->exists()) {
            
            throw new UserExistPreviouslyException('this Download was deleted previously, did you want to restore it?');
        }

        $download_doc = json_encode($request->download_documents);
        
        $download = [
            'download_name' => $request->download_name,
            'download_slug' => $request->download_slug,
            'download_documnets' => $download_doc,
            'download_status' => $request->download_status,
            'download_category_id' => $request->download_category_id,
        ];
        
        $result = Download::create($download);

        if ($result) {
            
            return response()->json([
                                    'success' => true,
                                    'message' => 'Download created successfully'
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
     * @param string $request
     * @return response
     */
    public function restore(string $request)
    {
        $download = Download::withTrashed()->whereDownload_name($request)->first();

        if ($download) {
            
            $download->restore();

            return response()->json([
                                    'success' => true,
                                    'message' => 'Download restored successfully'
                                    ], 202);
        } else {
            
            return response()->json([
                                    'success' => false,
                                    'message' => 'Download not found'
                                    ], 404);
        }
    }

    /**
     * Display the specified resource.
     * 
     * @param string $id
     * @return Response
     */
    public function show(string $id)
    {
        $download = Download::find($id);

        if ($download) {
            
            return response()->json([
                                    'data' => $download,
                                    'success' => true,
                                    'message' => ''
                                    ], 200);
        } else {

            return response()->json([
                                    'data' => [],
                                    'success' => false,
                                    'message' => 'Download not found'
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
        $download = Download::find($id);

        if (!$download) {
            
            return response()->json([
                                    'success' => false,
                                    'message' => 'Download not found'
                                    ], 404);
        }

        $validator = Validator::make($request->all(), [
            'download_name' => ['required', 'string', Rule::unique('downloads', 'download_name')->ignore($id, 'download_id')],
            'download_slug' => ['required', 'string', Rule::unique('downloads', 'download_slug')->ignore($id, 'download_id')],
            'download_documents' =>  'required',
            'download_status' => 'required|string',
            'download_category_id' => 'required|exists:download_categories,download_category_id',
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        $download_document = json_encode($request->download_documents);
        
        $download_doc = [
            'download_name' => $request->download_name,
            'download_slug' => $request->download_slug,
            'download_documnets' => $download_document,
            'download_status' => $request->download_status,
            'download_category_id' => $request->download_category_id,
        ];

        $result = $download->update($download_doc);

        if ($result) {
            
            return response()->json([
                                    'success' => true,
                                    'message' => 'Download updated successfully'
                                    ], 202);
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
     * @return response
     */
    public function destroy(string $id)
    { 
        $download = Download::find($id);

        if (!$download) {
            
            return response()->json([
                                    'success' => false,
                                    'message' => 'Download not found'
                                    ], 404);
        }

        $result = $download->delete();

        if ($result) {
            
            return response()->json([
                                    'success' => true,
                                    'message' => 'Download deleted successfully'
                                    ], 202);
        } else {
            
            return response()->json([
                                   'success' => false,
                                   'message' => 'Something went wrong, please try again later'
                                    ], 422);
        }
    }
}
