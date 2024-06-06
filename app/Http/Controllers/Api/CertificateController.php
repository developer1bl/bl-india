<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Helpers\MediaHelper;

class CertificateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $certificates = Certificate::orderByDesc('certificate_id')->get();

        return response()->json([
            'data' => $certificates ?? [],
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
            'certificates_name' => ['required', 'string', 'max:255'],
            'certificates_slug' => ['required', 'string', 'max:255', Rule::unique('certificates', 'certificates_slug')->whereNull('deleted_at')],
            'certificates_img_id' => ['required', 'integer', 'exists:media,media_id'],
        ]);

        // Check for validation failure
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 403);
        }

        $certificateImagePath = MediaHelper::getMediaPath($request->certificates_img_id ?? null);

        $data = [
            'certificates_name' => $request->certificates_name,
            'certificates_slug' => $request->certificates_slug,
            'certificates_img_url' => $certificateImagePath,
        ];

        $certificate = Certificate::create($data);

        if ($certificate) {
            return response()->json([
                'success' => true,
                'message' => 'Certificate Created Successfully',
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
        $certificate = Certificate::withTrashed()->where('certificates_name', $name)->first();

        if ($certificate) {
            $certificate->restore();

            return response()->json([
                'success' => true,
                'message' => 'Certificate Restored Successfully',
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
        $certificate = Certificate::find($id);

        if ($certificate) {
            return response()->json([
                                    'success' => true,
                                    'data' => $certificate,
                                    'message' => ''
                                    ], 200);
        } else {
            return response()->json([
                                    'success' => false,
                                    'data' => [],
                                    'message' => 'Certificate not found.'
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
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $id)
    {
        $certificate = Certificate::find($id);

        if (!$certificate) {
            return response()->json([
                'success' => false,
                'message' => 'Certificate not found.'
            ], 404);
        }

        $validator = Validator::make($request->all(),[
            'certificates_name' => ['required', 'string', 'max:255'],
            'certificates_slug' => ['required', 'string', 'max:255', Rule::unique('certificates', 'certificates_slug')
                ->ignore($id, 'certificate_id')
                ->whereNull('deleted_at')],
            'certificates_img_id' => ['required', 'integer', 'exists:media,media_id'],
        ]);

        // Check for validation failure
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 403);
        }

        $certificateImagePath = MediaHelper::getMediaPath($request->certificates_img_id ?? null);

        $data = [
            'certificates_name' => $request->certificates_name,
            'certificates_slug' => $request->certificates_slug,
            'certificates_img_url' => $certificateImagePath,
        ];

        $certificate->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Certificate Updated Successfully',
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
        $certificate = Certificate::find($id);

        if (!$certificate) {
            return response()->json([
                'success' => false,
                'message' => 'Certificate not found.'
            ], 404);
        }

        $certificate->delete();

        return response()->json([
            'success' => true,
            'message' => 'Certificate Deleted Successfully',
        ], 200);
    }
}
