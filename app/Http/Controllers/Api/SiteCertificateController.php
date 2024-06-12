<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SiteCertificateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $siteCertificates = SiteCertificate::all();

        return response()->json([
            'data' => $siteCertificates ?? [],
            'success' => true
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // No implementation needed for API controller
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_certificate_name' => 'required|string|max:255',
            'site_certificate_slug' => 'required|string|max:255|unique:site_certificates',
            'site_certificate_url' => 'required|url',
            'site_certificate_status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()
            ], 403);
        }

        $siteCertificate = SiteCertificate::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $siteCertificate,
            'message' => 'Site Certificate created successfully'
        ], 201);
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
                'data' => $siteCertificate,
                'success' => true,
                'message' => '',
            ], 200);
        } else {
            return response()->json([
                'data' => [],
                'success' => false,
                'message' => 'Site Certificate not found',
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
     * @param Request $request
     * @param string $id
     * @return Response
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'site_certificate_name' => 'required|string|max:255',
            'site_certificate_slug' => 'required|string|max:255|unique:site_certificates,site_certificate_slug,' . $id,
            'site_certificate_url' => 'required|url',
            'site_certificate_status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()
            ], 403);
        }

        $siteCertificate = SiteCertificate::find($id);

        if ($siteCertificate) {
            $siteCertificate->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Site Certificate updated successfully'
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Site Certificate not found'
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return Response
     */
    public function destroy(string $id)
    {
        $siteCertificate = SiteCertificate::find($id);

        if ($siteCertificate) {
            $siteCertificate->delete();

            return response()->json([
                'success' => true,
                'message' => 'Site Certificate deleted successfully'
            ], 202);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Site Certificate not found'
            ], 404);
        }
    }
}
