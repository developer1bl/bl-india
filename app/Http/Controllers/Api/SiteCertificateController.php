<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Helpers\MediaHelper;

class SiteCertificateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $siteCertificates = SiteCertificate::all();

        return response()->json([
                                'data' => $siteCertificates ?? [],
                                'success' => true
                                ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'site_certificate_name' => ['required', 'string', 'max:255'],
            'site_certificate_slug' => ['required', 'string', 'max:255', Rule::unique('site_certificates', 'site_certificate_slug')->whereNull('deleted_at')],
            'site_certificate_img_id' => ['required', 'integer', 'exists:media,media_id'],
        ]);

        // Check for validation failure
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->errors()
                                    ], 403);
        }

        $siteCertificateImagePath = MediaHelper::getMediaPath($request->site_certificate_img_id);

        $data = [
            'site_certificate_name' => $request->site_certificate_name,
            'site_certificate_slug' => $request->site_certificate_slug,
            'site_certificate_url' => $siteCertificateImagePath,
            'site_certificate_status' => true,
        ];

        $siteCertificate = SiteCertificate::create($data);

        if ($siteCertificate) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Site Certificate Created Successfully',
                                    ], 200);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong'
                                    ], 403);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param string $name
     * @return Response
     */
    public function restore(string $name)
    {
        $siteCertificate = SiteCertificate::withTrashed()->where('site_certificate_name', $name)->first();

        if ($siteCertificate) {
            $siteCertificate->restore();

            return response()->json([
                                    'success' => true,
                                    'message' => 'Site Certificate Restored Successfully',
                                    ], 200);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong'
                                    ], 403);
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
        $siteCertificate = SiteCertificate::find($id);

        if ($siteCertificate) {

            return response()->json([
                                    'success' => true,
                                    'data' => $siteCertificate,
                                    'message' => ''
                                    ], 200);
        } else {

            return response()->json([
                                    'success' => false,
                                    'data' => [],
                                    'message' => 'Site Certificate not found.'
                                    ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // No implementation needed for API controller
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $id)
    {
        $siteCertificate = SiteCertificate::find($id);

        if (!$siteCertificate) {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Site Certificate not found.'
                                    ], 404);
        }

        $validator = Validator::make($request->all(),[
            'site_certificate_name' => ['required', 'string', 'max:255'],
            'site_certificate_slug' => ['required', 'string', 'max:255', Rule::unique('site_certificates', 'site_certificate_slug')
                                                                                ->ignore($id, 'id')
                                                                                ->whereNull('deleted_at')],
            'site_certificate_img_id' => ['required', 'integer', 'exists:media,media_id'],
            'site_certificate_status' => ['required', 'boolean'],
        ]);

        // Check for validation failure
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->errors()
                                    ], 403);
        }

        $siteCertificateImagePath = MediaHelper::getMediaPath($request->site_certificate_img_id);

        $data = [
            'site_certificate_name' => $request->site_certificate_name,
            'site_certificate_slug' => $request->site_certificate_slug,
            'site_certificate_url' => $siteCertificateImagePath,
            'site_certificate_status' => $request->site_certificate_status,
        ];

        $siteCertificate->update($data);

        return response()->json([
                                'success' => true,
                                'message' => 'Site Certificate Updated Successfully',
                                ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id)
    {
        $siteCertificate = SiteCertificate::find($id);

        if (!$siteCertificate) {
            return response()->json([
                                    'success' => false,
                                    'message' => 'Site Certificate not found.'
                                    ], 404);
        }

        $siteCertificate->delete();

        return response()->json([
                                'success' => true,
                                'message' => 'Site Certificate Deleted Successfully',
                                ], 200);
    }
}
