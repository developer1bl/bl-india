<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notice;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Exceptions\UserExistPreviouslyException;

class NoticeController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @return Response
     */
    public function index()
    {
        $notice = Notice::with('services', 'image', 'documents')->orderByDesc('notice_id')->get();

        return response()->json([
                                'data' => $notice ?? [],
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
            'notice_title' => 'required|string|max:255',
            'notice_slug' => ['required', 'string', 'max:255', Rule::unique('notices', 'notice_slug')->whereNull('deleted_at')],
            'notice_content' => 'nullable|string',
            'service_id' => 'integer|exists:services,service_id',
            'notice_img_alt' => 'nullable|string',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'seo_keywords' => 'nullable|string',
            'notice_status' => 'boolean',
            'seo_other_details' => 'nullable|string',
            'notice_image_id' => 'integer|exists:media,media_id',
            'notice_document_id' => 'integer|exists:documents,document_id',
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        if (Notice::withTrashed()->whereNotice_slug($request->notice_slug)->exists()) {
            
            throw new UserExistPreviouslyException('this Notice was deleted previously, did you want to restore it?');
        }
        
        $product_tags = explode(',', $request->products_tag);

        $data = [
            "notice_title" => $request->notice_title,
            "notice_slug" => $request->notice_slug,
            "notice_content" => $request->notice_content,
            "service_id" => $request->service_id,
            "notice_image_id" => $request->notice_image_id,
            "notice_img_alt" => $request->notice_img_alt,
            "seo_title" => $request->seo_title,
            "seo_description" => $request->seo_description,
            "seo_keywords" => $request->seo_keywords,
            "notice_document_id" => $request->notice_document_id,
            "products_tag" => json_encode($product_tags),
            "seo_other_details" => $request->seo_other_details,
        ];

        $notice = Notice::create($data);
     
        if ($notice) {
            
            return response()->json([
                                    'success' => true,
                                    'message' => 'Notice created successfully'
                                    ], 202);
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
        $notice = Notice::withTrashed(true)->whereNotice_slug($request)->first();

        if ($notice) {

            $result = $notice->restore();

            if ($result) {
                
                return response()->json([
                                        'success' => true,
                                        'message' => 'Notice restored successfully'
                                        ], 202);
            } else {
                
                return response()->json([
                                        'success' => false,
                                        'message' => 'Something went wrong, please try again later'
                                        ], 422);
            }

        } else {
            
            return response()->json([
                                   'success' => false,
                                   'message' => 'Notice not found'
                                    ], 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $notice = Notice::find($id);

        if ($notice) {
            
            return response()->json([
                                    'data' => $notice,
                                    'success' => true,
                                    'message' => ''
                                    ], 200);
        } else {

            return response()->json([
                                    'data' => [],
                                    'success' => false,
                                    'message' => 'Service not found'
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
        // Find the notice
        $notice = Notice::find($id);

        if (!$notice) {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Notice not found'
                                    ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'notice_title' => 'required|string|max:255',
            'notice_slug' => ['required', 'string', 'max:255', Rule::unique('notices', 'notice_slug')->ignore($id, 'notice_id')],
            'notice_content' => 'nullable|string',
            'service_id' => 'integer|exists:services,service_id',
            'notice_image_id' => 'integer|exists:media,media_id',
            'notice_img_alt' => 'nullable|string',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'seo_keywords' => 'nullable|string',
            'notice_document_id' => 'integer|exists:documents,document_id',
            'notice_status' => 'boolean',
            'seo_other_details' => 'nullable|string',
        ]);


        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }
  
        $result = $notice->update($request->all());

        if ($result) {
            
            return response()->json([
                                    'success' => true,
                                    'message' => 'Notice updated successfully'
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
     * @param int $id
     * @return response
     */
    public function destroy(int $id)
    {
        $notice = Notice::find($id);

        if ($notice) {
            
            $notice->delete();

            return response()->json([
                                    'success' => true,
                                    'message' => 'Notice deleted successfully'
                                    ], 202);
        } else {
            
            return response()->json([
                                    'success' => false,
                                    'message' => 'Notice not found'
                                    ], 404);
        }
    }
}
