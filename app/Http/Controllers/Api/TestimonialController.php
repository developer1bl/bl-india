<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Exceptions\UserExistPreviouslyException;

class TestimonialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $testimonials = Testimonial::orderByDesc('testimonial_id')->get();

        return response()->json([
                                'data' => $testimonials ?? [],
                                'success' => true
                                ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'testimonial_name' => 'required|string|max:255',
            'testimonial_slug' => ['required' ,'string', 'max:255', Rule::unique('testimonials', 'testimonial_slug')->whereNull('deleted_at')],
            'testimonial_designation' => 'nullable|string',
            'testimonial_company' => 'nullable|string',
            'testimonial_content' => 'nullable|string',
            'testimonial_rating' => 'nullable|numeric|max:5',
        ]);

        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 400);
        }

        if (Testimonial::withTrashed(true)
                          ->whereTestimonial_slug($request->testimonial_slug)
                          ->exists())
        {
            throw new UserExistPreviouslyException('Oops! It appears that the chosen testimonial slug is already in use. Please select a different one and try again.');
        }

        $data = [
            'testimonial_name' => $request->testimonial_name,
            'testimonial_slug' => $request->testimonial_slug,
            'testimonial_designation' => $request->testimonial_designation,
            'testimonial_company' => $request->testimonial_company,
            'testimonial_content' => $request->testimonial_content,
            'testimonial_rating' => $request->testimonial_rating,
        ];

        $testimonial = Testimonial::create($data);

        if ($testimonial) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Testimonial created successfully'
                                    ], 200);
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
     * @param string $name
     * @return Response
     */
    public function restore(string $name)
    {
        $testimonial = Testimonial::withTrashed()->whereTestimonial_slug($name)->first();

        if ($testimonial) {

            $testimonial->restore();

            return response()->json([
                                    'success' => true,
                                    'message' => 'Testimonial restored successfully'
                                    ], 200);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, please try again later'
                                    ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $testimonial = Testimonial::where('testimonial_id', $id)->first();

        if ($testimonial) {

            return response()->json([
                                    'data' => $testimonial,
                                    'success' => true,
                                    'message' => ''
                                    ], 200);
        } else {

            return response()->json([
                                    'data' => [],
                                    'success' => false,
                                    'message' => 'Testimonial not found'
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
        $testimonial = Testimonial::find($id);

        if (!$testimonial) {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Testimonial not found'
                                    ], 404);
        }

        $validator = Validator::make($request->all(), [
            'testimonial_name' => 'required|string|max:255',
            'testimonial_slug' => ['required' ,'string', 'max:255', Rule::unique('testimonials', 'testimonial_slug')
                                                                          ->ignore($id, 'testimonial_id')
                                                                          ->whereNull('deleted_at')],
            'testimonial_designation' => 'nullable|string',
            'testimonial_company' => 'nullable|string',
            'testimonial_content' => 'nullable|string',
            'testimonial_rating' => 'nullable|numeric|max:5',
        ]);

        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 400);
        }

        $data = [
            'testimonial_name' => $request->testimonial_name,
            'testimonial_slug' => $request->testimonial_slug,
            'testimonial_designation' => $request->testimonial_designation,
            'testimonial_company' => $request->testimonial_company,
            'testimonial_content' => $request->testimonial_content,
            'testimonial_rating' => $request->testimonial_rating,
        ];

        $testimonial = $testimonial->update($data);

        if ($testimonial) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Testimonial updated successfully'
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
     * @param   string $id
     * @return Response
     */
    public function destroy(string $id)
    {
        $testimonial = Testimonial::find($id);

        if (!$testimonial) {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Testimonial not found'
                                    ], 404);
        }

        $testimonial = $testimonial->delete();

        if ($testimonial) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Testimonial deleted successfully'
                                    ], 202);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, please try again later'
                                    ], 422);
        }
    }
}
